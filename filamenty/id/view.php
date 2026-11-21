<?php
if (!defined('FILAMENT_APP')) { http_response_code(404); exit; }
$filamentsJson=json_encode($items,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_THROW_ON_ERROR);
?>
<!doctype html>
<html lang="cs">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#6055dc">
  <title>Moje filamenty | Přehled skladu</title>
  <script>try { document.documentElement.dataset.theme = localStorage.getItem('filament-theme') || (matchMedia('(prefers-color-scheme:dark)').matches ? 'dark' : 'light'); } catch(e) { document.documentElement.dataset.theme='light'; }</script>
  <style>
    :root { color-scheme:light; --bg:#f6f7fd;--surface:#fff;--soft:#f3f5fc;--soft2:#edeff8;--ink:#222a46;--muted:#75809b;--stroke:#e4e8f4;--shadow:0 12px 35px rgba(34,42,87,.06);--purple:#6858df;--blue:#3986ed;--teal:#10aa95;--pink:#ed65a6;--orange:#e8a449; }
    :root[data-theme="dark"] { color-scheme:dark; --bg:#111625;--surface:#1b2234;--soft:#222b40;--soft2:#29334a;--ink:#eef1ff;--muted:#a5b0ca;--stroke:#36415b;--shadow:0 12px 40px rgba(0,0,0,.15);--purple:#a597ff;--blue:#69abff;--teal:#42d8bb;--pink:#ff86b8;--orange:#f6bd68; }
    *,*:before,*:after{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;background:radial-gradient(ellipse 700px 430px at 4% -15%,rgba(115,100,230,.13),transparent 80%),radial-gradient(ellipse 560px 500px at 98% 0%,rgba(49,187,212,.1),transparent 80%),var(--bg);color:var(--ink);font-family:system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;font-size:14px;line-height:1.5;min-height:100vh}button,input,select{font:inherit}button{cursor:pointer}button:disabled{cursor:wait;opacity:.6}a{color:inherit;text-decoration:none}button:focus-visible,a:focus-visible,input:focus-visible,select:focus-visible{outline:3px solid var(--blue);outline-offset:2px}.wrap{max-width:1350px;margin:0 auto;padding:0 24px}.hidden{display:none!important}
    .nav{background:var(--surface);border-bottom:1px solid var(--stroke);position:sticky;top:0;z-index:20;box-shadow:0 5px 20px rgba(20,20,50,.025)}.nav-inner{flex-wrap:wrap;padding-top:10px;padding-bottom:10px;min-height:78px;display:flex;align-items:center;justify-content:space-between;gap:16px}.brand{display:flex;align-items:center;gap:12px;font-size:19px;font-weight:850;letter-spacing:-.7px}.brand-mark{height:43px;width:43px;border-radius:15px;color:#fff;display:grid;place-items:center;background:linear-gradient(135deg,#8e69ee,#465fdf);box-shadow:0 6px 15px rgba(93,80,212,.26);font-size:23px}.brand span span{color:var(--purple)}.nav-actions{flex-wrap:wrap;display:flex;align-items:center;gap:9px}.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border-radius:13px;border:1px solid var(--stroke);padding:11px 15px;font-weight:750;background:var(--surface);color:var(--ink);transition:transform .18s,background .18s,border-color .18s,box-shadow .18s;white-space:nowrap}.btn:hover{transform:translateY(-1px);background:var(--soft);box-shadow:0 5px 14px rgba(0,0,0,.035)}.btn-primary{border:0;color:#fff;background:linear-gradient(110deg,#6659de,#4d84ed);box-shadow:0 8px 18px rgba(98,89,230,.2)}.btn-primary:hover{background:linear-gradient(110deg,#5c4cda,#4275e3)}.btn-danger{color:#ce416d;border-color:rgba(206,65,109,.25)}.btn-icon{width:43px;height:43px;padding:0;font-size:19px}.nav .secondary-link{color:var(--muted);font-weight:700;padding:9px 12px}.nav .secondary-link:hover{color:var(--purple)}
    .intro{display:flex;align-items:flex-end;justify-content:space-between;gap:14px;padding:34px 0 23px}.eyebrow{font-size:11px;text-transform:uppercase;font-weight:850;letter-spacing:1.8px;color:var(--purple);margin-bottom:8px}.intro h1{font-size:clamp(28px,3.7vw,42px);line-height:1.15;letter-spacing:-1.5px;margin:0 0 8px;font-weight:850}.intro h1 em{font-style:normal;background:linear-gradient(95deg,#6858df,#e269aa);background-clip:text;-webkit-background-clip:text;color:transparent}.intro p{margin:0;color:var(--muted);font-size:14px}.chip{border:1px solid rgba(106,88,225,.2);background:rgba(106,88,225,.09);color:var(--purple);padding:8px 13px;border-radius:100px;font-size:12px;font-weight:800;white-space:nowrap}
    .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:17px}.stat{min-width:0;display:flex;align-items:center;gap:15px;background:var(--surface);border:1px solid var(--stroke);border-radius:20px;padding:19px 20px;box-shadow:var(--shadow)}.stat-ico{height:48px;width:48px;flex:none;display:grid;place-items:center;border-radius:16px;font-size:23px}.stat:nth-child(1) .stat-ico{background:rgba(108,90,231,.13);color:var(--purple)}.stat:nth-child(2) .stat-ico{background:rgba(35,164,189,.12);color:var(--teal)}.stat:nth-child(3) .stat-ico{background:rgba(234,165,70,.15);color:var(--orange)}.stat:nth-child(4) .stat-ico{background:rgba(232,95,161,.13);color:var(--pink)}.stat-label{font-size:12px;color:var(--muted);font-weight:700}.stat-value{font-size:25px;font-weight:850;line-height:1.25;letter-spacing:-.9px}.stat-value small{font-size:14px;color:var(--muted);font-weight:650}
    .dashboard{display:grid;grid-template-columns:1.16fr 1fr;gap:17px;margin-bottom:22px}.panel{background:var(--surface);border:1px solid var(--stroke);border-radius:23px;padding:23px;box-shadow:var(--shadow);min-width:0}.section-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:15px}.section-head h2{margin:0;font-size:18px;font-weight:840;letter-spacing:-.45px}.section-head .subtle{font-size:11px;color:var(--muted);font-weight:700}.chart-inner{display:flex;align-items:center;gap:30px;min-height:193px}.donut{height:178px;width:178px;min-width:178px;flex:none;display:grid;place-items:center;border-radius:50%;background:var(--soft2);position:relative}.donut:before{content:"";background:var(--surface);height:122px;width:122px;border-radius:50%;position:absolute}.donut-center{position:relative;text-align:center}.donut-center strong{font-size:26px;display:block;line-height:1.1;font-weight:850}.donut-center small{color:var(--muted);font-size:11px;font-weight:700}.legend{flex:1;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px 10px}.legend-row{border:1px solid transparent;border-radius:9px;padding:6px;text-align:left;background:none;color:var(--ink)}.legend-row:hover{background:var(--soft);border-color:var(--stroke)}.legend-heading{display:flex;align-items:center;gap:7px;color:var(--muted);font-weight:750;font-size:12px}.legend-dot{height:9px;width:9px;border-radius:4px}.legend-value{font-size:16px;font-weight:850;padding-left:16px}.legend-value small{font-size:10px;color:var(--muted);font-weight:650}.bar-list{display:flex;flex-direction:column;gap:15px;margin-top:24px}.bar-top{display:flex;justify-content:space-between;gap:10px;font-weight:780;font-size:13px;margin-bottom:6px}.bar-top span:last-child{color:var(--muted);font-weight:700;font-size:12px}.bar-track{height:11px;border-radius:12px;background:var(--soft2);overflow:hidden}.bar-fill{height:100%;border-radius:12px;transition:width .3s}.graph-note{font-size:11px;color:var(--muted);margin:15px 0 0}
    .filters{margin-bottom:19px}.filter-head{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-bottom:17px}.filter-head h2{font-size:18px;letter-spacing:-.4px;margin:0}.filters-grid{display:grid;grid-template-columns:2.2fr .85fr 1.25fr 1.5fr 1.2fr;gap:12px;align-items:end}.field{min-width:0}.field label{display:block;color:var(--muted);font-size:11px;font-weight:850;margin-bottom:7px}.field input,.field select{width:100%;min-width:0;display:block;background:var(--soft);color:var(--ink);border:1px solid var(--stroke);padding:12px 12px;border-radius:13px;min-height:45px;outline:none}.field input::placeholder{color:var(--muted)}.field input:focus,.field select:focus{border-color:var(--purple);box-shadow:0 0 0 3px rgba(109,89,225,.12)}.field select{cursor:pointer}.filter-input-wrap{position:relative}.filter-input-wrap input{padding-left:35px}.filter-input-wrap:before{content:'⌕';font-size:26px;color:var(--muted);position:absolute;top:2px;left:11px}.filter-bottom{display:flex;gap:16px;align-items:center;justify-content:space-between;margin-top:16px;flex-wrap:wrap}.checkbox-label{display:flex;align-items:center;gap:8px;color:var(--muted);font-size:12px;font-weight:700;cursor:pointer}.checkbox-label input{accent-color:#7561e8;height:17px;width:17px}.muted{color:var(--muted)}
    .catalog-head{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:16px}.catalog-head h2{font-size:20px;letter-spacing:-.5px;margin:0}.catalog-head h2 span{color:var(--purple)}.catalog-head p{font-size:12px;color:var(--muted);margin:0}.cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(245px,1fr));gap:17px;margin-bottom:35px}.filament-card{min-width:0;background:var(--surface);border:1px solid var(--stroke);border-radius:22px;overflow:hidden;box-shadow:var(--shadow);transition:transform .2s,box-shadow .2s}.filament-card:hover{transform:translateY(-4px);box-shadow:0 18px 42px rgba(40,42,86,.12)}.card-top{position:relative;display:flex;align-items:center;justify-content:center;height:219px;background:radial-gradient(circle at 50% 43%,var(--tint) 0,transparent 66%),linear-gradient(125deg,var(--soft),var(--surface));border-bottom:1px solid var(--stroke)}.swatch-ring{width:176px;height:176px;border-radius:50%;display:flex;align-items:center;justify-content:center;padding:7px;background:var(--surface);box-shadow:0 6px 23px rgba(20,23,60,.10),0 0 0 1px var(--stroke);transition:transform .25s}.filament-card:hover .swatch-ring{transform:scale(1.04)}.swatch-img{height:100%;width:100%;object-fit:cover;border-radius:50%;display:block;background:var(--soft2)}.card-badge{position:absolute;top:14px;padding:5px 10px;border-radius:9px;font-weight:820;letter-spacing:.25px;font-size:11px;box-shadow:0 3px 8px rgba(0,0,0,.05)}.card-id{left:14px;background:var(--surface);color:var(--muted);border:1px solid var(--stroke)}.card-mat{right:14px;background:var(--accent);color:#fff}.card-edit{position:absolute;right:15px;bottom:12px;width:37px;height:37px;display:grid;place-items:center;border:none;color:#fff;background:linear-gradient(135deg,#7b65ea,#4f8dee);border-radius:12px;font-size:18px;box-shadow:0 5px 14px rgba(85,91,201,.27)}.card-hidden{opacity:.59}.card-hidden-label{position:absolute;bottom:14px;left:15px;background:var(--ink);color:var(--surface);padding:6px 11px;border-radius:8px;font-weight:850;font-size:11px}.card-content{padding:17px 18px 18px}.card-title{font-size:17px;letter-spacing:-.3px;font-weight:825;line-height:1.28;margin:0;min-height:43px;overflow-wrap:anywhere}.card-vendor{margin:5px 0 15px;font-size:12px;color:var(--muted);font-weight:600}.card-meta{border-top:1px solid var(--stroke);display:flex;align-items:center;justify-content:space-between;padding-top:13px;gap:6px}.card-meta .date{font-size:11px;color:var(--muted)}.weight{font-size:23px;font-weight:850;letter-spacing:-.65px}.weight small{font-size:12px;font-weight:700;color:var(--muted)}.weight-low{color:#e45b77}.empty{padding:56px 20px;border:1px dashed var(--stroke);border-radius:19px;text-align:center;background:var(--surface);margin-bottom:30px}.empty b{font-size:18px}.empty p{color:var(--muted)}.hint{font-size:12px;color:var(--muted);padding:0 3px 30px}
    .modal{display:none;position:fixed;inset:0;z-index:80;background:rgba(13,19,39,.75);backdrop-filter:blur(7px);overflow-y:auto;padding:22px}.modal.open{display:flex;align-items:center;justify-content:center}.dialog{width:min(100%,525px);max-height:calc(100vh - 40px);overflow-y:auto;background:var(--surface);color:var(--ink);border:1px solid var(--stroke);border-radius:24px;padding:26px;box-shadow:0 28px 90px rgba(0,0,0,.3)}.dialog-small{max-width:395px}.dialog-title{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:20px}.dialog-title h2{font-size:22px;margin:0;letter-spacing:-.6px}.close{border:0;background:var(--soft);border-radius:11px;height:36px;width:36px;color:var(--muted);font-size:19px}.edit-fields{display:grid;gap:13px}.edit-fields .field input{background:var(--soft)}.edit-cols{display:grid;grid-template-columns:1fr 1fr;gap:12px}.photo-upload{font-size:12px;color:var(--muted)}.photo-upload input{font-size:12px}.switch-line{display:flex;align-items:center;gap:9px;padding:11px;border:1px solid var(--stroke);border-radius:13px;background:var(--soft)}.dialog-actions{display:flex;gap:9px;margin-top:22px}.dialog-actions .btn-primary{flex:1}.dialog-actions .btn-danger{margin-right:auto}.toast{position:fixed;bottom:18px;right:18px;background:var(--ink);color:var(--surface);padding:13px 17px;border-radius:13px;font-weight:740;box-shadow:0 8px 24px rgba(0,0,0,.15);max-width:360px;z-index:100}.toast.error{background:#c63f64;color:#fff}
    @media(max-width:1100px){.filters-grid{grid-template-columns:repeat(3,minmax(0,1fr))}.filters-grid>.field:first-child{grid-column:span 2}.stats{grid-template-columns:repeat(2,minmax(0,1fr))}.dashboard{grid-template-columns:1fr 1fr}.chart-inner{gap:14px}.donut{height:148px;width:148px;min-width:148px}.donut:before{height:101px;width:101px}.legend{grid-template-columns:1fr}}
    @media(max-width:720px){.wrap{padding:0 15px}.nav-inner{min-height:68px}.brand{font-size:16px;gap:8px}.brand-mark{height:37px;width:37px;border-radius:13px}.nav-actions{gap:6px}.nav-actions .btn{padding:9px 11px}.nav-actions .btn-icon{width:38px;height:38px;padding:0}.nav .secondary-link{display:none}.intro{padding:24px 0 20px;align-items:flex-start}.intro .chip{display:none}.intro h1{font-size:30px}.stats{gap:9px}.stat{padding:13px;gap:10px;border-radius:16px}.stat-ico{width:40px;height:40px;border-radius:12px;font-size:19px}.stat-value{font-size:21px}.stat-label{font-size:10px}.dashboard{grid-template-columns:1fr;gap:11px}.panel{padding:17px;border-radius:19px}.chart-inner{gap:20px;min-height:140px}.donut{height:137px;width:137px;min-width:137px}.donut:before{height:91px;width:91px}.legend{grid-template-columns:repeat(2,minmax(0,1fr))}.filters-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.filters-grid>.field:first-child{grid-column:span 2}.filter-head{align-items:flex-start}.filter-head .btn{padding:8px}.cards{grid-template-columns:repeat(2,minmax(0,1fr));gap:11px}.card-top{height:185px}.swatch-ring{width:144px;height:144px}.card-content{padding:14px 12px}.card-title{font-size:15px;min-height:39px}.card-vendor{font-size:11px}.card-meta .date{font-size:10px}.weight{font-size:19px}.card-badge{font-size:10px;top:9px;padding:4px 7px}.card-id{left:9px}.card-mat{right:9px}.card-edit{width:32px;height:32px;right:10px;bottom:10px}.dialog{padding:20px}.dialog-actions{flex-wrap:wrap}}
    @media(max-width:410px){.nav .btn-primary{font-size:12px}.nav .btn-primary .plus-word{display:none}.intro h1{font-size:28px}.stats .stat{gap:7px;padding:10px}.stat-ico{height:34px;width:34px;font-size:18px}.stat-value{font-size:19px}.cards{grid-template-columns:repeat(2,minmax(0,1fr))}.card-top{height:159px}.swatch-ring{width:119px;height:119px}.card-title{font-size:14px}.weight{font-size:18px}.chart-inner{gap:12px}.donut{height:120px;width:120px;min-width:120px}.donut:before{height:82px;width:82px}.donut-center strong{font-size:21px}.legend-value{font-size:13px}.legend-row{padding:3px}.filters-grid{grid-template-columns:1fr 1fr}}
    [hidden]{display:none!important}
    @media(prefers-reduced-motion:reduce){*,*:before,*:after{scroll-behavior:auto!important;transition:none!important;animation:none!important}}
    /* Barva filamentu – vlastní HEX náhled a editor, nezávislé na motivu. */
    .swatch-hex {width:100%;height:100%;border-radius:50%;border:1px solid rgba(30,40,65,.18);background:var(--hex-color,#8b97aa);display:grid;place-items:end center;padding:0 0 16px;color:#162238;font-weight:850;font-size:11px;text-shadow:0 1px 4px #fff;}
    .swatch-empty {background:repeating-linear-gradient(45deg,#d4dbe6,#d4dbe6 9px,#f3f5f9 9px,#f3f5f9 18px);}
    .hex-control {display:flex;flex-wrap:wrap;align-items:center;gap:9px}
    .hex-control input[type="color"]{width:59px;height:43px;min-width:59px;padding:3px;border:1px solid var(--stroke);border-radius:10px;background:var(--surface);cursor:pointer}
    .hex-control input[type="text"]{width:135px;min-width:120px;max-width:100%;padding:9px 10px;text-transform:uppercase;font-family:monospace;font-weight:800;letter-spacing:1px}
    .hex-control .btn{padding:9px 11px;font-size:12px}
    .hex-preview{height:70px;width:70px;border-radius:50%;border:2px solid var(--stroke);background:repeating-conic-gradient(#d2d8e6 0% 25%,#fff 0% 50%) 50%/14px 14px;flex:none}
    .hex-preview-row{display:flex;align-items:center;gap:14px;margin:8px 0}
    .sample-mode-note{font-size:12px;color:var(--muted);margin:5px 0 0}
    .card-hex-caption{position:absolute;bottom:12px;left:50%;transform:translateX(-50%);font-size:11px;color:var(--muted);background:var(--surface);padding:3px 7px;border-radius:8px}
    .global-preview{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:12px}
    .global-preview select{border:1px solid var(--stroke);border-radius:10px;padding:8px;background:var(--surface);color:var(--ink)}
  </style>
</head>
<body>
<header class="nav"><div class="wrap nav-inner">
  <a href="<?= $isAdmin ? '../index.php' : 'index.php' ?>" class="brand" aria-label="Moje filamenty – úvodní stránka"><span class="brand-mark">◉</span><span>Moje <span>filamenty</span></span></a>
  <div class="nav-actions">
    <a href="<?= $isAdmin ? 'id.php' : 'id/id.php' ?>" class="btn" title="Rychlá úprava hmotnosti">Zápis g</a>
    <button id="themeToggle" class="btn btn-icon" type="button" aria-label="Přepnout světlý a tmavý režim" title="Přepnout vzhled">☾</button>
    <?php if ($isAdmin): ?>
      <button class="btn btn-primary" type="button" id="addButton">＋ <span class="plus-word">Přidat filament</span></button>
      <a href="nastaveni.php" class="btn">Nastavení</a><a href="historie.php" class="btn">Historie</a>
    <?php else: ?>
      <a href="id/sprava.php" class="btn btn-primary">Správa</a>
    <?php endif; ?>
  </div>
</div></header>
<main class="wrap">
<?php if($isAdmin): ?><p class="panel" id="backupFeedback" hidden role="status"></p><?php endif; ?>
  <section class="intro"><div><div class="eyebrow">Přehled 3D tiskového materiálu</div><h1>Tvůj <em>barevný sklad.</em></h1><p>Všechny filamenty, dostupné zásoby a statistiky na jednom místě.</p></div><div class="chip">● <span id="stockIndicator">Aktuální přehled</span></div></section>
  <section id="stats" class="stats" aria-label="Celkové statistiky"></section>
  <section class="dashboard" aria-label="Grafy skladových zásob">
    <div class="panel"><div class="section-head"><h2>Materiály v zásobě</h2><span class="subtle">Podíl podle hmotnosti</span></div><div class="chart-inner"><div class="donut" id="donut"><div class="donut-center"><strong id="donutTotal">0 kg</strong><small>celkem</small></div></div><div class="legend" id="chartLegend"></div></div></div>
    <div class="panel"><div class="section-head"><h2>Počet filamentů</h2><span class="subtle">Podle materiálu</span></div><div id="materialBars" class="bar-list"></div><p class="graph-note">Kliknutím na materiál v kruhovém grafu ho vyfiltruješ.</p></div>
  </section>
  <section class="panel filters"><div class="filter-head"><h2>Najít filament</h2><button type="button" id="resetFilters" class="btn">↺ Vymazat filtry</button></div>
    <div class="filters-grid">
      <div class="field"><label for="filterText">Fulltext (barva, materiál, výrobce, ID)</label><div class="filter-input-wrap"><input id="filterText" type="search" placeholder="Např. pistáciová, SUNLU, 26…" autocomplete="off"></div></div>
      <div class="field"><label for="filterId">Konkrétní ID</label><input type="number" min="1" id="filterId" inputmode="numeric" placeholder="Např. 15"></div>
      <div class="field"><label for="filterMat">Materiál</label><select id="filterMat"><option value="ALL">Všechny materiály</option></select></div>
      <div class="field"><label for="sortBy">Řadit podle</label><select id="sortBy"><option value="id-desc">ID – od nejvyššího</option><option value="id-asc">ID – od nejnižšího</option><option value="name-asc">Název A → Z</option><option value="name-desc">Název Z → A</option><option value="weight-desc">Hmotnost – nejvíc</option><option value="weight-asc">Hmotnost – nejméně</option><option value="date-desc">Datum změny – nejnovější</option><option value="date-asc">Datum změny – nejstarší</option></select></div>
      <div class="field"><label for="filterStatus">Zobrazení</label><select id="filterStatus"><option value="all">Všechny dostupné</option><option value="low">Málo materiálu (≤ 100 g)</option><?php if ($isAdmin): ?><option value="active">Jen veřejné</option><option value="hidden">Jen skryté</option><?php endif; ?></select></div>
    </div>
  </section>
  <?php if($isAdmin || !empty($settings['publicExport'])): ?><div class="nav-actions" style="margin:16px 0"><button type="button" class="btn" data-export="pdf">Export přehledu PDF</button><button type="button" class="btn" data-export="csv">Export přehledu CSV</button><span class="hint">Podle aktuálních filtrů</span></div><?php endif; ?>
  <section><div class="catalog-head"><h2>Filamenty <span id="resultCount">(0)</span></h2><p id="resultsNote"></p></div>
    <div class="global-preview"><label for="globalSampleMode"><b>Zobrazit vzorky:</b></label><select id="globalSampleMode"><option value="saved">Podle nastavení každého filamentu</option><option value="photo">Všude fotografie</option><option value="hex">Všude HEX barvu</option></select><span class="hint" style="padding:0">Jen dočasné zobrazení, nemění uložené nastavení.</span></div>
    <div id="filament-grid" class="cards" aria-live="polite"></div><div id="no-results" class="empty hidden"><b>Nic jsme nenašli.</b><p>Zkus upravit hledání nebo vymazat filtry.</p><button class="btn" type="button" id="emptyReset">Vymazat filtry</button></div></section>
  <?php if ($isAdmin): ?><p class="hint">Datum změny se při uložení nastaví na dnešek. V editaci můžeš zvolit vlastní datum. U starších záznamů zůstává původní datum, pokud existovalo; nedatované záznamy čekají na první aktualizaci. Historie se zaznamenává od této verze.</p><?php endif; ?>
</main>
<?php if ($isAdmin): ?>
<div id="editModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="editTitle"><div class="dialog">
  <div class="dialog-title"><h2 id="editTitle">Upravit filament <span id="editIdDisplay" style="color:var(--purple)"></span></h2><button class="close" type="button" data-close="editModal" aria-label="Zavřít">×</button></div>
  <form id="editForm"><input id="editIndex" type="hidden"><div class="edit-fields">
    <div class="field"><label for="editColor">Barva / název</label><input id="editColor" maxlength="150" required placeholder="Např. pistáciová zelená"></div>
    <div class="edit-cols"><div class="field"><label for="editMat">Materiál</label><input id="editMat" list="materialsList" required maxlength="50" placeholder="PLA"><datalist id="materialsList"><option value="PLA"><option value="PETG"><option value="TPU"><option value="ABS"><option value="ASA"><option value="PA"><option value="PVB"><option value="PLA-CF"><option value="PETG-CF"></datalist></div><div class="field"><label for="editWeight">Zbývající hmotnost (g)</label><input id="editWeight" type="number" min="0" max="1000000" step="1" required></div></div>
    <div class="field"><label for="editVendor">Výrobce</label><input id="editVendor" maxlength="150" required></div>
    <div class="field"><label for="editDateMode">Datum změny</label><select id="editDateMode"><option value="auto">Automaticky – dnešní datum při uložení</option><option value="manual">Vlastní datum změny</option></select><label for="editDate" style="margin-top:12px">Vlastní datum</label><input id="editDate" type="date" disabled><small id="previousDate" class="hint"></small></div>
    <div class="field photo-upload"><label for="editImage">Fotografie – JPG / PNG / WebP</label><input id="editImage" type="file" accept="image/jpeg,image/png,image/webp"><small>Fotografie se ukládá beze změny stávajících pravidel (komprese max. 800 px). Slouží také k odhadu HEX.</small></div>
    <div class="field"><label for="editDisplayMode">Co zobrazit na kartě filamentu</label><select id="editDisplayMode"><option value="photo">Reálnou fotografii</option><option value="hex">Barevný kruh podle HEX</option></select><small>Fotografie zůstane uložená, i když přepneš na barevný kruh.</small></div>
    <div class="field"><label for="editHex">Barva HEX (pro CSV a Python kalkulačku)</label>
      <div class="hex-preview-row"><div id="editHexPreview" class="hex-preview" aria-label="Náhled vybrané barvy"></div><div><div class="hex-control"><input id="editHexPicker" type="color" value="#808080" aria-label="Vybrat barvu na paletě"><input id="editHex" type="text" maxlength="7" placeholder="#4F6B42" spellcheck="false" pattern="#[0-9a-fA-F]{6}" title="Šest číslic HEX, například #4F6B42"></div><p class="sample-mode-note">Klikni na paletu, napiš HEX, nebo použij tlačítko níže.</p></div></div>
      <div class="hex-control"><button id="detectPhotoHex" class="btn" type="button">Odhadnout z fotografie</button><button id="detectNameHex" class="btn" type="button">Odhadnout podle názvu</button></div>
      <small id="hexEstimateNotice" class="sample-mode-note">Automatický odstín je orientační. Před uložením ho můžeš opravit.</small>
    </div>
    <label class="switch-line"><input id="editActive" type="checkbox" style="accent-color:#6659de;width:18px;height:18px"> <b>Zobrazit veřejně</b></label>
  </div><div class="dialog-actions"><a class="btn" id="itemHistory" href="historie.php">Historie filamentu</a><button class="btn btn-danger" type="button" id="deleteButton">Smazat</button><button class="btn" type="button" data-close="editModal">Zrušit</button><button class="btn btn-primary" type="submit" id="saveButton">Uložit</button></div></form>
</div></div>
<?php endif; ?>
<div id="toast" class="toast hidden" role="status" aria-live="polite"></div>
<script>
'use strict';
let filaments = <?php echo $filamentsJson ?: '[]'; ?>;
const isAdmin = <?php echo $isAdmin ? 'true' : 'false'; ?>;
const csrfToken = <?php echo json_encode($csrf); ?>;
const imageEndpoint = <?= json_encode($isAdmin ? 'image.php?id=' : 'index.php?image=') ?>;
const showQuantities = isAdmin || <?= !empty($settings['showWeight']) ? 'true' : 'false' ?>;
const showDates = isAdmin || <?= !empty($settings['showChangedAt']) ? 'true' : 'false' ?>;
const quantityVisible = f => showQuantities;
const allQuantitiesVisible = () => showQuantities;
const $ = id => document.getElementById(id);
const materialsBase = {PLA:'#19b393',PETG:'#4d8bf1',TPU:'#eca448',ABS:'#ed6c9a',ASA:'#9275e4',PA:'#c28b57',PVB:'#bd7be8','PLA-CF':'#698caa','PETG-CF':'#468ec5'};
const extraColors = ['#20bfc9','#a66fd4','#f0767b','#7a94e3','#78b35d','#d38d65'];
const normalize = v => String(v ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLocaleLowerCase('cs').trim();
const escapeHTML = v => String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const weight = f => Math.max(0,Number(f.weight) || 0);
const nameCompare = (a,b) => String(a.color || '').localeCompare(String(b.color || ''),'cs',{sensitivity:'base',numeric:true});
const matKey = f => String(f.mat || 'NEURČENO').trim().toUpperCase() || 'NEURČENO';
const colorFor = mat => materialsBase[mat] || extraColors[[...mat].reduce((a,c)=>a+c.charCodeAt(0),0)%extraColors.length];
const dateOnly = f => /^\d{4}-\d{2}-\d{2}/.test(String(f.changedAt || '')) ? String(f.changedAt).slice(0,10) : '';
const dateLabel = f => { const date=dateOnly(f); return date ? new Date(date+'T12:00:00').toLocaleDateString('cs-CZ') : 'Datum neuvedeno'; };
const safeHex = value => /^#[0-9a-fA-F]{6}$/.test(String(value||'').trim()) ? String(value).trim().toUpperCase() : '';
function guessHexFromName(value){
  const n=normalize(value);
  const rules=[
    [/pistaci/, '#B4D18D'],[/armad|olivov|khaki/, '#667453'],[/limetk/, '#92CB47'],[/smaragd|emerald/, '#159C78'],[/fuchsi|magenta/, '#D9439C'],
    [/levandul/, '#AA93D4'],[/fialov/, '#9465BA'],[/ruzov/, '#EFA5C3'],[/cerven|cervene/, '#E34C4D'],[/oranz|mandarink/, '#F58A35'],
    [/zlut|sirov/, '#EBCB39'],[/zelen/, '#45A568'],[/modr/, '#478ACB'],[/tyrkys/, '#36B5B7'],[/hned|meden/, '#976B4A'],
    [/stribr|sed|siv/, '#929BA5'],[/cern/, '#20232D'],[/bil|bile/, '#F5F5F2'],[/pruhled|transparent/, '#DFE8E9'],[/zlat/, '#C5A14E']
  ];
  const found=rules.find(([rx])=>rx.test(n));if(!found)return '';
  let color=found[1];
  if(/svetl|pastel/.test(n) && !/pistaci|levandul/.test(n))color=adjustHex(color,0.35);
  else if(/tmav/.test(n))color=adjustHex(color,-0.27);
  return color;
}
function adjustHex(hex,factor){
  const rgb=[1,3,5].map(i=>parseInt(hex.slice(i,i+2),16));
  return '#'+rgb.map(x=>Math.round(factor>=0?x+(255-x)*factor:x*(1+factor)).toString(16).padStart(2,'0')).join('').toUpperCase();
}
const placeholder = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="320" height="320" viewBox="0 0 320 320"><rect fill="#edeff8" width="320" height="320"/><circle cx="160" cy="160" r="91" stroke="#a5aac4" stroke-width="16" fill="none"/><circle cx="160" cy="160" r="31" fill="#a5aac4"/><text font-family="Arial" x="160" y="287" text-anchor="middle" fill="#7d86a2" font-size="17">Bez fotografie</text></svg>');
let toastTimer;
function notify(message,error=false){const node=$('toast');node.textContent=message;node.classList.toggle('error',error);node.classList.remove('hidden');clearTimeout(toastTimer);toastTimer=setTimeout(()=>node.classList.add('hidden'),4000);}
function showModal(id){$(id)?.classList.add('open');document.body.style.overflow='hidden';}
function closeModal(id){$(id)?.classList.remove('open');document.body.style.overflow='';if(id==='editModal' && isCreating){isCreating=false;editId=null;}}
document.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',()=>closeModal(b.dataset.close)));
document.querySelectorAll('.modal').forEach(m=>m.addEventListener('click',e=>{if(e.target===m)closeModal(m.id);}));
document.addEventListener('keydown',e=>{if(e.key==='Escape')document.querySelectorAll('.modal.open').forEach(m=>closeModal(m.id));});

function updateThemeIcon(){const dark=document.documentElement.dataset.theme==='dark';$('themeToggle').textContent=dark?'☀':'☾';$('themeToggle').title=dark?'Světlý režim':'Tmavý režim';}
$('themeToggle').addEventListener('click',()=>{const next=document.documentElement.dataset.theme==='dark'?'light':'dark';document.documentElement.dataset.theme=next;try{localStorage.setItem('filament-theme',next)}catch(e){}updateThemeIcon();});updateThemeIcon();
function validVisible(f){return isAdmin || f.active !== false;}
function getVisible(){return filaments.filter(validVisible);}
function materialEntries(items){const m=new Map();items.forEach(f=>{const key=matKey(f);let row=m.get(key)||{mat:key,count:0,grams:0};row.count++;row.grams+=weight(f);m.set(key,row);});return [...m.values()].sort((a,b)=>b.grams-a.grams || a.mat.localeCompare(b.mat));}
function updateMaterialOptions(){const select=$('filterMat');const old=select.value;select.innerHTML='<option value="ALL">Všechny materiály</option>';const distinct=[...new Set(getVisible().map(matKey))].sort((a,b)=>a.localeCompare(b,'cs'));distinct.forEach(mat=>{const opt=document.createElement('option');opt.value=mat;opt.textContent=mat;select.appendChild(opt);});select.value=[...select.options].some(o=>o.value===old)?old:'ALL';}
function renderStats(){
  const visible=getVisible(),allWeight=visible.reduce((acc,f)=>acc+weight(f),0),low=visible.filter(f=>quantityVisible(f)&&weight(f)<=100).length,mats=materialEntries(visible);
  const defs=[['◉','Filamentů',visible.length,'ks'],['⬡','Zásoba materiálu',(allWeight/1000).toLocaleString('cs-CZ',{minimumFractionDigits:2,maximumFractionDigits:2}),'kg'],['◷','Do 100 g',low,'ks'],['✦','Druhů materiálu',mats.length,'typů']];
  $('stats').innerHTML=defs.filter((d,i)=>allQuantitiesVisible()||i===0||i===3).map(([icon,label,value,unit])=>`<article class="stat"><div class="stat-ico" aria-hidden="true">${icon}</div><div><div class="stat-label">${label}</div><div class="stat-value">${value} <small>${unit}</small></div></div></article>`).join('');
  $('donut').closest('.panel').hidden=!allQuantitiesVisible();
  document.querySelector('.dashboard').style.gridTemplateColumns=allQuantitiesVisible()?'':'1fr';
  document.querySelector('.graph-note').hidden=!allQuantitiesVisible();
  $('stats').style.gridTemplateColumns=allQuantitiesVisible()?'':'repeat(2,minmax(0,1fr))';
  $('stockIndicator').textContent=visible.length+' položek ve skladu';
  const angleTotal=mats.reduce((a,m)=>a+m.grams,0);let angle=0;
  const segments=mats.filter(m=>m.grams>0).map(m=>{const start=angle;angle+=360*m.grams/angleTotal;return `${colorFor(m.mat)} ${start.toFixed(3)}deg ${angle.toFixed(3)}deg`;});
  $('donut').style.background=segments.length?'conic-gradient('+segments.join(',')+')':'var(--soft2)';
  $('donutTotal').textContent=allWeight>=1000?(allWeight/1000).toLocaleString('cs-CZ',{maximumFractionDigits:1})+' kg':allWeight+' g';
  $('chartLegend').innerHTML=mats.length?mats.map(m=>`<button class="legend-row" type="button" data-chart-mat="${escapeHTML(m.mat)}" title="Filtrovat ${escapeHTML(m.mat)}"><span class="legend-heading"><i class="legend-dot" style="background:${colorFor(m.mat)}"></i>${escapeHTML(m.mat)}</span><span class="legend-value">${(m.grams/1000).toLocaleString('cs-CZ',{maximumFractionDigits:2})} <small>kg</small></span></button>`).join(''):'<p class="muted">Žádné filamenty.</p>';
  const countMax=Math.max(1,...mats.map(m=>m.count));
  $('materialBars').innerHTML=mats.length?mats.map(m=>`<div><div class="bar-top"><span>${escapeHTML(m.mat)}</span><span>${m.count} ks</span></div><div class="bar-track"><div class="bar-fill" style="width:${(100*m.count/countMax).toFixed(1)}%;background:${colorFor(m.mat)}"></div></div></div>`).join(''):'<p class="muted">Zatím není co zobrazit.</p>';
}
$('chartLegend').addEventListener('click',e=>{const b=e.target.closest('[data-chart-mat]');if(!b)return;$('filterMat').value=b.dataset.chartMat;render();$('filterText').focus({preventScroll:true});document.querySelector('.filters').scrollIntoView({behavior:'smooth',block:'center'});});
function render(){
  const search=normalize($('filterText').value),idValue=$('filterId').value.trim(),mat=$('filterMat').value,status=$('filterStatus').value,sort=$('sortBy').value;
  const filtered=getVisible().filter(f=>{
    const full=[f.id,f.color,f.mat,f.vendor,quantityVisible(f)?f.weight:'',f.changedAt||''].map(normalize).join(' ');
    return (!search || search.split(/\s+/).every(word=>full.includes(word))) && (!idValue || String(f.id)===idValue)
      && (mat==='ALL'||matKey(f)===mat)
      && (status==='all'||(status==='low'&&quantityVisible(f)&&weight(f)<=100)||(status==='active'&&f.active!==false)||(status==='hidden'&&f.active===false));
  });
  filtered.sort((a,b)=>{
    switch(sort){
      case 'id-asc':return Number(a.id)-Number(b.id);
      case 'name-asc':return nameCompare(a,b) || Number(a.id)-Number(b.id);
      case 'name-desc':return -nameCompare(a,b) || Number(a.id)-Number(b.id);
      case 'weight-asc':return Number(quantityVisible(b))-Number(quantityVisible(a)) || weight(a)-weight(b) || Number(b.id)-Number(a.id);
      case 'weight-desc':return Number(quantityVisible(b))-Number(quantityVisible(a)) || weight(b)-weight(a) || Number(b.id)-Number(a.id);
      case 'date-asc':case 'date-desc':{
        const ad=dateOnly(a),bd=dateOnly(b);if(ad&&bd&&ad!==bd)return sort==='date-asc'?ad.localeCompare(bd):bd.localeCompare(ad);
        if(ad&&!bd)return -1;if(!ad&&bd)return 1;return sort==='date-asc'?Number(a.id)-Number(b.id):Number(b.id)-Number(a.id);
      }
      default:return Number(b.id)-Number(a.id);
    }
  });
  $('resultCount').textContent='('+filtered.length+')';$('resultsNote').textContent='Z celkem '+getVisible().length+' položek';
  $('no-results').classList.toggle('hidden',filtered.length>0);
  $('filament-grid').innerHTML=filtered.map(f=>{
    const m=matKey(f),c=colorFor(m),id=Number(f.id),hidden=f.active===false;
    const mode=$('globalSampleMode').value==='saved'?(f.displayMode==='hex'?'hex':'photo'):$('globalSampleMode').value;
    const hx=safeHex(f.hexColor);
    const swatch=mode==='hex'
      ? `<div class="swatch-hex ${hx?'':'swatch-empty'}" style="--hex-color:${hx||'#8b97aa'}" title="${hx||'HEX dosud nenastaven'}"></div>`
      : `<img class="swatch-img" loading="lazy" src="${imageEndpoint}${id}&amp;v=${encodeURIComponent(f.imageVersion||'none')}" alt="Fotografie ${escapeHTML(f.color)}">`;
    return `<article class="filament-card ${hidden?'card-hidden':''}" style="--accent:${c};--tint:${c}25">
      <div class="card-top"><span class="card-badge card-id">#${id}</span><span class="card-badge card-mat">${escapeHTML(m)}</span>
        <div class="swatch-ring">${swatch}</div>${mode==='hex'&&!hx?'<span class="card-hex-caption">HEX nenastaven</span>':''}
        ${isAdmin?`<button type="button" class="card-edit" aria-label="Upravit filament ${id}" data-edit-id="${id}">✎</button>`:''}
        ${hidden?'<span class="card-hidden-label">Skryto</span>':''}
      </div><div class="card-content"><h3 class="card-title">${escapeHTML(f.color)}</h3><p class="card-vendor">${escapeHTML(f.vendor||'Neznámý výrobce')}</p>
        <div class="card-meta">${showDates?`<span class="date" title="Datum poslední změny">Změna: ${escapeHTML(dateLabel(f))}</span>`:''}${quantityVisible(f)?`<span class="weight ${weight(f)<=100?'weight-low':''}">${weight(f).toLocaleString('cs-CZ')} <small>g</small></span>`:'<span class="muted">Množství skryto</span>'}</div>
      </div></article>`;
  }).join('');
}
$('globalSampleMode').addEventListener('change',render);
$('filament-grid').addEventListener('error',e=>{if(e.target.matches('img.swatch-img')&&e.target.src!==placeholder){e.target.src=placeholder;}},true);
$('filament-grid').addEventListener('click',e=>{const button=e.target.closest('[data-edit-id]');if(button)openEdit(Number(button.dataset.editId));});
['filterText','filterId','filterMat','sortBy','filterStatus'].forEach(id=>$(id).addEventListener(['filterMat','sortBy','filterStatus'].includes(id)?'change':'input',render));
function resetFilters(){$('filterText').value='';$('filterId').value='';$('filterMat').value='ALL';$('sortBy').value='id-desc';$('filterStatus').value='all';render();}
$('resetFilters').addEventListener('click',resetFilters);$('emptyReset').addEventListener('click',resetFilters);
function resetDateEditor(f){$('editDateMode').value='auto';$('editDate').disabled=true;$('editDate').required=false;$('editDate').value=f?dateOnly(f):'';$('previousDate').textContent=f?'Dosud: '+dateLabel(f):'Nový filament';}
$('editDateMode')?.addEventListener('change',()=>{const manual=$('editDateMode').value==='manual';$('editDate').disabled=!manual;$('editDate').required=manual;});
if(!showDates)document.querySelectorAll('#sortBy option[value^="date-"]').forEach(o=>o.remove());
if(!showQuantities){document.querySelectorAll('#sortBy option[value^="weight-"]').forEach(o=>o.remove());document.querySelector('#filterStatus option[value="low"]')?.remove();}
document.querySelectorAll('[data-export]').forEach(button=>button.addEventListener('click',()=>{
  const params=new URLSearchParams({format:button.dataset.export,q:$('filterText').value,id:$('filterId').value,mat:$('filterMat').value,status:$('filterStatus').value,sort:$('sortBy').value});
  window.location.href=<?= json_encode($isAdmin?'export.php?':'export.php?') ?>+params.toString();
}));
function setHexField(hex){
  const valid=safeHex(hex);$('editHex').value=valid;$('editHexPicker').value=valid||'#808080';
  $('editHexPreview').style.background=valid||'repeating-conic-gradient(#d2d8e6 0% 25%,#fff 0% 50%) 50%/14px 14px';
  $('hexEstimateNotice').textContent=valid?'HEX '+valid+' – barvu můžeš ručně upravit.':'HEX není uložen – vyber ručně nebo ho odhadni.';
}
$('editHexPicker')?.addEventListener('input',()=>setHexField($('editHexPicker').value));
$('editHex')?.addEventListener('input',()=>{const raw=$('editHex').value;const ok=safeHex(raw);if(ok){$('editHexPicker').value=ok;$('editHexPreview').style.background=ok;}else{$('editHexPreview').style.background='repeating-conic-gradient(#d2d8e6 0% 25%,#fff 0% 50%) 50%/14px 14px';}});
$('detectNameHex')?.addEventListener('click',()=>{const guess=guessHexFromName($('editColor').value);if(!guess){notify('Název barvy nerozpoznán. Nastav HEX ručně.',true);return;}setHexField(guess);$('hexEstimateNotice').textContent='Orientační barva podle názvu; můžeš upravit.';});
// Odhad nejčastější barvy uvnitř vzorku. Ořez nepočítá s bílými okraji.
// Výsledek může ovlivnit pozadí nebo osvětlení – vždy lze nastavit přesnou barvu.
async function sourceImageForHex(){
  const file=$('editImage').files?.[0];
  const url=file?URL.createObjectURL(file):(!isCreating&&editId!==null?imageEndpoint+encodeURIComponent(editId):null);
  if(!url)throw new Error('Nejprve nahraj fotografii filamentu.');
  try{
    const im=new Image();im.decoding='async';
    await new Promise((resolve,reject)=>{im.onload=resolve;im.onerror=()=>reject(new Error('Fotografie se nepodařila načíst.'));im.src=url;});
    const canvas=document.createElement('canvas');canvas.width=96;canvas.height=96;
    const ctx=canvas.getContext('2d',{willReadFrequently:true});ctx.drawImage(im,0,0,96,96);
    return ctx.getImageData(0,0,96,96).data;
  }finally{if(file)URL.revokeObjectURL(url);}
}
function estimateHexFromPixels(pixels){
  const groups=new Map();
  for(let y=10;y<86;y+=2)for(let x=10;x<86;x+=2){
    const dx=x-48,dy=y-48;if(dx*dx+dy*dy>37*37)continue;
    const off=(y*96+x)*4;let r=pixels[off],g=pixels[off+1],b=pixels[off+2];
    if(pixels[off+3]<230)continue;
    const hi=Math.max(r,g,b),lo=Math.min(r,g,b),sat=hi-lo;
    if(hi>245&&lo>242)continue; // bílé pozadí
    const key=[r,g,b].map(v=>Math.min(7,Math.floor(v/32))).join('-');
    const center=1.5-Math.hypot(dx,dy)/100;
    const points=center*(sat>22?2.0:1.0);
    const row=groups.get(key)||{sum:[0,0,0],weight:0};row.sum[0]+=r*points;row.sum[1]+=g*points;row.sum[2]+=b*points;row.weight+=points;groups.set(key,row);
  }
  const sorted=[...groups.values()].sort((a,b)=>b.weight-a.weight);
  if(!sorted.length)throw new Error('Z fotografie nelze rozumně určit barvu, nastav HEX ručně.');
  // Sloučíme nejbližší skupiny kolem dominantního tónu, aby se omezil šum.
  const top=sorted[0],rgb=top.sum.map(v=>v/top.weight);
  return '#'+rgb.map(v=>Math.max(0,Math.min(255,Math.round(v))).toString(16).padStart(2,'0')).join('').toUpperCase();
}
$('detectPhotoHex')?.addEventListener('click',async()=>{
  const btn=$('detectPhotoHex');btn.disabled=true;
  try{const data=await sourceImageForHex();setHexField(estimateHexFromPixels(data));$('hexEstimateNotice').textContent='Barva odhadnuta z fotografie. Zkontroluj a případně ručně uprav.';}
  catch(err){notify(err.message,true);}
  finally{btn.disabled=false;}
});
let editId=null,isCreating=false;
function addFilament(){if(!isAdmin)return;isCreating=true;editId=null;const next=Math.max(0,...filaments.map(f=>Number(f.id)||0))+1;$('editIndex').value='';$('editIdDisplay').textContent='#'+next;$('editColor').value='';$('editMat').value='PLA';$('editWeight').value=1000;$('editVendor').value='';resetDateEditor(null);$('itemHistory').hidden=true;$('editActive').checked=true;$('editImage').value='';setHexField('');$('editDisplayMode').value='photo';$('deleteButton').classList.add('hidden');showModal('editModal');$('editColor').focus();}
function openEdit(id){if(!isAdmin)return;const f=filaments.find(x=>Number(x.id)===id);if(!f)return;isCreating=false;editId=id;$('editIndex').value=id;$('editIdDisplay').textContent='#'+id;$('editColor').value=f.color??'';$('editMat').value=f.mat??'';$('editWeight').value=weight(f);$('editVendor').value=f.vendor??'';resetDateEditor(f);$('itemHistory').hidden=false;$('itemHistory').href='historie.php?id='+id;$('editActive').checked=f.active!==false;$('editImage').value='';setHexField(f.hexColor||'');$('editDisplayMode').value=f.displayMode==='hex'?'hex':'photo';$('deleteButton').classList.remove('hidden');showModal('editModal');}
$('addButton')?.addEventListener('click',addFilament);
async function jpegPhoto(file){
  return new Promise((resolve,reject)=>{
    const url=URL.createObjectURL(file),img=new Image();
    img.onload=()=>{URL.revokeObjectURL(url);try{
      const scale=Math.min(1,1600/Math.max(img.naturalWidth,img.naturalHeight));
      const canvas=document.createElement('canvas');canvas.width=Math.max(1,Math.round(img.naturalWidth*scale));canvas.height=Math.max(1,Math.round(img.naturalHeight*scale));
      const ctx=canvas.getContext('2d');ctx.fillStyle='#ffffff';ctx.fillRect(0,0,canvas.width,canvas.height);ctx.drawImage(img,0,0,canvas.width,canvas.height);
      canvas.toBlob(blob=>blob?resolve(blob):reject(new Error('Nelze připravit fotografii.')),'image/jpeg',.88);
    }catch(e){reject(e);}};
    img.onerror=()=>{URL.revokeObjectURL(url);reject(new Error('Použij obrázek JPG, PNG nebo WebP.'));};img.src=url;
  });
}
async function mutate(action,item,file){
  const form=new FormData();form.append('csrf',csrfToken);form.append('action',action);form.append('item',JSON.stringify(item));
  if(file)form.append('image',await jpegPhoto(file),'photo.jpg');
  const res=await fetch('sprava.php',{method:'POST',body:form,credentials:'same-origin',cache:'no-store'});
  let data;try{data=await res.json();}catch(e){throw new Error('Server nepotvrdil zápis. Obnov stránku a ověř data; zkontroluj také limit nahrávání hostingu.');}
  if(!res.ok||data.ok!==true||!Array.isArray(data.items))throw new Error(data.error||'Zápis nebyl potvrzen.');
  filaments=data.items;updateMaterialOptions();renderStats();render();
  if(data.backup){$('backupFeedback').textContent=data.backup;$('backupFeedback').hidden=false;}
  return data;
}
$('editForm')?.addEventListener('submit',async e=>{
  e.preventDefault();const button=$('saveButton'),prev=filaments.find(f=>Number(f.id)===editId);
  const input={id:isCreating?0:editId,_rev:prev?._rev,mat:$('editMat').value.trim().toUpperCase(),color:$('editColor').value.trim(),weight:Number($('editWeight').value),vendor:$('editVendor').value.trim(),active:$('editActive').checked,hexColor:$('editHex').value.trim().toUpperCase(),displayMode:$('editDisplayMode').value,changedAt:$('editDate').value,dateMode:$('editDateMode').value};
  button.disabled=true;$('deleteButton').disabled=true;button.textContent='Ukládám…';
  try{const data=await mutate('save',input,$('editImage').files?.[0]);isCreating=false;closeModal('editModal');notify('Filament #'+data.savedId+' byl uložen.');}
  catch(err){notify(err.message,true);}
  finally{button.disabled=false;$('deleteButton').disabled=false;button.textContent='Uložit';}
});
$('deleteButton')?.addEventListener('click',async()=>{
  if(!isAdmin||isCreating||editId===null)return;const f=filaments.find(x=>Number(x.id)===editId);
  if(!confirm('Opravdu smazat filament #'+editId+' – '+(f?.color||'')+'?'))return;
  const b=$('deleteButton');b.disabled=true;$('saveButton').disabled=true;
  try{await mutate('delete',{id:editId,_rev:f._rev});closeModal('editModal');notify('Filament byl odebrán.');}
  catch(err){notify(err.message,true);}finally{b.disabled=false;$('saveButton').disabled=false;}
});
window.addEventListener('pageshow',e=>{if(e.persisted)location.reload();});
updateMaterialOptions();renderStats();render();
</script>
</body>
</html>
