<h3>Müllkalender Version 2</h3>
Ein PHP-Programm um die Daten für die Müllentsorgung aus einer iCal Datei zu lesen und auf einer Webseite darzustellen.
Die Webseite ist für Mobilgeräte optimiert.

Auf den Seiten der Stadtverwaltung kann man sich einen Müllkalender herunterladen. Meistens findet man dort auch den Name bzw. den Link
für die iCal Datei.

Alle Symbole haben die Größe 148 x 148 
Quelle:Lizenzfreie Bilder aus der Google suche  (die Bilder wurden in der Größe verändert).

Installation:
Auf einem Raspberry Pi mit Apache Webserver, auf einem PC mit XAMPP oder Engine X.
Die Verzeichnisstruktur so lassen Bilder ist ein Unterverzeichnis des Programmverzeichnisses.

Änderungen zu Version 1:
Es wird keihe Datei mehr abgelegt falls die Daten auf einer SD-Karte liegen (Raspberry Pi).
Es wurden mehr Kommentare geschrieben.
Lesen und Anzeige der Infotexte aus der ICAL Datei wurde optimiert.
Es erfolgt eine Fehlermeldung bei einem falschen Dateiformat.

Die notwendigen Änderungen im Programm werden auf der Ersten Seite vorgenommen und sind kommentiert. 
Die Namen der Müllarten sind in der iCal Datei zu finden. Jeder Abfuhrtermin ist zwischen den Zeilen "BEGIN:VEVENT" und "END:VEVENT beschrieben. 
Der Name der Abfuhr ist unter "SUMMARY:" zu finden.  

Bei Rückfragen erreicht ihr mich im Heimnetzforum    https://forum.heimnetz.de/threads/php-muellkalender.6841/

