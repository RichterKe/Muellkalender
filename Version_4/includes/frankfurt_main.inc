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
                   
    M�llkalender der Stadt Frankfurt. Beispieladresse ist das Polizeirevier in der
    Goldsteinstra�e 126 passe Deine Adresse wie folgt an:
    
    �ffne im Browser die Seite  "https://www.fes-frankfurt.de/services/abfallkalender"
    und gib Deine Adresse ein.
    Klicke auf das Symbol "Kalender" und kopiere den Link mit der Schaltfl�che "Link kopieren"
    in die Zwischenablage.
    Danach f�gst Du den Link in der Zeile  $icsdat = ""; zwischen den beiden Anf�hrungszeichen ein.
    
    Die Kommentarzeichen am Zeilenanfag l�schen und die Beispielzeile auskommentieren mit "//"
    
*/

//$icsdat = "https://www.fes-frankfurt.de/abfallkalender/R29sZHN0ZWluc3RyLnwxMjZ8NjA1Mjg%3D.ics";
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
$bilder["Repair-Caf�"] = $bpath.'repair.png';
$bilder["Sperrmuell"] = $bpath.'sperr.png';
// Frankfurter M�llarten
$bilder["Bioabfall"] = $bpath.'bio.png';
$bilder["Altpapier"] = $bpath.'papier.png';
$bilder["Restabfall 1. Leerung"] = $bpath.'rest.png';
$bilder['Restabfall 2. Leerung (falls beantragt)'] = $bpath.'rest.png';
$bilder['Verpackungen / Gruener Punkt'] = $bpath.'gruenerpunkt.png';

// Korrekturen bei der Abfallart
// Im M�llkalender der Stadt steht f�r die M�llart beispielsweise: 
// "Bio-Tonne Bezirk B-3, S-3 Di"
// Das wird in "Bio-Tonne" korrigiert 
function aart_korr($text)
{
    $ergebnis = "";
    $temp = explode('|', $text);
    $ergebnis = $temp[0];
    return $ergebnis;
}

// Die Informationen f�r die Abfallart lesen
// Die Beschreibung steht ab "DESCRIPTION:" bis zum n�chsten Tag
// Bei Fehler die iCal Datei pr�fen und die Begiffe anpassen
// Achtung: Die Tags m�ssen hinter "DESCRIPTION" vorkommen, sonst klappt es nicht!
function minfo_lesen($text)
{
    preg_match_all('/DESCRIPTION:(.*)SUMMARY:|TRANSP:|END:/s', $text, $info_array);
    
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