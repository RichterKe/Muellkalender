<?php
/*
    Müllkalender der Stadt lesen (iCal-Format)
    Version 2
    
*/

/* ################################################################
   # Globale Variablen                                            #
   ################################################################ */    

// ** Zu ändernde Variablen **

//
// $icsdat  - Link zu Deiner ICAL Datei (Siehe Deine Stadtverwaltung)
// $dpath   - Relative Verzeichnisadresse zu den Daten/Programmen
// $bpath   - Relative Verzeichnisadresse zu den Bilddateien
// $anztage - Anzahl Tage die ab heute angezeigt werden
// $bilder  - Zuweisung eines Bildes zur Müllart
//            (Die Namen der Müllarten findest Du in Deiner ICAL Datei
//             zwischen "BEGIN;VEVENT" und "END:VEVENT" unter "SUMMARY:" )


$icsdat = "https://kelkheim.de/mod_abfallkalender/index.php?action=ical&area=B-3%2C+S-3+Di&datetype=Restm%FCll%2CBlaue+Tonne%2CBio-Tonne%2CGelber+Sack%2CSonderm%FCll%2CSperrm%FCll%2CGr%FCnabfuhr%2CRestm%FCll-Container%2CGr%FCnschnittannahmestelle%2CWertstoffhof%2CSonstige&street=Gagernring&number=6";

$dpath = "";
$bpath = "bilder/";
$anztage = 30;
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

// ** Änderungen Ende **


$termin = array();
$eintrag = array();
$mdatum = array();
$mtyp = array();
$minfo = array();
$ar_termine = array();
$ar_infos = array();
$fehler = 0;
$tagsek = 86400;

/* ################################################################
   # Unterprogramme                                               #
   ################################################################ */ 
   
// Terminkalender generiren   
function termin_finden()
{
    global $icsdat, $ar_termine, $ar_infos;
    global $fehler;
    $adat = "";
    $aart = "";
    $ainf = "";
    $datum = "";
    $index = 0;
    $temp = "";
    
    // iCal Datei in die Variable $inhalt lesen
    $inhalt = file_get_contents($icsdat);

    // Die einzelnen Termine in ein Array hineinschreiben
    // Die Termine stehen zwischen "BEGIN:VEVENT" und "END:VEVENT"
    preg_match_all('/BEGIN:VEVENT.*END:VEVENT/sU', $inhalt, $termin);


    // Wenn Termine gefunden wurden dann alle durchgehen
    if (!empty($termin[0]))
    {
        foreach ($termin[0] as $terdat)
        {
            // Das Datum ist die Ziffernfolge hinter "DTSTART
            preg_match_all('/DTSTART\D*(\d*)/', $terdat, $mdatum); 
            
            // Der Mülltyp ist die Ziffernfolge hinter "SUMMARY:"   
            preg_match_all('/SUMMARY:(.*)/', $terdat, $mtyp);
            
            // Die Beschreibung steht ab "DESCRIPTION:" bis zum nächsten Tag
            // Bei Fehler die iCal Datei prüfen und die Begiffe anpassen
            // Achtung: Die Tags müssen hinter "DESCRIPTION" vorkommen, sonst klappt es nicht!
            preg_match_all('/DESCRIPTION:(.*)SUMMARY:|TRANSP:|END:/s', $terdat, $minfo);
            
            // Datum formatieren und Ergebnisse in Variablen schreiben 
            $adat = (date("d.m.Y", strtotime($mdatum[1][0])));
            $aart = $mtyp[1][0];
            // Optionale Korrektur der Müllart
            $temp = explode('Bezirk', $aart);
            $aart = $temp[0];
            // Option Ende
            $ainf = $minfo[1][0];  
            
            // Wenn das Datum wechselt ein Array mit 4 Einträgen anlegen
            // Für die Müllkarten und die Informationen
            // Pro Termin werden maximal 4 Müllarten verwaltet
            if ($datum !== $adat)
            {
                $datum = $adat;
                $index = 0;
                $ar_termine[$adat] = array("", "", "", "");
                $ar_infos[$adat] = array("", "", "", "");                
            } 
            
            // Maximal 4 Müllarten eintragen
            if ($index <= 3)
            {
                $ar_termine[$adat][$index] =  $aart;
                //$ar_infos[$adat][$index] = preg_replace('/\s+/', ' ', str_replace("\\n", " ", $ainf));
                $ar_infos[$adat][$index] = preg_replace('/\s+/', ' ', str_replace("\\n", "&#10", $ainf));
                $index += 1;
            }         
        }   
     }
     else
     {
        // ICS-Datei nicht gefunden oder falsche Datei
        $fehler = 1;
     } 

}

