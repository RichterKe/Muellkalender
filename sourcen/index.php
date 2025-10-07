<?php
/*
    Müllkalender der Stadt lesen (iCal-Format)
    Version 5
    
    Alle Änderungen werden in der Datei "mcalender4.inc" vorgenommen
    
*/

/* ################################################################
   # Globale Variablen                                            #
   ################################################################ */    

$docker_call = 0;                       // 0=Normal, 1=Docker
$docker_vz = "daten";
$prog_data = "mkalender5.dat";
$bpath = "bilder/";
$anztage = 31;
$dpath = "";
$icsdat = "";
$prog_sec_d = "";
$ar_wtage = array("Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag");
$ar_uml = array("Ä" => "Ae","Ö" => "Oe","Ü" => "Ue","ä" => "ae","ö" => "oe","ü" => "ue","ß" => "ss");
$termin = array();
$eintrag = array();
$mdatum = array();
$mtyp = array();
$minfo = array();
$ar_termine = array();
$ar_infos = array();
$bilder = array();
$txt_korr = array();
$html_korr = array();

$fehler = 0;
$tagsek = 86400;


/* ################################################################
   # Unterprogramme                                               #
   ################################################################ */ 
   
/* Funktion zum zerlegen einer Zeile aus der Datendatei in die einzelnen Bestandteile
   und Rückgabe der Teile in einem Array, Element 0 enthält die Anzahl der Einträge */
function teile_text($text)
{
  $tok = "";
  $index = 0;
  $respond = array("0" => "0");
  $tok = strtok($text,"=\t");
  while ($tok !== false)
  {
    $index += 1;
    $respond[$index] = trim(str_replace("'", "", $tok));
    $respond[0] = $index;
    if ($index >= 8) break;
    $tok = strtok(" \t");
  }
  return $respond;
}    

/* Initialisierungen beim Programmstart */
function startup()
{
    global $docker_call, $docker_vz, $prog_data, $bpath; 
    $data = $docker_vz."/".$prog_data;
    $os_bef = "";
    if (file_exists($docker_vz))
    {
        $os_bef = "sudo chmod 777 ".$docker_vz;
        shell_exec($os_bef);
        $os_bef = "sudo chown www-data:www-data ".$docker_vz;
        shell_exec($os_bef);        
        $os_bef = "sudo chmod 666 ".$prog_data;
        shell_exec($os_bef);        
        $os_bef = "sudo chown www-data:www-data ".$prog_data;
        shell_exec($os_bef);         
        $docker_call = 1;
        if (!file_exists($data))
        {
            $os_bef = "sudo cp ".$prog_data." ".$data;
            shell_exec($os_bef);
        }
        $os_bef = "sudo chmod 666 ".$data;
        shell_exec($os_bef); 
        $os_bef = "sudo chown www-data:www-data ".$data;
        shell_exec($os_bef);           
        $prog_data = $data;
        $data = $docker_vz."/";
        $os_bef = "sudo cp -n ".$bpath."*.* ".$data; 
        shell_exec($os_bef);
        $os_bef = "sudo chmod 666 ".$data."*.*";
        shell_exec($os_bef);
        $os_bef = "sudo chown www-data:www-data ".$data."*.*";        
        shell_exec($os_bef);       
        $bpath = $data;
    }
    else
    {
        $docker_call = 0;
    }
}
   
// Datendatei lesen
function lese_daten()
{
    global $prog_data, $bpath, $prog_sec_d, $icsdat, $anztage;
    global $bilder, $txt_korr, $html_korr;
    $buffer = "";
    $teil = "";
    $dummy = 0;
    $han_data = "";
    if (file_exists($prog_data))
    {
        $han_data = fopen($prog_data, 'r');
        if ($han_data)
        {
            while (($buffer = fgets($han_data, 1024))!==false)
            {
                // Eingabezeile zerlegen
                $teil = teile_text($buffer);
                // Kommentarzeile
               if (($teil[0] >= 1) AND (stripos($teil[1],"//") !== false))
                {
                    $dummy = 0;
                } 
                // Neue Sektion
                elseif (($teil[0] == 1) AND (strtoupper($teil[1]) == "[PROGRAMM]"))
                {
                    $prog_sec_d = 'pro';
                }                                           
                elseif (($teil[0] == 1) AND (strtoupper($teil[1]) == "[ARTKORR]"))
                {
                    $prog_sec_d = 'kor';
                } 
  
                elseif (($teil[0] == 1) AND (strtoupper($teil[1]) == "[HTMLKORR]"))
                {
                    $prog_sec_d = 'htm';
                }               
                
                elseif (($teil[0] == 1) AND (strtoupper($teil[1]) == "[BILDER]"))
                {
                    $prog_sec_d = 'bil'; 
                }                        
                // Werte in [programm]   
                elseif (($teil[0]>=2) AND ($prog_sec_d == "pro") AND (strtoupper($teil[1]) == "ICAL"))
                {
                    $icsdat = $teil[2];
                }
                elseif (($teil[0]>=2) AND ($prog_sec_d == "pro") AND (strtoupper($teil[1]) == "TAGE"))
                {
                    $anztage = $teil[2];
                }                
                // Werte in [artkorr]
                elseif (($teil[0]>=2) AND ($prog_sec_d == "kor"))
                {
                    $txt_korr[] = [$teil[1], $teil[2]];
                }
                // Werte in [htmlkorr]
                elseif (($teil[0]>=2) AND ($prog_sec_d == "htm"))
                {
                    $html_korr[] = [$teil[1], $teil[2]];
                }                
                
                
                // Werte in [bilder]
                elseif (($teil[0]>=2) AND ($prog_sec_d == "bil"))
                {
                    $bilder[$teil[1]] = $bpath.$teil[2];
                }                                

            }        
        
        }
    
    }

}   

