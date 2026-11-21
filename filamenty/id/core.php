<?php
// Sdílená logika. Přímé volání nepovoluje žádnou operaci.
if (!defined('FILAMENT_APP')) { http_response_code(404); exit; }
date_default_timezone_set('Europe/Prague');
const DATA_FILE = __DIR__ . '/filaments.php';
const SETTINGS_FILE = __DIR__ . '/settings.php';
const DATA_PREFIX = "<?php http_response_code(404); exit; ?>\n";
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
function defaults() { return ['showWeight'=>true,'showChangedAt'=>true,'publicExport'=>false,'backupEnabled'=>false,'backupTo'=>'','backupFrom'=>'webmaster@example.org']; }
function readStore($file, $fallback) {
    if (!is_file($file)) return $fallback;
    $raw = file_get_contents($file);
    if ($raw === false || substr($raw,0,strlen(DATA_PREFIX)) !== DATA_PREFIX) throw new RuntimeException('Datový soubor nelze přečíst. Nic nebylo přepsáno.');
    $data = json_decode(substr($raw,strlen(DATA_PREFIX)),true);
    if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) throw new RuntimeException('Poškozený datový soubor. Obnov jej ze zálohy.');
    return $data;
}
function atomicStore($file, $data) {
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    $tmp = dirname($file).'/.write_'.bin2hex(random_bytes(12)).'.php';
    $payload = DATA_PREFIX.$json."\n";
    if (file_put_contents($tmp,$payload,LOCK_EX) !== strlen($payload)) { @unlink($tmp); throw new RuntimeException('Zápis selhal: zkontroluj volné místo a oprávnění složky id.'); }
    if (!rename($tmp,$file)) { @unlink($tmp); throw new RuntimeException('Nelze dokončit uložení dat.'); }
}
function locked($fn) {
    $fp = fopen(__DIR__.'/lock.php','c');
    if (!$fp || !flock($fp,LOCK_EX)) throw new RuntimeException('Nelze zamknout data. Zkontroluj oprávnění složky id.');
    try { return $fn(); } finally { flock($fp,LOCK_UN); fclose($fp); }
}
function settings() { return array_replace(defaults(),readStore(SETTINGS_FILE,[])); }
// Data i historie tvoří jeden atomicky uložený celek. Starší seznam se převede až při zápisu.
function database() {
    $data=readStore(DATA_FILE,[]);
    if (array_values($data)===$data) return ['version'=>2,'filaments'=>$data,'history'=>[]];
    if (($data['version']??null)!==2 || !is_array($data['filaments']??null) || !is_array($data['history']??null)
        || array_values($data['filaments'])!==$data['filaments'] || array_values($data['history'])!==$data['history'])
        throw new RuntimeException('Neplatná databáze filamentů nebo historie.');
    return $data;
}
function rows() { return database()['filaments']; }
function changeDate($row) { return $row['changedAt']??$row['createdAt']??''; }
function dateCz($date) { return $date ? implode('.',array_reverse(explode('-',substr($date,0,10)))) : 'Neuvedeno'; }
function historySnapshot($row) {
    if ($row===null) return null;
    $out=[];
    foreach(['id','mat','color','vendor','weight','active','image','hexColor','displayMode'] as $key) $out[$key]=$row[$key]??null;
    $out['changedAt']=changeDate($row);
    return $out;
}
// Volat pouze pod společným zámkem, po ověření vstupu a revize.
function commitChange($items,$before,$after,$source,$dateMode='auto') {
    $db=database();
    $db['filaments']=$items;
    $db['history'][]=[
        'eventId'=>bin2hex(random_bytes(16)), 'recordedAt'=>date('c'),
        'filamentId'=>(int)($after['id']??$before['id']),
        'action'=>$before===null?'create':($after===null?'delete':'update'),
        'source'=>$source, 'dateMode'=>$dateMode,
        'before'=>historySnapshot($before), 'after'=>historySnapshot($after)
    ];
    atomicStore(DATA_FILE,$db);
}
function revision($row) { return hash('sha256',json_encode($row,JSON_UNESCAPED_UNICODE)); }
function h($s) { return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8'); }
function csrf() {
    if (session_status()!==PHP_SESSION_ACTIVE) { session_name('filament_forms'); session_start(['cookie_httponly'=>true,'cookie_samesite'=>'Strict','cookie_secure'=>!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off']); }
    if (empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32));
    $t=$_SESSION['csrf']; session_write_close(); return $t;
}
function checkCsrf($token) { if (!is_string($_POST['csrf']??null)||!hash_equals($token,$_POST['csrf'])) throw new RuntimeException('Platnost formuláře vypršela. Obnov stránku.',403); }
function reply($data,$code=200) { http_response_code($code); header('Content-Type: application/json; charset=utf-8'); echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_THROW_ON_ERROR); exit; }
function fail($e) { $c=(int)$e->getCode(); reply(['ok'=>false,'error'=>$e->getMessage()],in_array($c,[400,403,404,409,413])?$c:500); }
function imagePath($row) {
    $name=$row['image']??'';
    if (!is_string($name)||!preg_match('/\A[a-zA-Z0-9_-]+\.(jpg|webp|png)\z/',$name)) return null;
    $p=__DIR__.'/files/'.$name; return is_file($p)?$p:null;
}
function forView($items,$admin,$settings) {
    $out=[];
    foreach($items as $row) {
        if (!$admin && ($row['active']??true)===false) continue;
        $r=$row; $r['changedAt']=changeDate($row); unset($r['createdAt'], $r['showWeight'], $r['showWeightSetting']); $r['_rev']= $admin ? revision($row) : null;
        // Viditelnost množství řídí pouze společné nastavení; staré příznaky záznamů ignorujeme.
        if (!$admin && !($settings['showWeight']??true)) unset($r['weight']);
        if (!$admin && !($settings['showChangedAt']??true)) unset($r['changedAt']);
        $r['imageVersion']= $row['image']??'';
        unset($r['image']); $out[]=$r;
    }
    return $out;
}
function uploadPhoto() {
    $f=$_FILES['image']??null;
    if (!$f || $f['error']===UPLOAD_ERR_NO_FILE) return null;
    if ($f['error']!==UPLOAD_ERR_OK) throw new RuntimeException('Fotografii nelze nahrát. Limit serveru: '.ini_get('upload_max_filesize').'; limit formuláře: '.ini_get('post_max_size').'.',400);
    if (!is_uploaded_file($f['tmp_name']) || $f['size']>20*1024*1024) throw new RuntimeException('Fotografie může mít nejvýše 20 MB.',400);
    $info=@getimagesize($f['tmp_name']);
    if (!$info || !in_array($info['mime'],['image/jpeg','image/png','image/webp'],true)) throw new RuntimeException('Použij platný JPG, PNG nebo WebP.',400);
    if ($info[0]*$info[1]>32000000) throw new RuntimeException('Fotografie má více než 32 megapixelů. Použij menší snímek.',400);
    $limit=trim(ini_get('memory_limit')); $unit=strtolower(substr($limit,-1));
    $bytes=(int)$limit * (['g'=>1073741824,'m'=>1048576,'k'=>1024][$unit]??1);
    if ($bytes>0 && memory_get_usage(true)+$info[0]*$info[1]*8+16777216>$bytes) throw new RuntimeException('Snímek je příliš velký pro paměť hostingu. Použij menší rozlišení.',400);
    if (!function_exists('imagecreatetruecolor')) throw new RuntimeException('Pro kompresi fotografií musí hosting zapnout PHP rozšíření GD.');
    $open=['image/jpeg'=>'imagecreatefromjpeg','image/png'=>'imagecreatefrompng','image/webp'=>'imagecreatefromwebp'][$info['mime']];
    if (!function_exists($open)) throw new RuntimeException('Hosting tento formát fotografie nepodporuje.',400);
    $src=@$open($f['tmp_name']); if (!$src) throw new RuntimeException('Fotografii nelze zpracovat.',400);
    if ($info['mime']==='image/jpeg' && function_exists('exif_read_data')) {
        $exif=@exif_read_data($f['tmp_name']); $o=(int)($exif['Orientation']??1);
        if (in_array($o,[2,4,5,7],true)) imageflip($src,IMG_FLIP_HORIZONTAL);
        $angle=[3=>180,4=>180,5=>90,6=>-90,7=>-90,8=>90][$o]??0;
        if ($angle) { $rot=imagerotate($src,$angle,0); if($rot) { imagedestroy($src); $src=$rot; } }
    }
    $w=imagesx($src); $hh=imagesy($src); $scale=min(1,800/max($w,$hh));
    $dst=imagecreatetruecolor(max(1,(int)round($w*$scale)),max(1,(int)round($hh*$scale)));
    imagefill($dst,0,0,imagecolorallocate($dst,255,255,255));
    imagecopyresampled($dst,$src,0,0,0,0,imagesx($dst),imagesy($dst),$w,$hh);
    if (!is_dir(__DIR__.'/files')&&!mkdir(__DIR__.'/files',0755,true)) throw new RuntimeException('Nelze vytvořit složku obrázků.');
    $webp=function_exists('imagewebp'); $name='photo_'.bin2hex(random_bytes(16)).($webp?'.webp':'.jpg');
    $ok=$webp?imagewebp($dst,__DIR__.'/files/'.$name,80):imagejpeg($dst,__DIR__.'/files/'.$name,82);
    imagedestroy($src); imagedestroy($dst);
    if (!$ok) throw new RuntimeException('Nelze uložit komprimovanou fotografii.');
    return $name;
}
function validateRow($input) {
    if (!is_array($input)) throw new RuntimeException('Neplatná data formuláře.',400);
    $r=[];
    foreach(['mat'=>100,'color'=>300,'vendor'=>300] as $key=>$max) {
        if (!is_string($input[$key]??null)||trim($input[$key])===''||strlen($input[$key])>$max) throw new RuntimeException('Vyplň platný název, materiál a výrobce.',400);
        $r[$key]=trim($input[$key]);
    }
    if (!is_numeric($input['weight']??null)||$input['weight']<0||$input['weight']>1000000) throw new RuntimeException('Hmotnost musí být 0 až 1 000 000 g.',400);
    $r['weight']=0+$input['weight'];
    foreach(['active'] as $key) { if (!is_bool($input[$key]??null)) throw new RuntimeException('Neplatný přepínač.',400); $r[$key]=$input[$key]; }
    if (!in_array($input['dateMode']??'auto',['auto','manual'],true)) throw new RuntimeException('Neplatný režim data.',400);
    $date=($input['dateMode']??'auto')==='manual'?($input['changedAt']??''):date('Y-m-d');
    if ($date==='') throw new RuntimeException('Vyplň vlastní datum změny, nebo zvol automatické datum.',400);
    if (!is_string($date)) throw new RuntimeException('Neplatné datum.',400);
    if ($date!=='') { $d=DateTime::createFromFormat('!Y-m-d',$date); if(!$d||$d->format('Y-m-d')!==$date) throw new RuntimeException('Neplatné datum.',400); }
    // Volitelné HEX odstíny a způsob zobrazení zůstávají kompatibilní se starými záznamy.
    $hex=$input['hexColor']??'';
    if(!is_string($hex)) throw new RuntimeException('Neplatná HEX barva.',400);
    $hex=strtoupper(trim($hex));
    if($hex!=='' && !preg_match('/^#[0-9A-F]{6}$/D',$hex)) throw new RuntimeException('HEX barva musí mít tvar #AABBCC.',400);
    $mode=$input['displayMode']??'photo';
    if(!in_array($mode,['photo','hex'],true)) throw new RuntimeException('Neplatný způsob zobrazení.',400);
    if($mode==='hex' && $hex==='') throw new RuntimeException('Pro barevný vzorek nastav HEX odstín.',400);
    $r['hexColor']=$hex;
    $r['displayMode']=$mode;
    $r['changedAt']=$date; return $r;
}
function backup($force=false) {
    // Vytvoří konzistentní archiv dat a aktuálně používaných obrázků pod společným zámkem.
    $tmp=null;
    try {
        $snapshot=locked(function() use($force,&$tmp) {
            $s=settings(); if (!$force&&!$s['backupEnabled']) return null;
            if (!filter_var($s['backupTo'],FILTER_VALIDATE_EMAIL)||!filter_var($s['backupFrom'],FILTER_VALIDATE_EMAIL)||preg_match('/[\r\n]/',$s['backupTo'].$s['backupFrom'])) throw new RuntimeException('Nastav platnou adresu příjemce a odesílatele zálohy.');
            if (!class_exists('ZipArchive')) throw new RuntimeException('Hosting musí zapnout PHP rozšíření ZIP pro úplnou e-mailovou zálohu.');
            $tmp=tempnam(sys_get_temp_dir(),'filbackup_'); if($tmp===false) throw new RuntimeException('Nelze vytvořit dočasnou zálohu.');
            $z=new ZipArchive(); if($z->open($tmp,ZipArchive::OVERWRITE)!==true) throw new RuntimeException('Nelze vytvořit ZIP zálohu.');
            $r=rows();
            $z->addFromString('id/filaments.php',DATA_PREFIX.json_encode(database(),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)."\n");
            $z->addFromString('id/settings.php',DATA_PREFIX.json_encode($s,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)."\n");
            $names=[];
            foreach($r as $row) { $p=imagePath($row); if($p&&!isset($names[$p])) { if(!$z->addFile($p,'id/files/'.basename($p))) throw new RuntimeException('Nelze přidat obrázek do zálohy.'); $names[$p]=true; } }
            $z->addFromString('OBNOVA.txt',"Zaloha dat aplikace Filamenty. Obsahuje vsechny zaznamy, historii zmen, nastaveni a pouzivane fotografie.\nObnova: docasne zastav upravy a nahraj obsah slozky id do id fungujici aplikace stejne verze. Zachovej serverovy .htaccess. Programove soubory jsou v instalacnim balicku.\n");
            if(!$z->close()) throw new RuntimeException('Nelze dokončit archiv zálohy.');
            if(filesize($tmp)>18*1024*1024) throw new RuntimeException('Záloha přesahuje 18 MB. E-mail nebyl odeslán.');
            return $s;
        });
        if ($snapshot===null) return '';
        $attachment=file_get_contents($tmp); if($attachment===false) throw new RuntimeException('Zálohu nelze přečíst.');
        $boundary='fil_'.bin2hex(random_bytes(16));
        $subject='Filamenty - '.($force?'test zalohy - ':'zaloha - ').date('Y-m-d H:i:s');
        $body='--'.$boundary."\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n".chunk_split(base64_encode('Záloha filamentů ze dne '.date('d.m.Y H:i:s').'. V příloze jsou data, historii změn, nastavení a fotografie.'))."\r\n--".$boundary."\r\nContent-Type: application/zip; name=\"filamenty-".date('Y-m-d-His').".zip\"\r\nContent-Disposition: attachment; filename=\"filamenty-".date('Y-m-d-His').".zip\"\r\nContent-Transfer-Encoding: base64\r\n\r\n".chunk_split(base64_encode($attachment))."\r\n--".$boundary."--\r\n";
        $headers='From: Webmaster <'.$snapshot['backupFrom'].">\r\nMIME-Version: 1.0\r\nContent-Type: multipart/mixed; boundary=\"".$boundary.'"';
        if (!function_exists('mail')||!@mail($snapshot['backupTo'],$subject,$body,$headers)) throw new RuntimeException('Hosting nepřijal e-mail k odeslání. Zkontroluj podporu mail() a adresu odesílatele.');
        $message='Záloha předána poštovnímu serveru ('.$snapshot['backupTo'].'). Doručení ověř ve schránce i spamu.';
        locked(function() use($message){ atomicStore(__DIR__.'/backup-status.php',['time'=>date('c'),'ok'=>true,'message'=>$message]); });
        return $message;
    } catch(Throwable $e) {
        $message='Záloha se nezdařila: '.$e->getMessage();
        try { locked(function() use($message){ atomicStore(__DIR__.'/backup-status.php',['time'=>date('c'),'ok'=>false,'message'=>$message]); }); } catch(Throwable $ignored) {}
        return $message;
    } finally { if($tmp&&is_file($tmp)) unlink($tmp); }
}