function bilder_finden($text, $inf)
{
    global $dpath, $bpath;
    global $bilder;
    $text = trim($text);
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

function anzeige_termine()
{   
    global $ar_termine, $tagsek;
    global $anztage, $ar_infos;
    $feld1 = "";
    $feld2 = "";
    $feld3 = "";
    $feld4 = "";
    ?>
      <!DOCTYPE HTML>
      <html lang="de">
        <head>
          <meta charset="utf-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <meta http-equiv="refresh" content="60; URL=mkalender2.php">
          <title>Muellkalender</title>
        </head>
      
        <style>
          body              { min-width:50px; max-width:1900px; }
          div               { display:flex; flex-flow:row wrap; }
          button            { font:normal bold 14px Arial; margin:0px; padding:0px; padding-top:10px;
                              border:1px solid #AAAAAA; border-radius:4px; width:80px; height:80px;
                              background: linear-gradient(to bottom, #F2F2F2, #F2F2F2 49%, #E2E2E2 51%, #E2E2E2 100%);}
          div iframe        { pointer-events:none; margin:0px; padding:0px; border:0px; width:60px; height:60px; }
  
          /* CLASS */
          .sensor           { pointer-events:none; margin:0px; padding:0px; border:0px; width:88px; height:83px; }
          .zeit             { margin:0px; padding:0px; border:0px; width:300px; height:25px; }
  
          /* ID */
          #rahmen           { border-top:1px solid #1683CE; }
          #titel            { font-size:24px; font-weight:bold; word-wrap:normal; white-space:normal; }
          #taba             { border:3px; }
          #tabtra           { height:20px; margin:2px; }
          #tabtda           { width:340px; text-align:center; }
          #tabi             { border:0px; }
          #tabtri           { height:180px;  margin:2px; }
          #tabtdi           { width:180px; text-align:center; background-color:#EEEEEE; }
    
        </style>  
        <body>
          <div> 
            <?php
              for ($i = 1; $i <= $anztage; $i++)
              {
                $datum = (date("d.m.Y", (time()+($i*$tagsek))));
                if (array_key_exists($datum,$ar_termine))
                {
                  $feld1 = ($ar_termine[$datum][0]);
                  $feld2 = ($ar_termine[$datum][1]);  
                  $feld3 = ($ar_termine[$datum][2]);               
                  $feld4 = ($ar_termine[$datum][3]);
                  $feld5 = ($ar_infos[$datum][0]);
                  $feld6 = ($ar_infos[$datum][1]);  
                  $feld7 = ($ar_infos[$datum][2]);               
                  $feld8 = ($ar_infos[$datum][3]);    
                }
                else
                {
                  $feld1 = "&nbsp;";
                  $feld2 = "&nbsp;";
                  $feld3 = "&nbsp;";    
                  $feld4 = "&nbsp;";   
                  $feld5 = "&nbsp;";
                  $feld6 = "&nbsp;";
                  $feld7 = "&nbsp;";    
                  $feld8 = "&nbsp;";                             
                }
                ?>
                <table border="1px" ID="taba">
                <tr ID="tabtra" >
                <td ID="tabtda">
                <?php echo ($datum); ?>
                </td>
                </tr>
                <tr>
                <td>
                <table ID="tabi">
                <tr ID="tabtri" >
                <td ID="tabtdi"><?php echo (bilder_finden($feld1, $feld5)); ?></td>
                <td ID="tabtdi"><?php echo (bilder_finden($feld2, $feld6)); ?></td>
                </tr>
                <tr ID="tabtri" >
                <td ID="tabtdi"><?php echo (bilder_finden($feld3, $feld7)); ?></td>
                <td ID="tabtdi"><?php echo (bilder_finden($feld4, $feld8)); ?></td>
                </tr>
                </table>
                </td>
                </tr>
                </table>  
              <?php                        
              }   
              ?>           
                </div> 
                </body>
                </html> 
               <?php 

}     

function anzeige_fehler()
{
   global $icsdat;

    ?>
      <!DOCTYPE HTML>
      <html lang="de">
        <head>
          <meta charset="utf-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Muellkalender</title>
        </head>
      
        <style>
          body              { min-width:50px; max-width:1900px; }
          div               { display:flex; flex-flow:row wrap; }
          button            { font:normal bold 14px Arial; margin:0px; padding:0px; padding-top:10px;
                              border:1px solid #AAAAAA; border-radius:4px; width:80px; height:80px;
                              background: linear-gradient(to bottom, #F2F2F2, #F2F2F2 49%, #E2E2E2 51%, #E2E2E2 100%);}
          div iframe        { pointer-events:none; margin:0px; padding:0px; border:0px; width:60px; height:60px; }
  
          /* CLASS */
          .sensor           { pointer-events:none; margin:0px; padding:0px; border:0px; width:88px; height:83px; }
          .zeit             { margin:0px; padding:0px; border:0px; width:300px; height:25px; }
  
          /* ID */
          #rahmen           { border-top:1px solid #1683CE; }
          #titel            { font-size:24px; font-weight:bold; word-wrap:normal; white-space:normal; }

    
        </style>  
        <body>
          <h1>
            Es ist ein Fehler aufgetreten:<br><br>
            Die Datei <?php echo ($icsdat); ?> ist nicht vorhanden oder<br>
            hat ein falsches Format!
          </h1>
        </body>
      </html> 
    <?php 

}  


/* ################################################################
   # Hauptprogramm                                                #
   ################################################################ 
*/ 

termin_finden();

// Alles ok, Müllkalender anzeigen
if ($fehler == 0)
{
    anzeige_termine();
}
// Das war nix - Fehlermeldung
if ($fehler == 1)
{
    anzeige_fehler();
}



?>