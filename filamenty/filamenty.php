<?php
// Zpětná kompatibilita starých odkazů; nepovoluje zápis.
if ($_SERVER['REQUEST_METHOD']!=='GET'&&$_SERVER['REQUEST_METHOD']!=='HEAD') { http_response_code(405); exit('Použij novou správu: id/sprava.php'); }
header('Cache-Control: no-store'); header('Location: index.php',true,302); exit;
