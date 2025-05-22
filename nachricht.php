<?php
/*
    Müllkalender der Stadt lesen (iCal-Format)
    und Nachricht(en) generieren
    
*/

/* ################################################################
   # Globale Variablen                                            #
   ################################################################ */    

// ** Zu ändernde Variablen **
//
// $_url    - Link zu Deiner ICAL Datei (Siehe Deine Stadtverwaltung)
// $dpath   - Relative Verzeichnisadresse zu den Daten/Programmen
// $bpath   - Relative Verzeichnisadresse zu den Bilddateien
// $anztage - Anzahl Tage die ab heute angezeigt werden
// $bilder  - Zuweisung eines Bildes zur Müllart
//            (Die Namen der Müllarten findest Du in Deiner ICAL Datei
//             zwischen "BEGIN;VEVENT" und "END:VEVENT" unter "SUMMARY:" )
//
// Ab Zeile 146 muss eventuell die Anzahl der Zeilen "$moretx .= fgets($handle, 1024);"
// erhöht oder erniedrigt werden. (Infotexte zu den Icons prüfen)
//
$_url = "https://kelkheim.de/mod_abfallkalender/index.php?action=ical&area=B-8%2C+S-8+Mi&datetype=Restm%FCll%2CBlaue+Tonne%2CBio-Tonne%2CGelber+Sack%2CSonderm%FCll%2CSperrm%FCll%2CGr%FCnabfuhr%2CRestm%FCll-Container%2CGr%FCnschnittannahmestelle%2CWertstoffhof%2CSonstige&street=Im+Kleinen+Grund&number=23";
$dpath = "";
$bpath = "bilder/";
$anztage = 30;
$bilder["Bio-Tonne"] = $bpath.'bio.png';
$bilder["Restmüll"] = $bpath.'rest.png';
$bilder["Blaue Tonne"] = $bpath.'papier.png';
$bilder["Gruenschnittannahmestelle"] = $bpath.'gruenschnitt.png';
$bilder['Muellsammelaktion "Sauberes Kelkheim"'] = $bpath.'muellsammel.png';
$bilder["Sondermuell"] = $bpath.'sonder.png';   
$bilder["Wertstoffhof"] = $bpath.'recycling.png';  
$bilder["Restmuell-Container"] = $bpath.'container.png';  
$bilder["Gelber Sack"] = $bpath.'gelbersack.png';  
$bilder["Repair-Café"] = $bpath.'repair.png';
// ** Änderungen Ende **

$pgm_Call = 0;                          // 1=HTML, 2=Kommandozeile
$handle = NULL;
$buffer = "";
$start = 0;
$zeile = "";
$status = 0;
$start = "";
$muell = "";
$info = NULL;
$ar_termine = NULL;
$tagsek = 86400;


/* ################################################################
   # Unterprogramme                                               #
   ################################################################ */ 

/* Funktion zum zerlegen einer Textzeile in die einzelnen Bestandteile
   und Rückgabe der Teile in einem Array, Element 0 enthält die Anzahl der Einträge */
function teile_text($text)
{
  $tok = "";
  $index = 0;
  $respond = array("0" => "0");
  $tok = strtok($text," \t");
  while ($tok !== false)
  {
    $index += 1;
    $respond[$index] = trim($tok);
    $respond[0] = $index;
    if ($index >= 8) break;
    $tok = strtok(" \t");
  }
  return $respond;
}     
   

/* Kalender Array generieren */
function seite_lesen()
{
    global $buffer;
    global $_url;

    $buffer = implode('', file($_url));
    //$buffer = strip_tags(implode('', file($_url)));    
}
                 
