<?php
define('FILAMENT_APP',true); require __DIR__.'/core.php';
$token=csrf(); $message=''; $error=''; $s=settings();
if($_SERVER['REQUEST_METHOD']==='POST') {
    try {
        checkCsrf($token);
        $action=$_POST['action']??'save';
        if(!in_array($action,['save','test'],true)) throw new RuntimeException('Neznámá operace.',400);
        $next=['showWeight'=>isset($_POST['showWeight']),'showChangedAt'=>isset($_POST['showChangedAt']),'publicExport'=>isset($_POST['publicExport']),'backupEnabled'=>isset($_POST['backupEnabled']),
            'backupTo'=>trim((string)($_POST['backupTo']??'')),'backupFrom'=>trim((string)($_POST['backupFrom']??''))];
        if ($next['backupEnabled']||$action==='test'||$next['backupTo']!=='') {
            if(!filter_var($next['backupTo'],FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Zadej platnou adresu příjemce.',400);
        }
        if(!filter_var($next['backupFrom'],FILTER_VALIDATE_EMAIL)||strtolower(strtok($next['backupFrom'],'@'))!=='webmaster'||preg_match('/[\r\n]/',$next['backupFrom'].$next['backupTo'])) throw new RuntimeException('Odesílatel musí mít formát webmaster@tvoje-domena.cz.',400);
        locked(function() use($next){
            if(!is_string($_POST['revision']??null)||!hash_equals(revision(settings()),$_POST['revision'])) throw new RuntimeException('Nastavení se mezitím změnilo. Obnov stránku.',409);
            atomicStore(SETTINGS_FILE,$next);
        });
        $s=$next;
        $backupMessage=backup($action==='test');
        // PRG zabraňuje opakovanému odeslání e-mailu při obnovení stránky.
        header('Location: nastaveni.php?saved=1'); exit;
    }catch(Throwable $e){$error=$e->getMessage();}
}
$status=readStore(__DIR__.'/backup-status.php',[]);
if(isset($_GET['saved'])) $message='Nastavení bylo uloženo.';
?>
<!doctype html><html lang="cs"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Nastavení filamentů</title>
<script>try{document.documentElement.dataset.theme=localStorage.getItem('filament-theme')||(matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light')}catch(e){}</script>
<style>
:root{color-scheme:light;--bg:#f6f7fd;--card:white;--ink:#222a46;--muted:#65718c;--line:#e4e8f4;--purple:#6858df}:root[data-theme=dark]{color-scheme:dark;--bg:#111625;--card:#1b2234;--ink:#eef1ff;--muted:#b0bad1;--line:#36415b;--purple:#b2a6ff}*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font:16px/1.55 system-ui}main{max-width:780px;margin:24px auto;padding:0 16px}nav{display:flex;gap:10px;flex-wrap:wrap;align-items:center}a,button{display:inline-block;border:1px solid var(--line);background:var(--card);color:var(--ink);border-radius:12px;padding:11px 16px;font:inherit;text-decoration:none;cursor:pointer}button.primary{background:#6858df;color:white;border-color:transparent}section{background:var(--card);border:1px solid var(--line);border-radius:20px;padding:24px;margin:18px 0}h1{font-size:30px}h2{font-size:20px;margin:0 0 16px}label{display:block;margin:15px 0;font-weight:650}input[type=email]{width:100%;font:inherit;padding:12px;border:1px solid var(--line);border-radius:12px;background:var(--bg);color:var(--ink);margin-top:6px}input[type=checkbox]{width:20px;height:20px;accent-color:#6858df;vertical-align:middle;margin-right:8px}.hint{color:var(--muted);font-size:14px}.message{padding:16px;border:1px solid var(--line);border-radius:12px;overflow-wrap:anywhere}.error{color:#d24869}.actions{display:flex;gap:10px;flex-wrap:wrap}button:disabled{opacity:.6;cursor:wait}code{overflow-wrap:anywhere}li{margin:7px 0}
</style></head><body><main>
<nav><a href="sprava.php">← Správa</a><a href="id.php">Zápis g</a><a href="../index.php">Veřejný přehled</a><button id="theme" type="button">☾ / ☀</button></nav>
<h1>Nastavení filamentů</h1>
<?php if($message): ?><p class="message" role="status"><?=h($message)?></p><?php endif; ?>
<?php if($error): ?><p class="message error" role="alert"><?=h($error)?></p><?php endif; ?>
<form method="post" id="settingsForm">
<input type="hidden" name="csrf" value="<?=h($token)?>"><input type="hidden" name="revision" value="<?=h(revision($s))?>">
<section><h2>Veřejné zobrazení</h2>
<label><input type="checkbox" name="showWeight" <?= $s['showWeight']?'checked':'' ?>>Zobrazovat množství všech filamentů</label>
<p class="hint">Společný přepínač pro celý veřejný přehled. Když množství není aktuální, vypni jej: skryjí se množství všech filamentů i souhrny a graf hmotnosti. Uložená množství se nezmění a ve správě je stále můžeš upravovat. Opětovné zapnutí zobrazí množství všech filamentů.</p></section>
<section><h2>Datum změny a exporty</h2>
<label><input type="checkbox" name="showChangedAt" <?= $s['showChangedAt']?'checked':'' ?>>Zobrazovat datum poslední změny u všech filamentů</label>
<p class="hint">Společný přepínač pro běžný přehled i jeho exporty. Ve správě datum zůstává viditelné a historie se ukládá vždy.</p>
<label><input type="checkbox" name="publicExport" <?= $s['publicExport']?'checked':'' ?>>Povolit export přehledu do PDF a CSV v běžném režimu</label>
<p class="hint">Veřejný export obsahuje pouze veřejné filamenty a respektuje skrytí množství a data. Správce může exportovat vždy. Exportuje se právě vyfiltrovaný přehled.</p>
<div class="actions"><a href="historie.php">Historie změn a její export</a><button class="primary" type="submit" name="action" value="save">Uložit nastavení</button></div>
</section>
<section><h2>E-mailové zálohy</h2>
<label><input type="checkbox" name="backupEnabled" <?= $s['backupEnabled']?'checked':'' ?>>Automatická záloha po každé uložené změně</label>
<label>Adresa příjemce<input name="backupTo" type="email" maxlength="254" value="<?=h($s['backupTo'])?>" placeholder="tvuj@email.cz"></label>
<label>Odesílatel (webmaster@doména)<input name="backupFrom" type="email" maxlength="254" required value="<?=h($s['backupFrom'])?>"></label>
<p class="hint">Použij adresu webmaster na doméně povolené tvým hostingem. Záloha obsahuje všechny filamenty včetně skrytých množství, historii změn, nastavení a používané fotografie. Odesílá ji PHP mail() na hostingu. Programové soubory zůstávají v instalačním balíčku.</p>
<div class="actions"><button class="primary" type="submit" name="action" value="save">Uložit nastavení</button><button type="submit" name="action" value="test">Uložit a poslat test zálohy</button></div>
<p class="hint">Test odešle skutečnou zálohu na zadanou adresu i při vypnuté automatické záloze. Vypnutí a uložení přepínače zastaví další automatické odesílání.</p>
<?php if($status): ?><div class="message <?=empty($status['ok'])?'error':''?>"><b>Poslední pokus: <?=h(date('d.m.Y H:i:s',strtotime($status['time'])))?></b><br><?=h($status['message'])?></div><?php endif; ?>
</section></form>
<section><h2>Kontrola hostingu</h2><ul>
<li>PHP: <?=h(PHP_VERSION)?> (požadováno 7.4 nebo novější)</li>
<li>PDF – český font a zlib: <?=function_exists('gzcompress')&&is_file(__DIR__.'/files/DejaVuSans.ttf')&&is_file(__DIR__.'/pdf-font.php')?'dostupné':'CHYBÍ – nahraj font PDF nebo zapni zlib na hostingu'?></li>
<li>GD – komprese fotografií: <?=function_exists('imagecreatetruecolor')?'dostupné':'CHYBÍ – požádej hosting o zapnutí'?></li>
<li>ZIP – úplná záloha: <?=class_exists('ZipArchive')?'dostupné':'CHYBÍ – požádej hosting o zapnutí'?></li>
<li>mail() – předání e-mailu: <?=function_exists('mail')?'funkce dostupná, doručení ověř testem':'funkce není dostupná'?></li>
<li>Zápis do složky id: <?=is_writable(__DIR__)?'povolen':'NENÍ POVOLEN'?></li>
<li>Zápis do fotografií: <?=is_writable(__DIR__.'/files')?'povolen':'NENÍ POVOLEN'?></li>
<li>Limit nahrávaného souboru: <?=h(ini_get('upload_max_filesize'))?>; celý požadavek: <?=h(ini_get('post_max_size'))?></li>
</ul><p class="hint">Přístup do této složky chrání výhradně nastavení .htaccess na webhostingu. Ověř ho v anonymním okně: správa musí vyžadovat heslo, veřejný přehled nikoli.</p></section>
</main><script>
document.getElementById('theme').onclick=()=>{let t=document.documentElement.dataset.theme==='dark'?'light':'dark';document.documentElement.dataset.theme=t;try{localStorage.setItem('filament-theme',t)}catch(e){}};
document.getElementById('settingsForm').addEventListener('submit',e=>{const b=e.submitter;if(b){const h=document.createElement('input');h.type='hidden';h.name='action';h.value=b.value;e.target.append(h);}e.target.querySelectorAll('button').forEach(b=>{b.disabled=true;});});
</script></body></html>
