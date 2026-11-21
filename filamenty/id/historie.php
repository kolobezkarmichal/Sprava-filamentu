<?php
// Tuto stránku i její exporty chrání stávající .htaccess složky id.
define('FILAMENT_APP',true);
require __DIR__.'/core.php';require __DIR__.'/report.php';
if(!in_array($_SERVER['REQUEST_METHOD'],['GET','HEAD'],true)){http_response_code(405);exit;}
try {
    $events=filterHistory(database()['history']);
    if(isset($_GET['format']))exportHistory($events,queryText('format'));
    $page=max(1,(int)queryText('page','1'));$total=count($events);$pages=max(1,(int)ceil($total/50));$page=min($page,$pages);$shown=array_slice($events,($page-1)*50,50);
    $filters=[];foreach(['id','q','from','to'] as $key)$filters[$key]=queryText($key);
} catch(Throwable $e){http_response_code($e->getCode()===400?400:500);exit(h($e->getMessage()));}
?>
<!doctype html><html lang="cs"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Historie změn filamentů</title>
<script>try{document.documentElement.dataset.theme=localStorage.getItem('filament-theme')||(matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light')}catch(e){}</script>
<style>
:root{color-scheme:light;--bg:#f6f7fd;--card:#fff;--ink:#222a46;--muted:#65718c;--line:#e4e8f4;--purple:#6858df}:root[data-theme=dark]{color-scheme:dark;--bg:#111625;--card:#1b2234;--ink:#eef1ff;--muted:#b0bad1;--line:#36415b;--purple:#b2a6ff}*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font:15px/1.55 system-ui}main{max-width:1120px;margin:24px auto;padding:0 16px}nav,.actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center}a,button{display:inline-block;border:1px solid var(--line);background:var(--card);color:var(--ink);border-radius:12px;padding:11px 16px;font:inherit;text-decoration:none;cursor:pointer}button.primary{background:#6858df;color:#fff}section,article{background:var(--card);border:1px solid var(--line);border-radius:18px;padding:22px;margin:16px 0}h1{font-size:30px}h2{font-size:18px;margin:0 0 8px;overflow-wrap:anywhere}label{display:block;font-weight:650}input{width:100%;min-width:0;font:inherit;padding:12px;border:1px solid var(--line);border-radius:12px;background:var(--bg);color:var(--ink);margin:6px 0 12px}.filters{display:grid;grid-template-columns:90px 2fr 1fr 1fr;gap:12px}.hint{color:var(--muted);font-size:14px}.tag{display:inline-block;padding:4px 10px;border-radius:8px;background:var(--bg);color:var(--purple);font-weight:700}.meta{display:flex;gap:12px;flex-wrap:wrap}ul{margin:12px 0 0;padding-left:20px;overflow-wrap:anywhere}li{white-space:pre-wrap;margin:5px 0}.empty{text-align:center;padding:32px}@media(max-width:650px){.filters{grid-template-columns:1fr 1fr}.filters label:nth-child(2){grid-column:auto}main{padding:0 12px}section,article{padding:16px}h1{font-size:26px}.actions a,.actions button{flex:1;text-align:center}}
</style></head><body><main>
<nav><a href="sprava.php">← Správa</a><a href="nastaveni.php">Nastavení</a><a href="id.php">Zápis g</a><button id="theme" type="button">☾ / ☀</button></nav>
<h1>Historie změn filamentů</h1><p class="hint">Pouze pro správce. Čas zápisu ukazuje skutečné uložení; datum změny může být zadané ručně. Historie se zaznamenává od této verze, i při skrytém datu ve veřejném přehledu.</p>
<section><form method="get"><div class="filters">
<label>ID<input type="number" name="id" min="1" value="<?=h($filters['id'])?>"></label>
<label>Hledat filament<input type="search" name="q" placeholder="Název, výrobce, materiál…" value="<?=h($filters['q'])?>"></label>
<label>Zápis od<input type="date" name="from" value="<?=h($filters['from'])?>"></label>
<label>Zápis do<input type="date" name="to" value="<?=h($filters['to'])?>"></label>
</div><div class="actions"><button class="primary" type="submit">Filtrovat</button><a href="historie.php">Vymazat filtry</a><button name="format" value="pdf">Export PDF</button><button name="format" value="csv">Export CSV</button></div></form><p class="hint">Export zahrne všechny záznamy podle vyplněných filtrů, i z dalších stránek.</p></section>
<p><?=h($total)?> záznamů · Strana <?=h($page)?> z <?=h($pages)?></p>
<?php if(!$shown): ?><section class="empty">Zatím žádné změny pro tento výběr. Nové zápisy se objeví po uložení filamentu.</section><?php endif; ?>
<?php foreach($shown as $e): ?><article>
<h2>#<?=h($e['filamentId'])?> · <?=h($e['after']['color']??$e['before']['color']??'Filament')?></h2>
<div class="meta"><strong><?=h(date('d.m.Y H:i:s',strtotime($e['recordedAt'])))?></strong><span class="tag"><?=h(historyAction($e))?></span><span><?=h(historySource($e))?></span></div>
<ul><?php foreach(historyDetails($e) as $detail): ?><li><?=h($detail)?></li><?php endforeach; ?></ul>
</article><?php endforeach; ?>
<nav aria-label="Stránkování"><?php if($page>1): ?><a href="?<?=h(http_build_query(array_merge($filters,['page'=>$page-1])))?>">← Novější</a><?php endif; ?><?php if($page<$pages): ?><a href="?<?=h(http_build_query(array_merge($filters,['page'=>$page+1])))?>">Starší →</a><?php endif; ?></nav>
</main><script>document.getElementById('theme').onclick=()=>{const t=document.documentElement.dataset.theme==='dark'?'light':'dark';document.documentElement.dataset.theme=t;try{localStorage.setItem('filament-theme',t)}catch(e){}};</script></body></html>
