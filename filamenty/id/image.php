<?php
// Obrázky skrytých filamentů jsou dostupné jen přes zabezpečenou správu.
define('FILAMENT_APP',true); require __DIR__.'/core.php';
$id=filter_var($_GET['id']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
foreach(rows() as $r) if($id&&(int)$r['id']===$id) {
    $p=imagePath($r); if(!$p) break;
    header('Content-Type: '.['jpg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp'][pathinfo($p,PATHINFO_EXTENSION)]);
    header('Content-Length: '.filesize($p)); readfile($p); exit;
}
http_response_code(404);
