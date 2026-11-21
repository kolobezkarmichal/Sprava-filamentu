# Přehled doložených změn

Číslování verzí PHP aplikace **nebylo v dostupných zdrojích spolehlivě doloženo**. Databázové pole `version: 2` označuje formát úložiště, **nikoli verzi aplikace**.

## Původní úplný archiv `filamenty.zip`

Obsahuje veřejný a správcovský přehled, souborovou databázi, historii změn, rychlý pětimístný zápis množství, filtrování a řazení, fotografie, CSS a JavaScript vložené do PHP, exporty CSV/PDF, volitelné e-mailové ZIP zálohy a nastavení veřejné viditelnosti.

## Poslední dostupná aktualizace `Filamenty_HEX_aktualizace.zip`

Doložené změny ve třech souborech `id/core.php`, `id/view.php`, `id/report.php`:

- Uložení volitelného HEX odstínu filamentu a režimu zobrazení `photo` / `hex`.
- Přepínač fotografie / barevný kruh na kartě filamentu a dočasný přepínač pro celý přehled.
- Ruční výběr HEX, orientační odhad z fotografie a odhad podle názvu.
- Zápis HEX i režimu do historie změn.
- Přidání sloupců `HEX barva` a `Zobrazení vzorku` do CSV exportu (PDF zůstává bez těchto strojových sloupců).

## Sestavení pro veřejné zveřejnění

- Zkompletovány poslední aktualizované soubory se zbývajícím původním kódem.
- Vyloučena osobní databáze se skutečnými zásobami, historie, soukromá nastavení, fotografie a zálohový status.
- Přidány prázdné PHP šablony dat, `.gitignore` a ukázkové **vymyšlené** CSV pro kontrolu propojení s Pythonem.
- Výchozí ochrana správy pomocí `id/.htaccess` (odmítnutí přístupu), příklad zabezpečení Basic Auth a český návod.
- Výchozí odesílatel záloh v `id/core.php` nahrazen neutrálním `webmaster@example.org`.
- Zaznamenáno, že PDF vyžaduje zvlášť dodaný `DejaVuSans.ttf`, který nebyl v přiloženém originálním ZIPu.

Přehled obsahuje pouze doložené změny a anonymizační/instalační úpravy. Žádné další historické verze PHP aplikace nebyly spolehlivě identifikovány.
