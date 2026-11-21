# Správa filamentů

**Správa filamentů** je česká webová aplikace v PHP pro vedení zásob filamentů pro 3D tisk. Je připravená pro vlastní PHP webhosting, aby šlo stav materiálů sledovat a upravovat také z mobilního telefonu, například při nákupu, ve firmě nebo mimo domácí počítač.

Projekt vznikl jako osobní evidence. Autor jej vytváří podle vlastních potřeb s pomocí umělé inteligence; aplikace i dokumentace jsou zaměřeny především na české uživatele.

> **Bezpečnost před spuštěním:** administrační část **nemá vlastní přihlašovací obrazovku**. V přiloženém balíčku je proto adresář `filamenty/id/` na Apache standardně **zablokovaný** souborem `.htaccess`. Před používáním správy je nezbytné nastavit heslem chráněný přístup na webhostingu. Nikdy nevystavuj `id/sprava.php`, `id/id.php`, `id/nastaveni.php` a další soubory v `id/` bez ochrany přihlášením. Ověř i v anonymním okně prohlížeče.

## Co aplikace skutečně umí

- Zobrazit přehled jednotlivých filamentů s **ID, druhem materiálu, názvem barvy, výrobcem**, dostupnou gramáží a datem změny.
- **Přidávat, upravovat a mazat** filamenty; každá změna zapisuje historii.
- Zobrazovat údaje na kartách, vyhledávat podle ID a názvů, filtrovat podle materiálu a stavu zásob, řadit a zobrazovat souhrny a grafy.
- Uložit **reálnou fotografii** filamentu, nebo vybrat **HEX odstín** – ručně, orientačním odhadem z fotografie nebo podle názvu.
- U každé položky uložit, zda se zobrazí fotografie nebo barevný kruh; navíc dočasně přepnout zobrazení celého seznamu.
- Nastavovat **veřejnou viditelnost** filamentů a rozhodnout, zda se veřejně zobrazuje množství, datum změny a export přehledu.
- Aktualizovat množství rychlým **pětimístným číselným kódem**: první dvě číslice ID 01–99, další tři číslice nová zásoba 000–999 g.
- Zobrazit historii přidání, úprav a odstranění položek a exportovat ji do CSV nebo PDF.
- Exportovat filtrovaný přehled filamentů do **CSV** nebo **PDF**; správcovský export zahrnuje i skryté položky.
- Volitelně odesílat **e-mailové ZIP zálohy** dat, nastavení, historie a používaných fotografií. Záloha je standardně vypnutá a vyžaduje podporu PHP `mail()`, `ZipArchive` a nastavení e-mailů.

Aplikace **neobsahuje import CSV do PHP databáze**, ani automatickou online synchronizaci s Pythonem. Zálohu lze obnovit ručním nahráním souborů na webhosting dle postupu v [instalačním návodu](INSTALACE.md).

## Propojení s Python kalkulačkou