function termin_finden()
{
    global $handle, $buffer;
    global $zeile, $status;
    global $start, $muell;
    global $ar_termine; 
    global $dpath, $bpath;
    global $info;

    $temp = "";
    $blabla = "";
    $termine = NULL;
    
    $handle = fopen($dpath."nachricht.txt","w+");
    fputs($handle,$buffer);
    fclose($handle);   

    $handle = fopen($dpath."nachricht.txt","r+");
    $status = 0;
    $anzahl = 0;
    $index = 0;
    $datum = "";
    $moretx = "";
    while (($zeile = fgets($handle, 1024))!==false)
    {
    
        if (strlen($zeile) > 3)
        {
            if (strpos($zeile, 'BEGIN:VEVENT') !== false)
            {
                $status = 1;
                $buffer = "";
            }   
            if (strpos($zeile, 'END:VEVENT') !== false)
            {
                $status = 2;
            }                    
                   
            if ($status == 1)
            {
                $buffer .= $zeile;
                if (strpos($zeile, 'DTSTART') !== false)
                {
                    $anzahl += 1;
                    $start = explode(':',$zeile);
                    //echo (date("d.m.Y", strtotime($start[1]))."  ");  
                }
                if (strpos($zeile, 'SUMMARY') !== false)
                {
                    $anzahl += 1;
                    $muell = explode(':',$zeile);  
                    $temp = explode('Bezirk',$muell[1]);              
                    //echo($temp[0]."<br><br>"); 
                }
                if (strpos($zeile, 'DESCRIPTION') !== false)
                {
                    $zeiger = ftell($handle);
                    $moretx = fgets($handle, 1024);
                    $moretx .= fgets($handle, 1024);   
                    $moretx .= fgets($handle, 1024);  
                    $moretx .= fgets($handle, 1024);
                    $moretx .= fgets($handle, 1024);                                                                           
                    $moretx = str_replace('\n', " ", $moretx);                    
                    $blabla = explode('DESCRIPTION:', ($zeile.$moretx));
                    fseek($handle,$zeiger);
                    //$blabla = array("", "");   
                }
                if ($anzahl == 2)
                {
                    $anzahl = 0;
                    if ($datum !== (date("d.m.Y", strtotime($start[1]))) )
                    {
                        $datum = (date("d.m.Y", strtotime($start[1]))); 
                        //echo ("<br>".$datum);
                        $ar_termine[$datum] = array("", "", "", "", "");
                        $info[$datum] = array("", "", "", "", "");
                        $index = 0;
                    }
                    $ar_termine[$datum][$index] = $temp[0];
                    $info[$datum][$index] = $blabla[1];
                    $index += 1;
                }    
            }
            if ($status == 2)
            {
                //echo($buffer);
                $status = 0;
            }        
    
            if ($status == 3)
            {
                $status = 0;
                return;
            }
        }
    
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
    global $anztage, $info;
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
          <meta http-equiv="refresh" content="60; URL=nachricht.php">
          <title>Muellkalender</title>
        </head>
      
        <style>
          body              { min-width:50px; max-width:1900px; }
          div               { display:flex; flex-flow:row wrap; }
          button            { font:normal bold 14px Arial; margin:0px; padding:0px; padding-top:10px;
                              border:1px solid #AAAAAA; border-radius:4px; width:80px; height:80px;
                              background: linear-gradient(to bottom, #F2F2F2, #F2F2F2 49%, #E2E2E2 51%, #E2E2E2 100%);}
          div iframe        { pointer-events:none; margin:0px; padding:0px; border:0px; width:60px; height:60px; }
  
          <!-- CLASS -->
          .sensor           { pointer-events:none; margin:0px; padding:0px; border:0px; width:88px; height:83px; }
          .zeit             { margin:0px; padding:0px; border:0px; width:300px; height:25px; }
  
          <!-- ID -->
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
                  $feld5 = ($info[$datum][0]);
                  $feld6 = ($info[$datum][1]);  
                  $feld7 = ($info[$datum][2]);               
                  $feld8 = ($info[$datum][3]);    
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


/* ################################################################
   # Hauptprogramm                                                #
   ################################################################ 
*/ 

seite_lesen();
termin_finden();
anzeige_termine();
//var_dump($ar_termine);
//echo ("31.05.2025 - ".$ar_termine["31.05.2025"][0]." : ".$ar_termine["31.05.2025"][1]." : ".$ar_termine["31.05.2025"][2]);

  




        

?>
