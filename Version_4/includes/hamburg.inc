<?php
/*
    Include Datei f�r den M�llkalender Version 4
    
    $icsdat   - Link zu Deiner ICAL Datei (Siehe Deine Stadtverwaltung)
    $dpath    - Relative Verzeichnisadresse zu den Daten/Programmen
    $bpath    - Relative Verzeichnisadresse zu den Bilddateien
    $anztage  - Anzahl Tage die ab heute angezeigt werden
    $ar_wtage - Wochentage je nach Wunsch abgek�rzt oder ausgeschrieben
    $bilder   - Zuweisung eines Bildes zur M�llart
                (Die Namen der M�llarten findest Du in Deiner ICAL Datei
                 zwischen "BEGIN;VEVENT" und "END:VEVENT" unter "SUMMARY:" )  
                   
    M�llkalender der Stadt Hamburg. Passe Deine Adresse wie folgt an:
    
    �ffne im Browser die Seite  "https://www.stadtreinigung.hamburg/abfuhrkalender/"
    und gib Deine Adresse ein.
    Hake das Feld "Berechtigung" an und dr�cke die Schaltfl�che "Termine suchen".
    Anschlie�en dr�cke die Schaltfl�che "Kalender abbonieren" dann die Schaltfl�che
    "Kalenderdatei herunterladen".
    Speichere die Datei im Unterverzeichnis "ics_dateien" unter dem Namen "hamburg.ics".
    
*/

//$icsdat = "https://www.fes-frankfurt.de/abfallkalender/R29sZHN0ZWluc3RyLnwxMjZ8NjA1Mjg%3D.ics";
$icsdat = "ics_dateien/hamburg.ics";


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
$bilder["Repair-Caf�"] = $bpath.'repair.png';
$bilder["Sperrmuell"] = $bpath.'sperr.png';
// Hamburgerer M�llarten
$bilder["gelbe Wertstofftonne/-sack"] = $bpath.'gelbersack.png';
$bilder["schwarze Restmuelltonne"] = $bpath.'rest.png';

// Korrekturen bei der Abfallart
// Im M�llkalender der Stadt steht f�r die M�llart beispielsweise: 
// "Bio-Tonne Bezirk B-3, S-3 Di"
// Das wird in "Bio-Tonne" korrigiert 
function aart_korr($text)
{
    $ergebnis = "";
    //$temp = explode('|', $text);
    //$ergebnis = $temp[0];
    $temp = str_replace('Abfuhr ', '', $text);    
    $ergebnis = trim($temp);
    return $ergebnis;
}

// Die Informationen f�r die Abfallart lesen
// Die Beschreibung steht ab "DESCRIPTION:" bis zum n�chsten Tag
// Bei Fehler die iCal Datei pr�fen und die Begiffe anpassen
// Achtung: Die Tags m�ssen hinter "DESCRIPTION" vorkommen, sonst klappt es nicht!
function minfo_lesen($text)
{
    preg_match_all('/DESCRIPTION:(.*)LOCATION:|TRIGGER:|END:/s', $text, $info_array);
    
    if (empty($info_array[0]) OR (strpos($text, 'DESCRIPTION:') == false))
    {
        $info_array = array();
        $info_array[1][0] = "* Keine Information vorhanden *";
    }
    
    return $info_array;
}

    
// Bilder finden und anzeigen
// �nderungen optional    
function bilder_finden($text, $inf)
{
    global $dpath, $bpath;
    global $bilder;
    $text = trim($text);
    $text = str_replace("Grüner", "Gruener", $text);
    $text = iconv("UTF-8", "Windows-1252", $text);
    $text = str_replace("�", "ue", $text);
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
    $text = str_replace("�", "&eacute;", $text);    
    echo ($text);
    return $ergebnis;
}
       
?>     