// Texte von UTF-8 auf Windows.1252 konvertieren
function cv_utf($txt)
{
    global $ar_uml;
    $erg = "";
    $erg = iconv("UTF-8", "Windows-1252", $txt);
    $erg = strtr($erg, $ar_uml);
    return $erg;
}  

// Korrekturen bei der Abfallart
// Im Müllkalender der Stadt steht für die Müllart beispielsweise: 
// "Bio-Tonne Bezirk B-3, S-3 Di"
// Das wird in "Bio-Tonne" korrigiert 
function aart_korr($text)
{
    global $txt_korr;
    $ergebnis = $text;
    
    foreach($txt_korr as $value)
    {
        $ergebnis = str_replace($value[0], $value[1], $ergebnis);
    }
    return $ergebnis;
}

// Bilder finden und anzeigen
// Änderungen optional    
function bilder_finden($text, $inf)
{
    global $dpath, $bpath;
    global $ar_uml, $bilder;
    global $html_korr;
   
    $text = trim($text);
    $text = strtr($text, $ar_uml);
    $ergebnis = "&nbsp;"; 
    
    if (array_key_exists($text, $bilder))
    {
        $ergebnis = '<img src="'.$bilder[$text].'" alt="'.$text.'" title="'.$inf.'" border=0 width="" height="">';
    }  
    elseif ((!empty($inf)) AND ($inf != "&nbsp;"))
    {
        $ergebnis = '<img src="'.$bilder["Default"].'" alt="'.$text.'" title="'.$inf.'" border=0 width="" height="">';
    }                               

    //$text = str_replace("é", "&eacute;", $text);  
    foreach($html_korr as $value)
    {
        $text = str_replace($value[0], $value[1], $text);
    }
      
    echo ($text);
    return $ergebnis;
}

   
// Terminkalender generieren   
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
    $handle = NULL;
    
    $handle = @fopen($icsdat, 'r');
    if ($handle)
    {
        fclose($handle);
    }
    else
    {
        $fehler = 1;
        return;
    }
    
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
            
            // Informationen zur Müllart lesen
            preg_match_all('/DESCRIPTION:(.*?)\b[A-Z]{3,}:/s', $terdat, $minfo);    
            if (empty($minfo[0]) OR (strpos($terdat, 'DESCRIPTION:') == false))
            {
                $minfo = array();
                $minfo[1][0] = "* Keine Information vorhanden *";
            }            
            // Datum formatieren und Ergebnisse in Variablen schreiben 
            $adat = (date("d.m.Y", strtotime($mdatum[1][0])));
            // Eventuelle Korrekturen im Text der Müllart
            $mtyp[1][0] = cv_utf($mtyp[1][0]);            
            $aart = aart_korr($mtyp[1][0]);
            
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
            if (is_array($aart))
            {
                $ar_termine[$adat] =  $aart;
                $index = 4;
            }
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

function anzeige_termine()
{   
    global $ar_termine, $tagsek;
    global $anztage, $ar_infos;
    global $ar_wtage;
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
          <meta http-equiv="refresh" content="60; URL=index.php">
          <title>Muellkalender</title>
        </head>
      
        <style>
          a:link            { text-decoration:none; font-weight:normal; color:#000000; }
          a:visited         { text-decoration:none; font-weight:normal; color:#000000; }
          a:hover           { text-decoration:none; font-weight:bold; background-color:#FFFF00; }
          a:active          { text-decoration:none; font-weight:normal; background-color:#FFFFFF; }
          a:focus           { text-decoration:none; font-weight:normal; background-color:#FFFFFF; }        
        
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
          <h2><a href = "mkal-edit.php">M&uuml;ll Entsorgungstermine</a></h2>
          <div> 
            <?php
              for ($i = 0; $i <= $anztage; $i++)
              {
                $datum = (date("d.m.Y", (time()+($i*$tagsek))));
                $wotag = $ar_wtage[(date("w", (time()+($i*$tagsek))))];
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
                <?php echo ($wotag."  ".$datum); ?>
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
          #txt              { font-size:18px;}

    
        </style>  
        <body>
          <span id="titel"> Es ist ein Fehler aufgetreten:<br><br></span>
          <span id="txt">
            Die iCal-Datei<br><br>
            <b><?php echo ($icsdat); ?></b><br><br>
            ist nicht vorhanden oder hat ein falsches Format!
          </h3><br><br>
          <h2><a href = "mkal-edit.php">Einstellungen bearbeiten</a></h2>
        </body>
      </html> 
    <?php 

}  


/* ################################################################
   # Hauptprogramm                                                #
   ################################################################ 
*/ 

startup();
lese_daten();

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