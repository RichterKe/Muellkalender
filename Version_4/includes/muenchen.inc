<?php
/*
    Include Datei für den Müllkalender Version 4
    
    $icsdat   - Link zu Deiner ICAL Datei (Siehe Deine Stadtverwaltung)
    $dpath    - Relative Verzeichnisadresse zu den Daten/Programmen
    $bpath    - Relative Verzeichnisadresse zu den Bilddateien
    $anztage  - Anzahl Tage die ab heute angezeigt werden
    $ar_wtage - Wochentage je nach Wunsch abgekürzt oder ausgeschrieben
    $bilder   - Zuweisung eines Bildes zur Müllart
                (Die Namen der Müllarten findest Du in Deiner ICAL Datei
                 zwischen "BEGIN;VEVENT" und "END:VEVENT" unter "SUMMARY:" )  
                   
    Müllkalender der Stadt München. Beispieladresse ist das Polizeirevier in der Beethovenstr 5
    Passe Deine Adresse wie folgt an:
    
    Öffne im Browser die Seite  "https://www.awm-muenchen.de/abfall-entsorgen/muelltonnen/abfuhrkalender"
    und gib Deine Adresse ein.
    Drücke die Schaltfläche "weiter". Gehe unten zur Schaltfläche "Download ICS-Datei"
    klicke mit der rechten Maustaste darauf und wähle "Linkadresse kopieren"
    Danach fügst Du den Link in der Zeile  $icsdat = ""; zwischen den beiden Anführungszeichen ein.   
*/

//$icsdat = "https://www.awm-muenchen.de/abfall-entsorgen/muelltonnen/abfuhrkalender?tx_awmabfuhrkalender_abfuhrkalender%5Bhausnummer%5D=5&tx_awmabfuhrkalender_abfuhrkalender%5Bleerungszyklus%5D%5BB%5D=1%2F2%3BU&tx_awmabfuhrkalender_abfuhrkalender%5Bleerungszyklus%5D%5BP%5D=&tx_awmabfuhrkalender_abfuhrkalender%5Bleerungszyklus%5D%5BR%5D=001%3BU&tx_awmabfuhrkalender_abfuhrkalender%5Bsection%5D=ics&tx_awmabfuhrkalender_abfuhrkalender%5Bsinglestandplatz%5D=false&tx_awmabfuhrkalender_abfuhrkalender%5Bstandplatzwahl%5D=true&tx_awmabfuhrkalender_abfuhrkalender%5Bstellplatz%5D%5Bbio%5D=70062049&tx_awmabfuhrkalender_abfuhrkalender%5Bstellplatz%5D%5Bpapier%5D=&tx_awmabfuhrkalender_abfuhrkalender%5Bstellplatz%5D%5Brestmuell%5D=70062049&tx_awmabfuhrkalender_abfuhrkalender%5Bstrasse%5D=Beethovenstr.&tx_awmabfuhrkalender_abfuhrkalender%5Byear%5D=2025&cHash=f261299b91d7e3f012484aecf807fbc1";
$icsdat = "";


$dpath = "";
$bpath = "bilder/";
$anztage = 31;
$ar_wtage = array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag");
//$ar_wtage = array("So", "Mo", "Di", "Mi", "Do", "Fr", "Sa");

$bilder["Bio-Tonne"] = $bpath.'bio.png';
$bilder["Restmuell"] = $bpath.'rest.png';
$bilder["Blaue Tonne"] = $bpath.'papier.png';
$bilder["Gruenschnittannahmestelle"] = $bpath.'gruenschnitt.png';
$bilder['Muellsammelaktion "Sauberes Kelkheim"'] = $bpath.'muellsammel.png';
$bilder["Sondermuell"] = $bpath.'sonder.png';   
$bilder["Wertstoffhof"] = $bpath.'recycling.png';  
$bilder["Restmuell-Container"] = $bpath.'container.png';  
$bilder["Gelber Sack"] = $bpath.'gelbersack.png';  
$bilder["Repair-Café"] = $bpath.'repair.png';
$bilder["Sperrmuell"] = $bpath.'sperr.png';
// Münchener Müllarten
$bilder["Biotonne"] = $bpath.'bio.png';
$bilder["Restmuelltonne"] = $bpath.'rest.png';

// Korrekturen bei der Abfallart
// Im Müllkalender der Stadt steht für die Müllart beispielsweise: 
// "Bio-Tonne Bezirk B-3, S-3 Di"
// Das wird in "Bio-Tonne" korrigiert 
function aart_korr($text)
{
    $ergebnis = "";
    $temp = explode(',', $text);
    $text = $temp[0];
    $temp = str_replace('Achtung: ', '', $text);    
    $ergebnis = trim($temp);
    return $ergebnis;
}

// Die Informationen für die Abfallart lesen
// Die Beschreibung steht ab "DESCRIPTION:" bis zum nächsten Tag
// Bei Fehler die iCal Datei prüfen und die Begiffe anpassen
// Achtung: Die Tags müssen hinter "DESCRIPTION" vorkommen, sonst klappt es nicht!
function minfo_lesen($text)
{
    preg_match_all('/DESCRIPTION:(.*)STATUS:|END:/s', $text, $info_array);
    
    if (empty($info_array[0]) OR (strpos($text, 'DESCRIPTION:') == false))
    {
        $info_array = array();
        $info_array[1][0] = "* Keine Information vorhanden *";
    }
    
    return $info_array;
}

    
// Bilder finden und anzeigen
// Änderungen optional    
function bilder_finden($text, $inf)
{
    global $dpath, $bpath;
    global $bilder;
    $text = trim($text);
    $text = str_replace("GruÌˆner", "Gruener", $text);
    $text = iconv("UTF-8", "Windows-1252", $text);
    $text = str_replace("ü", "ue", $text);
    $ergebnis = "&nbsp;"; 
    
    if (array_key_exists($text, $bilder))
    {
        $ergebnis = '<img src="'.$bilder[$text].'" alt="'.$text.'" title="'.$inf.'" border=0 width="" height="">';
    }                                 

    //<img src="bilder/papier.png" alt="" border="0" width="" height="">
    if ($text !== "Blaue Tonne")
    {
        $text = str_replace("ue", "&uuml;", $text);
    }
    $text = str_replace("é", "&eacute;", $text);    
    echo ($text);
    return $ergebnis;
}
       
?>     