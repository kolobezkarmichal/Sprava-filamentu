<?php
// Tuto stránku chrání .htaccess složky id. PHP přihlášení zde není.
define('FILAMENT_APP',true);
require __DIR__.'/core.php';
$csrf=csrf();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $uploaded=null; $committed=false;
    try {
        checkCsrf($csrf);
        $action=$_POST['action']??'';
        if (!in_array($action,['save','delete'],true)) throw new RuntimeException('Neznámá operace.',400);
        $input=json_decode($_POST['item']??'',true);
        if (!is_array($input)) throw new RuntimeException('Neplatný záznam.',400);
        $id=filter_var($input['id']??0,FILTER_VALIDATE_INT,['options'=>['min_range'=>0]]);
        if ($id===false || ($action==='delete'&&$id===0)) throw new RuntimeException('Neplatné ID.',400);
        $validated=$action==='save'?validateRow($input):null;
        if($action==='save') $uploaded=uploadPhoto();
        $result=locked(function() use($action,$input,$id,$validated,$uploaded) {
            $items=rows(); $key=null;
            foreach($items as $k=>$r) if ((int)$r['id']===$id) $key=$k;
            if ($id!==0) {
                if ($key===null) throw new RuntimeException('Filament již neexistuje. Obnov stránku.',409);
                if (!is_string($input['_rev']??null)||!hash_equals(revision($items[$key]),$input['_rev'])) throw new RuntimeException('Tento filament mezitím někdo změnil (např. rychlým kódem). Obnov stránku a uprav aktuální záznam.',409);
            }
            $before=$key===null?null:$items[$key]; $row=null;
            if($action==='delete') { array_splice($items,$key,1); }
            else {
                if ($id===0) { $id=max(array_merge([0],array_map(function($r){return (int)$r['id'];},$items)))+1; $row=['id'=>$id]; }
                else $row=$items[$key];
                $row=array_replace($row,$validated); if($uploaded) $row['image']=$uploaded;
                if($key===null) array_unshift($items,$row); else $items[$key]=$row;
            }
            commitChange($items,$before,$row,'sprava',$input['dateMode']??'auto');
            return ['items'=>forView($items,true,settings()),'savedId'=>$id];
        });
        $committed=true;
        $result['backup']=backup(); $result['ok']=true; reply($result);
    } catch(Throwable $e) { if($uploaded&&!$committed) @unlink(__DIR__.'/files/'.$uploaded); fail($e); }
}
$isAdmin=true;
$settings=settings();
$items=forView(rows(),true,$settings);
require __DIR__.'/view.php';
