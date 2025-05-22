Ein PHP-Programm um die Daten für die Müllentsorgung aus einer iCal Datei zu lesen und auf einer Webseite darzustellen.

Auf den Seiten der Stadtverwaltung kann man sich einen Müllkalender herunterladen. Meistens findet man dort auch den Name bzw. den Link
für die iCal Datei.

Die notwendigen Änderungen im Programm werden auf der Ersten Seite vorgenommen und sind kommentiert. Die Namen der Müllarten sind in der iCal Datei zu finden.
Jeder Abfuhrtermin ist zwischen den Zeilen "BEGIN:VEVENT" und "END:VEVENT beschrieben. Der Name der Abfuhr ist unter "SUMMARY:" zu finden.

Alle Symbole haben die Größe 148 x 148 
Quelle:Lizenzfreie Bilder aus der Google suche  (die Bilder wurden in der Größe verändert).

Installation:
Auf einem Raspberry Pi mit Apache Webserver, auf einem PC mit XAMPP oder Engine X.
Die Verzeichnisstruktur so lassen Bilder ist ein Unterverzeichnis des Programmverzeichnisses.
