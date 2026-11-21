<?php
define('FILAMENT_APP',true);
require __DIR__.'/id/core.php';
if ($_SERVER['REQUEST_METHOD']!=='GET' && $_SERVER['REQUEST_METHOD']!=='HEAD') { http_response_code(405); header('Allow: GET, HEAD'); exit('Veřejný přehled je pouze pro čtení.'); }
try {
    $settings=settings(); $raw=rows();
    if (isset($_GET['image'])) {
        $id=filter_var($_GET['image'],FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        foreach($raw as $r) if ($id && (int)$r['id']===$id && ($r['active']??true)!==false) {
            $path=imagePath($r); if(!$path) break;
            $mime=['jpg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp'][pathinfo($path,PATHINFO_EXTENSION)];
            header('Content-Type: '.$mime); header('Content-Length: '.filesize($path));
            if($_SERVER['REQUEST_METHOD']!=='HEAD') readfile($path); exit;
        }
        http_response_code(404); exit;
    }
    $isAdmin=false; $csrf=''; $items=forView($raw,false,$settings);
    require __DIR__.'/id/view.php';
} catch(Throwable $e) { http_response_code(500); echo 'Přehled nelze načíst. Zkontroluj datové soubory a oprávnění ve správě.'; }
