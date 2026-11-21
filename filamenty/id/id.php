<?php
define('FILAMENT_APP',true); require __DIR__.'/core.php';
$overviewUrl='../index.php'; $csrf=csrf();
if($_SERVER['REQUEST_METHOD']==='POST') {
    try {
        checkCsrf($csrf);
        $code=$_POST['code']??null;
        if(!is_string($code)||!preg_match('/\A[0-9]{5}\z/',$code)||substr($code,0,2)==='00') throw new RuntimeException('Zadej přesně 5 číslic: ID 01–99 a množství 000–999 g.',400);
        $id=(int)substr($code,0,2); $weight=(int)substr($code,2);
        $item=locked(function() use($id,$weight) {
            $items=rows(); $key=null;
            foreach($items as $k=>$r) if((int)$r['id']===$id) { if($key!==null) throw new RuntimeException('Duplicitní ID v datech.',409); $key=$k; }
            if($key===null) throw new RuntimeException('Filament s tímto ID neexistuje.',404);
            $before=$items[$key];
            $items[$key]['weight']=$weight; $items[$key]['changedAt']=date('Y-m-d');
            commitChange($items,$before,$items[$key],'code'); return $items[$key];
        });
        reply(['ok'=>true,'id'=>substr($code,0,2),'weight'=>$weight,'changedAt'=>$item['changedAt'],'name'=>$item['color'],'material'=>$item['mat'],'vendor'=>$item['vendor'],'backup'=>backup()]);
    } catch(Throwable $e) { fail($e); }
}
?>
<!doctype html>
<html lang="cs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#6055dc">
<title>Rychlý zápis – Moje filamenty</title>
<script>
try {document.documentElement.dataset.theme=localStorage.getItem('filament-theme')||(matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light');} catch(e) {}
</script>
<style>
:root{color-scheme:light;--bg:#f6f7fd;--surface:#fff;--soft:#f3f5fc;--ink:#222a46;--muted:#65718c;--stroke:#e4e8f4;--purple:#6858df;--ok:#16775a;--error:#b82f50}
:root[data-theme=dark]{color-scheme:dark;--bg:#111625;--surface:#1b2234;--soft:#222b40;--ink:#eef1ff;--muted:#a5b0ca;--stroke:#36415b;--purple:#a597ff;--ok:#62d9b7;--error:#ff91a9}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font:16px system-ui,-apple-system,"Segoe UI",sans-serif}a{color:inherit;text-decoration:none}button,input{font:inherit}button{cursor:pointer}.bar{background:var(--surface);border-bottom:1px solid var(--stroke)}.bar-inner{max-width:900px;margin:auto;padding:14px 18px;display:flex;align-items:center;justify-content:space-between;gap:12px}.brand{font-size:18px;font-weight:850}.brand b{color:var(--purple)}.buttons{flex-wrap:wrap;display:flex;gap:8px}.button{border:1px solid var(--stroke);border-radius:12px;background:var(--surface);color:var(--ink);padding:12px;min-height:46px;font-weight:700}.container{max-width:660px;padding:30px 16px;margin:auto}.hero{text-align:center;margin-bottom:24px}.hero h1{font-size:30px;letter-spacing:-1px;margin:8px 0}.hero p,.hint{color:var(--muted);line-height:1.6}.eyebrow{color:var(--purple);font-size:11px;letter-spacing:1.5px;font-weight:850;text-transform:uppercase}.panel{background:var(--surface);border:1px solid var(--stroke);border-radius:23px;padding:26px;box-shadow:0 14px 40px #222a5714}label{display:block;font-weight:750;margin-bottom:12px}#code{width:100%;min-height:90px;background:var(--soft);color:var(--ink);border:2px solid var(--stroke);border-radius:15px;text-align:center;font-size:clamp(32px,9vw,48px);font-weight:800;letter-spacing:.16em;font-variant-numeric:tabular-nums;padding:14px 6px}#code:focus{border-color:var(--purple);outline:2px solid var(--purple);outline-offset:3px}#code[readonly]{opacity:.7}.hint{font-size:14px;margin:14px 0}.hint b{color:var(--ink)}.primary{width:100%;background:linear-gradient(115deg,#7059e2,#4c89ea);color:white;border:0;margin-top:8px}button:disabled{opacity:.6;cursor:wait}#status{margin:18px 0 0;line-height:1.5;overflow-wrap:anywhere}#status[data-state=error]{color:var(--error)}#status[data-state=success]{color:var(--ok)}#receipt{margin-top:20px;border:1px solid var(--ok);border-radius:15px;padding:18px;background:var(--soft)}#receipt h2{font-size:21px;white-space:pre-wrap;overflow-wrap:anywhere;margin:8px 0}#receipt p{margin:6px 0}#savedWeight{font-size:28px;font-weight:850}.tag{color:var(--purple);font-weight:800}.details{color:var(--muted)}[hidden]{display:none!important}@media(max-width:420px){.bar-inner{padding:12px}.brand{font-size:16px}.button{padding:10px}.panel{padding:20px 16px}.container{padding:24px 12px}}
</style>
</head>
<body>
<header class="bar"><div class="bar-inner">
<a class="brand" href="<?= htmlspecialchars($overviewUrl, ENT_QUOTES, 'UTF-8') ?>">Moje <b>filamenty</b></a>
<div class="buttons"><a class="button" href="sprava.php">Správa</a><button type="button" id="themeToggle" class="button" aria-label="Přepnout režim">☾</button><a class="button" href="<?= htmlspecialchars($overviewUrl, ENT_QUOTES, 'UTF-8') ?>">← Přehled</a></div>
</div></header>
<main class="container">
<div class="hero"><span class="eyebrow">Rychlá správa skladu</span><h1>Zápis množství</h1><p>Pět číslic. Automatické uložení.</p></div>
<section class="panel">
<form id="codeForm">
<label for="code">Kód filamentu a množství</label>
<input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]{5}" autocomplete="off" autocapitalize="off" spellcheck="false" enterkeyhint="done" placeholder="05500" aria-describedby="codeHint" required autofocus>
<p class="hint" id="codeHint"><b>05</b> = ID filamentu · <b>500</b> = aktuální množství v gramech.<br>Po zadání páté číslice se množství automaticky uloží, datum změny se nastaví na dnešek a zápis se přidá do historie.</p>
<button type="submit" id="saveButton" class="button primary">Uložit / zopakovat zápis</button>
</form>
<noscript><p>Pro automatické zadávání zapni JavaScript.</p></noscript>
<p id="status" role="status" aria-live="polite" aria-atomic="true">Připraveno pro kód.</p>
<p id="backupStatus" class="hint" role="status"></p>
<section id="receipt" hidden aria-label="Poslední úspěšný zápis">
<span class="tag" id="savedId"></span><h2 id="savedName"></h2><p class="details" id="savedDetails"></p><p id="savedWeight"></p><p id="savedDate"></p><p>✓ Úspěšně uloženo</p>
</section>
</section>
</main>
<script>
'use strict';
const $=id=>document.getElementById(id);
const input=$('code'), button=$('saveButton'), status=$('status');
const csrf=<?= json_encode($csrf) ?>;
let busy=false;
function theme(){const dark=document.documentElement.dataset.theme==='dark';$('themeToggle').textContent=dark?'☀':'☾';$('themeToggle').setAttribute('aria-label',dark?'Světlý režim':'Tmavý režim');}
$('themeToggle').addEventListener('click',()=>{const next=document.documentElement.dataset.theme==='dark'?'light':'dark';document.documentElement.dataset.theme=next;try{localStorage.setItem('filament-theme',next);}catch(e){}theme();});theme();
function message(text,state){status.textContent=text;status.dataset.state=state;}
async function save(){
    if(busy) return;
    const code=input.value;
    if(!/^[0-9]{5}$/.test(code)||code.startsWith('00')){
        message('Zadej přesně 5 číslic: ID 01–99 a množství 000–999 g.','error');
        input.setAttribute('aria-invalid','true');return;
    }
    busy=true;input.readOnly=true;button.disabled=true;
    input.removeAttribute('aria-invalid');$('receipt').hidden=true;
    message('Ukládám…','busy');
    const controller=new AbortController();
    const timeout=setTimeout(()=>controller.abort(),20000);
    try{
        const response=await fetch(location.pathname,{method:'POST',credentials:'same-origin',cache:'no-store',signal:controller.signal,body:new URLSearchParams({code,csrf})});
        let data;
        try{data=await response.json();}catch(e){throw new Error('Server neposlal potvrzení zápisu. Zkontroluj připojení a přístup ke stránce.');}
        if(!response.ok||data.ok!==true) throw new Error(data.error||'Uložení nebylo potvrzeno.');
        $('backupStatus').textContent=data.backup||'';
        $('savedId').textContent='ID '+data.id;
        $('savedName').textContent=data.name||'(Název v databázi není vyplněný)';
        $('savedDetails').textContent=[data.material,data.vendor].filter(Boolean).join(' · ');
        $('savedWeight').textContent=data.weight+' g';$('savedDate').textContent='Datum změny: '+data.changedAt.split('-').reverse().join('.');$('receipt').hidden=false;
        input.value='';
        message('Uloženo: ID '+data.id+' · '+data.name+' · '+data.weight+' g. Můžeš zadat další kód.','success');
    }catch(e){
        message((e.name==='AbortError'?'Server včas nepotvrdil uložení.':e.message)+' Kód zůstal v poli; můžeš zopakovat zápis stejného množství.','error');
    }finally{
        clearTimeout(timeout);busy=false;input.readOnly=false;button.disabled=false;input.focus();
        if(input.value) input.select();
    }
}
input.addEventListener('input',()=>{
    input.removeAttribute('aria-invalid');
    if(input.value.length===5){save();}
    else if(input.value.length>5||/[^0-9]/.test(input.value)){message('Kód musí obsahovat přesně 5 číslic.','error');}
    else{message('Zadáno '+input.value.length+' z 5 číslic.','');}
});
$('codeForm').addEventListener('submit',e=>{e.preventDefault();save();});
</script>
</body>
</html>