Samostatná desktopová [**Kalkulačka 3D tisku**](https://github.com/kolobezkarmichal/Kalkulacka-3D-tisku-) používá údaje z této PHP evidence k nacenění tisků v Bambu Studiu.

Postup: ve stránce **`filamenty/id/sprava.php`** klikni na **Export přehledu CSV** (bez filtru, chceš-li všechny položky), ulož `.csv` a v Python kalkulačce klikni na **Import skladu**. Python načítá především `ID`, `Název / barva`, `Materiál`, `Výrobce`, `Množství (g)` a volitelně `Datum změny`, `HEX barva`, `Viditelnost`. **Stejné ID** propojuje položky obou aplikací; při dalším importu se sklad aktualizuje, již nastavené ceny v Pythonu zůstávají. Vložená HEX barva má přednost před odhadem odstínu podle názvu.

**Formát exportu:** CSV UTF‑8 s BOM, **středník** jako oddělovač. Na konci CSV jsou sloupce `HEX barva` a `Zobrazení vzorku`; PDF tyto strojové sloupce nemá. V souboru není nákupní cena filamentu – tu zadáš až v Python kalkulačce. **Export/import je ruční jednosměrný přenos; kalkulačka automaticky neodepisuje zásoby na PHP webu.** Ukázka s vymyšlenými položkami: [ukazka-export-filamentu.csv](ukazka-export-filamentu.csv).

## Instalace a první spuštění

Podrobný návod včetně ochrany správy, práv k souborům, dat a PDF: **[INSTALACE.md](INSTALACE.md)**.

1. Nahraj **celou složku `filamenty/`** na vlastní webhosting s PHP.
2. Zajisti zabezpečený přístup k adresáři **`filamenty/id/`** (typicky Apache Basic Auth; viz bezpečnostní šablona). Výchozí `.htaccess` úmyslně přístup do správy blokuje.
3. Zajisti zápis do `filamenty/id/` a `filamenty/id/files/` pro PHP proces.
4. Otevři veřejný přehled `/filamenty/index.php` a chráněnou správu `/filamenty/id/sprava.php`.
5. Vytvoř první filament a nastav podle potřeby vzhled, množství, fotografie, HEX a exporty. Databáze může vzniknout automaticky při prvním zápisu.
6. Pro fungování PDF nahraj **vlastní legálně získaný soubor `DejaVuSans.ttf`** do `filamenty/id/files/`. Font **není součástí repozitáře ani ZIPu**. Bez něj ostatní funkce běží, PDF však nebude fungovat.

## Technologie

- **PHP 7.4+**; lokálně ověřená syntaxe na PHP 8.4.
- PHP `json`, `session`, `zlib`; pro nahrávání fotografií rozšíření **GD** s JPEG/PNG/WebP; pro odesílání kompletních záloh **ZIP** a funkce `mail()`; EXIF je volitelný.
- Webserver s podporou **PHP a ochrany adresáře**; přibalené `.htaccess` soubory jsou určeny pro Apache 2.4. Pokud server pravidla ignoruje, zabezpeč administraci jiným způsobem **před jejím zpřístupněním**.
- Pro prohlížení a úpravy je potřeba prohlížeč s JavaScriptem; CSS i JavaScript jsou vložené do PHP stránek, externí knihovny ani databázový server nejsou potřeba.
- Odesílání e-mailových záloh vyžaduje platné adresy, `ZipArchive` a funkční poštovní systém hostingu.

## Kam se ukládají data

Aplikace používá **souborovou evidenci, nikoli SQL**:

| Soubor / složka | Význam |
|---|---|
| `filamenty/id/filaments.php` | Soukromá databáze filamentů **i historie změn**. |
| `filamenty/id/settings.php` | Soukromé nastavení a adresy e-mailové zálohy. |
| `filamenty/id/backup-status.php` | Stav posledního pokusu o zálohu. |
| `filamenty/id/lock.php` | Zámek proti souběžným zápisům. |
| `filamenty/id/files/` | Nahrané fotografie a volitelný font pro PDF. |

V repozitáři jsou pouze **prázdné vzory `*.example.php`**. Skutečná databáze, nastavení, historie, fotografie, hesla a zálohy jsou záměrně vynechané a chráněné v `.gitignore`. Data můžeš založit zkopírováním vzoru nebo je aplikace vytvoří při prvním zápisu. **Při aktualizacích PHP zdrojů nikdy nepřepisuj vlastní soubory s daty a fotografiemi.**

## Exporty a importy

- **CSV přehledu**: export filtrovaných položek, určený i pro Python kalkulačku. Správcovská cesta `id/export.php`; veřejný export `export.php` jen po povolení.
- **PDF přehledu**: tiskový přehled, vyžaduje `DejaVuSans.ttf` a zlib.
- **CSV/PDF historie**: v chráněné stránce `id/historie.php`.
- **ZIP záloha**: kompletní data a používané fotografie lze odeslat e-mailem po povolení v Nastavení. Obnova se provádí **ručně**.
- **CSV import do webové aplikace není implementován.** CSV se importuje **do Python kalkulačky**, ne opačně.

## Bezpečnost, licence a omezení

- **Správa vyžaduje externí autentizaci** poskytovanou hostingem. CSRF ochrana formulářů nepředstavuje náhradu přihlašování.
- Soukromé údaje nejsou součástí GitHub projektu. Nesdílej soukromé zálohy, osobní `filaments.php`, `settings.php` ani nahrané fotografie.
- Používej HTTPS a silné heslo pro chráněný adresář. Nastavení viditelnosti je určeno k řízení veřejného přehledu, nikoli k ochraně administrace.
- Automatický HEX odhad je orientační, závisí na fotografii a osvětlení.
- Tento balíček nemá přiloženou licenci umožňující další šíření či úpravy; autor může samostatně určit licenční podmínky projektu.
- Jde o rekonstrukci poslední dostupné podoby ze zdrojových archivů; **spolehlivé číslo verze nebylo v dodaných zdrojích zjištěno**.

Viz také [CHANGELOG.md](CHANGELOG.md).
