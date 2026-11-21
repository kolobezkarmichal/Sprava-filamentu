# Instalace Správy filamentů na vlastní webhosting

## 1. Požadavky

PHP 7.4+ (testováno pomocí PHP 8.4), podporované rozšíření JSON, Session a zlib; pro fotografie GD (JPEG/PNG/WebP), pro e-mailové zálohy ZipArchive a `mail()`. Pro PDF je nutný vlastní **DejaVuSans.ttf**. Databáze SQL ani Composer nejsou potřeba.

## 2. Nahrání souborů

Nahraj celou složku `filamenty` do kořene webu. Zachovej přesnou strukturu všech PHP souborů a složky `id/files/`.

**Důležité:** ZIP záměrně neobsahuje skutečné `filaments.php`, `settings.php`, `backup-status.php`, fotografie ani hesla. Novou prázdnou evidenci vytvoří první úspěšné uložení. Volitelně zkopíruj soubory `filaments.example.php` -> `filaments.php`, `settings.example.php` -> `settings.php`; dodrží se ochranná PHP hlavička. Nesnaž se do příkladů vkládat původní data vlastníka projektu.

## 3. Ochrana administrace – povinné

V adresáři `filamenty/id/` je přibalen `.htaccess`, který na Apache **odmítá veškeré HTTP požadavky**, dokud není správce zabezpečen. Toto je záměrná bezpečná výchozí konfigurace. **Tento soubor neodstraňuj bez náhrady autentizací.**

Pokud používáš Apache a hosting podporuje Basic Auth:

1. Ve správě hostingu zapni heslem chráněný adresář `filamenty/id/` nebo vytvoř vlastní soubor `.htpasswd` **mimo veřejný webroot**.
2. Pro vlastní nastavení je připravena šablona `filamenty/id/.htaccess.example`; v ní nastav skutečnou absolutní cestu k soukromému `.htpasswd`.
3. Teprve potom nahraď výchozí `filamenty/id/.htaccess` vytvořeným pravidlem `AuthType Basic` + `AuthUserFile` + `Require valid-user`. Případně použij ekvivalentní nastavení hostingu.
4. Otestuj anonymní okno: `/filamenty/index.php` se má zobrazit, ale `/filamenty/id/sprava.php` musí vyžadovat přihlášení; rovněž `id/export.php`, `id/id.php`, `id/nastaveni.php`, `id/historie.php`, `id/image.php`.

Na **Nginx**, na vestavěném serveru `php -S` ani při zakázaném `.htaccess` se pravidla Apache neuplatní. Takovou instalaci nevystavuj internetu bez ochrany celé složky `id` jiným způsobem. Pro bezpečné změny používej HTTPS.

## 4. Práva zápisu

PHP musí umět vytvářet/zapisovat do `filamenty/id/` a `filamenty/id/files/`. V adresáři `id` vznikají datové soubory a dočasné soubory při zápisu. Nepovoluj všem přístup `777`, pokud můžeš práva nastavit uživatelem nebo skupinou webového serveru. Hosting může potřebovat jiný režim oprávnění.

## 5. První spuštění

Otevři `/filamenty/index.php` (veřejný přehled s prázdným skladem) a po přihlášení `/filamenty/id/sprava.php`. Klikni na **Přidat filament**. Zadej materiál, název/barvu, výrobce, množství a viditelnost. Zvol fotografii nebo HEX vzorek (případný odhad můžeš doladit). Po uložení se v `filaments.php` vytvoří souborové úložiště a první záznam historie.

Další možnosti: `id/id.php` pro rychlý pětimístný zápis gramáže, `id/historie.php` pro historii, `id/nastaveni.php` pro veřejné údaje a zálohy. Nastavení e-mailové adresy `webmaster@example.org` je **pouze rezervovaný příklad**; před povolením záloh ji vyměň za skutečnou adresu povolenou hostingem.

## 6. Fotografie a PDF

Pro obrázky musí být aktivní rozšíření **GD**. Nahrávaná fotka se zmenšuje nejvýše na 800 px a ukládá jako WebP (případně JPEG). Exif slouží volitelně k opravě orientace JPEG.

PDF export používá rozšíření **zlib** a `id/pdf-font.php` pro metriky českého Unicode písma. K vykreslení **vyžaduje soubor `filamenty/id/files/DejaVuSans.ttf`**, který nebyl v původním úplném archivu nalezen a není v tomto veřejném balíčku. Získej DejaVu Sans od legálního poskytovatele (např. z Linuxového balíčku *fonts-dejavu-core*) a **nahraj jej sám** pod přesným názvem `DejaVuSans.ttf`. Bez toho budou přehledy a CSV fungovat, PDF skončí chybou.

## 7. CSV a Python

Ve správě otevři **Export přehledu CSV**. CSV je UTF‑8 BOM, oddělované středníkem, se sloupci ID, Název / barva, Materiál, Výrobce, Množství (g), Datum změny, Viditelnost, HEX barva a Zobrazení vzorku. Pokud chceš všechny položky, vymaž filtry. V [Python kalkulačce](https://github.com/kolobezkarmichal/Kalkulacka-3D-tisku-) klikni na **Import skladu**. Ta doplní své katalogové položky podle stabilních ID a převezme aktuální zásoby a HEX. Ceny nepocházejí z tohoto PHP exportu a nastavují se v Pythonu. Na webu **není import CSV** a import do Pythonu **nezpůsobuje odpis ze skladu**.

## 8. Záloha a obnova

V `id/nastaveni.php` lze zapnout e-mailové ZIP zálohy a ručně spustit test. `mail()` jen předá zprávu poštovnímu systému; doručení není automaticky zaručené. Archiv obsahuje soukromou databázi, historii, nastavení a používané fotografie. Pro obnovu na hostingu dočasně zastav úpravy a přepiš odpovídající datové soubory v `id/` a fotografie v `id/files/` z vlastní zálohy. Zachovej PHP zdrojové soubory, autentizaci a `.htaccess`. **Nikdy zálohu neumisťuj do veřejného repozitáře.**

## 9. Aktualizace

Při aktualizaci nahrazuj pouze PHP programové soubory. Neztrácej vlastní `filaments.php`, `settings.php`, `backup-status.php`, `id/files/*`, soubor s hesly ani konfiguraci chráněného adresáře. Vždy nejprve zálohuj a otestuj po aktualizaci.
