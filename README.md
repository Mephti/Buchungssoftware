## „PHP Script“-Run Configuration

1. Öffne dein Projekt in PhpStorm.

2. Oben rechts bei den Run-Konfigurationen: Add Configuration… (oder Dropdown → Edit Configurations…)

3. Klick + → wähle PHP Script.

4. Setze die Felder so:

   - Name: CI4 Serve

   - File:
   Wähle die Datei "spark" im Projekt-Root (ohne Endung)
   also: …\mein-ci4-projekt\spark

   - Arguments: serve

   - Interpreter: dein XAMPP-PHP (C:\xampp\php\php.exe)

Apply → OK.


## Projekt in PHPStorm laden ##
### PHP-Interpreter und Composer setzen

In Settings → PHP:

- CLI Interpreter auf die lokale php.exe setzen (z. B. C:\xampp\php\php.exe)

### Composer Komponenten installieren

PHPStorm Terminal anklicken:
- "composer install"
- Im Fehlerfall muss wahrscheinlich "composer update" ausgeführt werden.
   - Sollte dies auch fehlschlagen, in der php.ini zuerst "extension=intl" und "extension=zip" einkommentieren

### env im Projekt einrichten

env → umbenennen in .env (falls .env nicht existiert)

Die folgenden Einträge sollten einkommentiert werden:
 database.default.hostname = localhost
 database.default.database = plauersee
 database.default.username = root
 database.default.password =
 database.default.DBDriver = MySQLi
 database.default.port = 3306

 ### Datenbank laden
 In phpMyAdmin eine Datenbank namens "plauersee" erstellen.
 Datenbank auswählen und im Reiter "Importieren" aus dem Projektordner app/Database/Exports/plauersee.sql laden.
