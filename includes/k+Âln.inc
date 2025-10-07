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
                   
    Müllkalender der Stadt Köln. Passe Deine Adresse wie folgt an:
    
    Öffne im Browser die Seite  "https://www.awbkoeln.de/"
    und gib Deine Adresse ein.
    Drücke die Schaltfläche "Termine für Ihren Kalender (ICS-Datei)".
    Anschließend wählst Du die Abfallart(en).
    Anschließen drücke die Schaltfläche "Termine herunterladen".
    Anschließend stellst Du den Cursor auf die gewünschte Schaltfläche
    und drückst dien rechte Maustaste. Wähle "Link-Adresse kopieren".
    Füge die Linkadresse bei $icsdat = ""; zwischen den beiden Anführungszeichen ein 
    
*/

//$icsdat = "https://www.awbkoeln.de/fileadmin/ics/icscal.php?building_number=1&street_code=601&start_year=2025&end_year=2025&start_month=1&end_month=12&trigger=960&wertstoff=1&grey=1&brown=1&blue=1&red=1&xmastree=1";
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
// Kölner Müllarten
$bilder["Bio (braun)"] = $bpath.'bio.png';
$bilder["Restmuell (grau)"] = $bpath.'rest.png';
$bilder["Papier (blau)"] = $bpath.'papier.png';
$bilder["Wertstoff (gelb)"] = $bpath.'gelbersack.png';

// Korrekturen bei der Abfallart
// Im Müllkalender der Stadt steht für die Müllart beispielsweise: 
// "Bio-Tonne Bezirk B-3, S-3 Di"
// Das wird in "Bio-Tonne" korrigiert 
function aart_korr($text)
{
    $ergebnis = "";
    $temp = str_replace('AWB KÃ¶ln', '', $text); 
    if (strpos($temp,",") !== false)
    {
        $ergebnis = explode(',', $temp);
        while (count($ergebnis) < 4)
        {
            $ergebnis[] = "";
        }
    }     
    else
    {
        $ergebnis = trim($temp);
    }    
    return $ergebnis;
}

// Die Informationen für die Abfallart lesen
// Die Beschreibung steht ab "DESCRIPTION:" bis zum nächsten Tag
// Bei Fehler die iCal Datei prüfen und die Begiffe anpassen
// Achtung: Die Tags müssen hinter "DESCRIPTION" vorkommen, sonst klappt es nicht!
function minfo_lesen($text)
{
    preg_match_all('/DESCRIPTION:(.*)BEGIN:VALARM|END:/s', $text, $info_array);
    
    if (empty($info_array[0]) OR (strpos($text, 'DESCRIPTION:') === false))
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