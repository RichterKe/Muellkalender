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
                   
    Müllkalender der Stadt Stuttgart. Passe Deine Adresse wie folgt an:
    
    Öffne im Browser die Seite  "https://service.stuttgart.de/lhs-services/aws/jahreskalender"
    und gib Deine Adresse ein anschließend wählst Du die Abfallart(en).
    Drücke die Schaltfläche "iCal-Link erstellen".
    Kopiere den Link und füge die Linkadresse bei $icsdat = ""; 
    zwischen den beiden Anführungszeichen ein. 
    
*/

//$icsdat = "https://service.stuttgart.de/lhs-services/aws/api/ical?street=Franziskaweg&streetnr=1";
$icsdat = "";


$dpath = "";
$bpath = "bilder/";
$anztage = 131;
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
// Stuttgarter Müllarten
$bilder["Biomuell"] = $bpath.'bio.png';
$bilder["Restmuell"] = $bpath.'rest.png';
$bilder["Altpapier"] = $bpath.'papier.png';
$bilder["Gelber Sack"] = $bpath.'gelbersack.png';

// Korrekturen bei der Abfallart
// Im Müllkalender der Stadt steht für die Müllart beispielsweise: 
// "Bio-Tonne Bezirk B-3, S-3 Di"
// Das wird in "Bio-Tonne" korrigiert 
function aart_korr($text)
{
    $ergebnis = "";
    $temp = str_replace('01-wÃ¶chentl.', '', $text); 
    $temp = str_replace('02-wÃ¶chentl.', '', $temp);
    $temp = str_replace('03-wÃ¶chentl.', '', $temp);  
    $temp = str_replace('04-wÃ¶chentl.', '', $temp);          
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
    preg_match_all('/DESCRIPTION:(.*)TRANSP:|ACTION:|END:/s', $text, $info_array);
    
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
