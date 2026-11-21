<?php
define('FILAMENT_APP',true);
require __DIR__.'/id/core.php';
require __DIR__.'/id/report.php';
if(!in_array($_SERVER['REQUEST_METHOD'],['GET','HEAD'],true)){http_response_code(405);exit;}
try { exportOverview(false); } catch(Throwable $e){http_response_code($e->getCode()===400?400:500);header('Content-Type: text/plain; charset=UTF-8');echo $e->getMessage();}
