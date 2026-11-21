<?php
if (!defined('FILAMENT_APP')) { http_response_code(404); exit; }
function queryText($key,$default='') {
    $v=$_GET[$key]??$default;
    if (!is_string($v)||strlen($v)>500) throw new RuntimeException('Neplatný filtr.',400);
    return trim($v);
}
function normalizeSearch($s) {
    $s=strtr((string)$s,array_combine(
        preg_split('//u','ÁČĎÉĚÍŇÓŘŠŤÚŮÝŽáčďéěíňóřšťúůýž',-1,PREG_SPLIT_NO_EMPTY),
        str_split('ACDEEINORSTUUYZacdeeinorstuuyz')));
    return strtolower(trim($s));
}
function filterOverview($items) {
    $q=normalizeSearch(queryText('q')); $id=queryText('id'); $mat=queryText('mat','ALL'); $status=queryText('status','all'); $sort=queryText('sort','id-desc');
    $out=array_values(array_filter($items,function($r)use($q,$id,$mat,$status){
        $full=normalizeSearch(implode(' ',[$r['id'],$r['color'],$r['mat'],$r['vendor'],$r['weight']??'',$r['changedAt']??'']));
        foreach(preg_split('/\s+/u',$q,-1,PREG_SPLIT_NO_EMPTY) as $word) if(strpos($full,$word)===false) return false;
        return ($id===''||(string)$r['id']===$id) && ($mat==='ALL'||strtoupper(trim($r['mat']))===$mat)
            && ($status==='all'||($status==='low'&&isset($r['weight'])&&$r['weight']<=100)||($status==='active'&&($r['active']??true)!==false)||($status==='hidden'&&($r['active']??true)===false));
    }));
    usort($out,function($a,$b)use($sort){
        switch($sort){
            case 'id-asc':return $a['id']<=>$b['id'];
            case 'name-asc':case 'name-desc':$n=strnatcmp(normalizeSearch($a['color']),normalizeSearch($b['color'])); return ($sort==='name-desc'?-$n:$n)?:($a['id']<=>$b['id']);
            case 'weight-asc':case 'weight-desc':$n=($a['weight']??0)<=>($b['weight']??0); return ($sort==='weight-desc'?-$n:$n)?:($b['id']<=>$a['id']);
            case 'date-asc':case 'date-desc':
                $ad=$a['changedAt']??'';$bd=$b['changedAt']??'';
                if($ad!==''&&$bd==='')return -1;if($ad===''&&$bd!=='')return 1;
                $n=strcmp($ad,$bd);return ($sort==='date-desc'?-$n:$n)?:($sort==='date-desc'?($b['id']<=>$a['id']):($a['id']<=>$b['id']));
            default:return $b['id']<=>$a['id'];
        }
    });
    return $out;
}
function filterHistory($history) {
    $id=queryText('id');$from=queryText('from');$to=queryText('to');$q=normalizeSearch(queryText('q'));
    foreach([$from,$to] as $date) if($date!=='') { $d=DateTime::createFromFormat('!Y-m-d',$date); if(!$d||$d->format('Y-m-d')!==$date)throw new RuntimeException('Neplatné období.',400); }
    if($from!==''&&$to!==''&&$from>$to)throw new RuntimeException('Začátek období musí být před koncem.',400);
    return array_values(array_filter(array_reverse($history),function($e)use($id,$from,$to,$q){
        $day=substr($e['recordedAt'],0,10);
        $text=normalizeSearch(implode(' ',[$e['filamentId'],$e['before']['color']??'',$e['after']['color']??'',$e['before']['vendor']??'',$e['after']['vendor']??'',$e['before']['mat']??'',$e['after']['mat']??'']));
        foreach(preg_split('/\s+/u',$q,-1,PREG_SPLIT_NO_EMPTY) as $word) if(strpos($text,$word)===false)return false;
        return ($id===''||(string)$e['filamentId']===$id)&&($from===''||$day>=$from)&&($to===''||$day<=$to);
    }));
}
function historyValue($field,$value) {
    if($value===null||$value==='')return 'Neuvedeno';
    if($field==='active')return $value?'Veřejný':'Skrytý';
    if($field==='changedAt')return dateCz($value);
    return (string)$value.($field==='weight'?' g':'');
}
function historyDetails($e) {
    $fields=['color'=>'Název','mat'=>'Materiál','vendor'=>'Výrobce','weight'=>'Množství','changedAt'=>'Datum změny','active'=>'Viditelnost','image'=>'Fotografie','hexColor'=>'HEX barva','displayMode'=>'Zobrazení vzorku'];$out=[];
    foreach($fields as $key=>$label){
        $a=$e['before'][$key]??null;$b=$e['after'][$key]??null;
        if((string)$a===(string)$b && $e['before']!==null && $e['after']!==null)continue;
        if($a===null&&$b===null)continue;
        $out[]=$label.': '.historyValue($key,$a).' → '.historyValue($key,$b);
    }
    return $out?:['Opakované uložení beze změny hodnot.'];
}
function historyAction($e) { return ['create'=>'Přidání','update'=>'Úprava','delete'=>'Smazání'][$e['action']]??'Úprava'; }
function historySource($e) { return $e['source']==='code'?'Číselný kód':('Správa'.(($e['dateMode']??'auto')==='manual'?' – vlastní datum':'')); }
function exportCsv($headers,$rows,$filename) {
    header('Content-Type: text/csv; charset=UTF-8');header('Content-Disposition: attachment; filename="'.$filename.'.csv"');
    $out=fopen('php://output','wb'); fwrite($out,"\xEF\xBB\xBF");
    foreach(array_merge([$headers],$rows) as $row){
        $safe=array_map(function($v){ $v=(string)$v;return preg_match('/^[\s\x00-\x20]*[=+@-]/u',$v)?"'".$v:$v; },$row);
        fputcsv($out,$safe,';','"','');
    }
    fclose($out);exit;
}
function exportOverview($admin) {
    $s=settings();if(!$admin&&!$s['publicExport']){http_response_code(403);exit('Export přehledu je v běžném režimu vypnutý.');}
    $items=filterOverview(forView(rows(),$admin,$s));
    $format=queryText('format','csv');
    $headers=['ID','Název / barva','Materiál','Výrobce'];$widths=[35,210,75,150];
    if($admin||$s['showWeight']){$headers[]='Množství (g)';$widths[]=90;}
    if($admin||$s['showChangedAt']){$headers[]='Datum změny';$widths[]=95;}
    if($admin){$headers[]='Viditelnost';$widths[]=100;}
    // Doplňující strojová pole pouze v CSV, aby se nezhoršilo rozvržení PDF.
    if($format==='csv'){$headers[]='HEX barva';$headers[]='Zobrazení vzorku';}
    $table=[];
    foreach($items as $r){
        $line=[$r['id'],$r['color'],$r['mat'],$r['vendor']];
        if($admin||$s['showWeight'])$line[]=$r['weight']??'';
        if($admin||$s['showChangedAt'])$line[]=dateCz($r['changedAt']??'');
        if($admin)$line[]=($r['active']??true)?'Veřejný':'Skrytý';
        if($format==='csv'){$line[]=$r['hexColor']??'';$line[]=($r['displayMode']??'photo')==='hex'?'HEX':'Fotografie';}
        $table[]=$line;
    }
    if($format==='csv')exportCsv($headers,$table,'prehled-filamentu-'.date('Y-m-d'));
    if($format!=='pdf')throw new RuntimeException('Neplatný formát exportu.',400);
    require __DIR__.'/pdf.php';
    $filters=[];foreach(['q'=>'Hledání','id'=>'ID','mat'=>'Materiál','status'=>'Stav','sort'=>'Řazení'] as $key=>$label){$v=queryText($key);if($v!=='')$filters[]=$label.': '.$v;}
    exportPdf('Přehled filamentů',$headers,$table,$widths,implode(' · ',$filters),'prehled-filamentu-'.date('Y-m-d'));
}
function exportHistory($events,$format) {
    $headers=['Čas zápisu','ID','Filament','Operace / způsob','Změny'];$table=[];
    foreach($events as $e)$table[]=[date('d.m.Y H:i:s',strtotime($e['recordedAt'])),$e['filamentId'],$e['after']['color']??$e['before']['color']??'',historyAction($e).' / '.historySource($e),implode("\n",historyDetails($e))];
    if($format==='csv')exportCsv($headers,$table,'historie-filamentu-'.date('Y-m-d'));
    if($format!=='pdf')throw new RuntimeException('Neplatný formát exportu.',400);
    require __DIR__.'/pdf.php';
    $filter='ID: '.(queryText('id')?:'všechna').' · Čas zápisu od '.(queryText('from')?:'počátku').' do '.(queryText('to')?:'dnes').' · Hledání: '.(queryText('q')?:'vše');
    exportPdf('Historie změn filamentů',$headers,$table,[108,32,130,145,340],$filter,'historie-filamentu-'.date('Y-m-d'));
}
