<h3>Müllkalender Version 5</h3>   
Ein PHP-Programm um die Daten für die Müllentsorgung aus einer iCal Datei zu lesen und auf einer Webseite darzustellen.  
Die Version 5 kann über einen Docker Container gestartet werden. Der Start über die PHP Dateien ist immer noch möglich.  
Die Datei mkalender4.php wurde in index.php umbenannt.  

<b>Programm starten:</b>  
Die Datei "docker-compose.yaml" in ein Verzeichnis auf dem Docker Host kopieren.  
Starten des Containers mit "docker compose up -d".  
  
<b>Programm beenden:</b>  
Beenden des Containers mit "docker compose down".  

<b>Programm einrichten:</b>  
Das Programm legt auf dem Host ein Verzeichnis mit dem Namen "abfuhrdaten" an.  
In diesem Verzeichnis werden die Einstellungen abgelegt und sind beim nächsten Start wieder verfügbar.  
Die Einstellungen erreichst Du in dem Du auf der HTML-Seite auf die Schrift "Müll Entsorgungstermine" klickst.  
Nachdem alle Einstellungen gemacht sind müssen diese mit der Schaltfläche "absenden" gespeichert werden.  
  
<b>iCal Datei finden:</b>  
Auf den Seiten der Stadtverwaltung kann man sich einen Müllkalender herunterladen.  
Meistens findet man dort auch den Namen bzw. den Link für die iCal Datei.  
Dieser Link muss unter "[programm]" in den Einstellungen hinter "ical" eingegeben werden.  
Wenn Du eine heruntergeladene iCal Datei hast z.B mit dem Namen "muellabfuhr.ics" dann kannst Du  
die in das Verzeichnis "abfuhrdaten" auf dem Docker Host kopieren. In der Konfiguration trägst  
Du ein ical = 'daten/muellabfuhr.ics'  
  
<b>Bilder zuordnen:</b>  
Unter "[bilder]" kannst Du den verschiedenen Müllarten ein Symbol zuordnen.  
Alle Symbole haben die Größe 148 x 148 Pixel.  
Quelle:Lizenzfreie Bilder aus der Google suche (die Bilder wurden in Größe und Farbe verändert).  
Du kannst selbst erzeugte Bilddateien (Größe beachten) in das Verzeichnis "abfuhrdaten" laden, oder vorhandene Ändern.  
  
<b>Verzeichnis "abfuhrdaten":</b>  
Wenn Du aus diesem Verzeichnis eine (voreingestelle) Datei entfernst wird diese beim Start wieder nachgeladen.  
  
<b>Inhalt von "<Dockerverzeichnis>/abfuhrdaten/"</b>  
<table border="0px">
  <tr>
    <td>mkalender5.dat </td>  
    <td>Programmeinstellungen</td>
  </tr>
  <tr>
    <td>bio.png</td>  
    <td>Mülltonne (braun)</td>
  </tr>  
 

Bei Rückfragen erreicht ihr mich im Heimnetzforum    https://forum.heimnetz.de/threads/php-muellkalender.6841/

