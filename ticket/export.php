<?
########################################################################
# Online-Ticket Modul for dotlan                                       #
#                                                                      #
# Copyright (C) 2009 Torsten Amshove <torsten@amshove.net>             #
#                                                                      #
# This program is free software; you can redistribute it and/or modify #
# it under the terms of the GNU General Public License as published by #
# the Free Software Foundation; either version 3 of the License, or    #
# (at your option) any later version.                                  #
#                                                                      #
# This program is distributed in the hope that it will be useful, but  #
# WITHOUT ANY WARRANTY; without even the implied warranty of           #
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU     #
# General Public License for more details.                             #
#                                                                      #
# You should have received a copy of the GNU General Public License    #
# along with this program; if not, see <http://www.gnu.org/licenses/>. #
#                                                                      #
# ticket/export.php - Version 1.0                                      #
########################################################################

include_once("../global.php");

$URL = "http://".$_SERVER["SERVER_NAME"]."/ticket";

$ip = "10.10.";
$subnetz = array(
  "A" => "2",
  "B" => "3",
  "C" => "4",
  "D" => "5",
  "E" => "6",
  "F" => "7",
  "G" => "8",
  "H" => "9",
  "V" => "10"
);
$subnetzmaske = "255.255.240.0";
$gateway = "10.10.1.1";
$dns1 = "10.10.1.253";
$dns2 = "10.10.1.1";
$wins = "10.10.1.253";
$workgroup = "LAN";

$text_ueber_settings = "Dies ist Deine persönliche IP-Konfiguration für die maxlan. Bitte denke daran, dass du ERST diese Einstellungen vornimmst, BEVOR du dein Netzwerkkabel in den Switch steckst.";
$text_wichtig = "Bitte halte dich UNBEDINGT an diese Einstellungen. Das eigenständige Wechseln der IP-Adresse während der maxlan ist strengstens untersagt und kann bei Wiederholung zum Ausschluss von der maxlan führen. Weiterhin ist auch die Arbeitsgruppe zwingend auf \"".$workgroup."\" zu ändern. Zuwiderhandlungen führen ohne Vorankündigung zu einer Sperrung eures Zugangs zum Netzwerk der maxlan (in dem Fall bitte beim Support melden). Alles Weitere regeln die AGB. Pizzaschachteln bitte in die gesonderten Behälter! Alle Flaschen und angebrochenen/vollen Tetrapacks bitte an die Außenseite der Tische stellen! Danke!";
$text_server = "Wir wollen hier nicht alle Adressen unserer Server aufführen, da ihr diese im Intranet nachschauen könnt.
Nur die wichtigsten sollten hier genannt werden:

Intranet Server: www.lan (IP: 10.10.1.252)
Dort gibt es alle Infos zur LAN, inkl. der Liste der weiteren Server.

FTP-Server: ftp.lan
Dort erhaltet ihr verschiedene Patches für Games und Betriebssysteme.";
$text_abschluss = "Wir wünschen Euch viel Spaß auf der maxlan";
$text_hinweis = "BITTE UNBEDINGT DEN POP-UP BLOCKER FÜR \"www.lan\" DEAKTIVIEREN, da wichtige Turnierinformationen über POP-Up's mitgeteilt werden.";


########################################################################


if(!empty($_GET["userid"]) && $ADMIN->check(IS_ADMIN)){
  $user_id = mysql_real_escape_string($_GET["userid"]);
  $user = $DB->query_first("SELECT nick, vorname, nachname, geb FROM user WHERE id = '".$user_id."' LIMIT 1");
  $nick = utf8_decode($user['nick']);
  $vorname = utf8_decode($user['vorname']);
  $nachname = utf8_decode($user['nachname']);
  $geb_date = $user['geb'];
}else{
  $user_id = $CURRENT_USER->id;
  $nick = $CURRENT_USER->nick;
  $vorname = $CURRENT_USER->vorname;
  $nachname = $CURRENT_USER->nachname;
  $geb_date = $CURRENT_USER->geb_date;
}

$event_id = $EVENT->next;
$EVENT->getevent($event_id);

$data = $DB->query_first("SELECT t.bezahlt AS bezahlt, s.status_extern AS status, t.sitz_nr AS sitzplatz FROM event_teilnehmer t, event_status s WHERE t.zahl_typ = s.id AND t.event_id = '".$event_id."' AND t.user_id = '".$user_id."' LIMIT 1");

if($data['bezahlt'] < 1){
  # Benutzer hat nicht bezahlt / ist nicht angemeldet
  echo "Du bist nicht zu diesem Event angemeldet oder hast noch nicht bezahlt. Den Status kannst du <a href='/party/?do=status'>hier</a> sehen.";
}else{
  $user_id_long = sprintf("%04d",$user_id);

  $geb_date = substr($geb_date,8,2).".".substr($geb_date,5,2).".".substr($geb_date,0,4);

  $bezahlt = $data['bezahlt'];
  if($bezahlt == 0) $bezahlt = "NEIN";
  elseif($bezahlt == 1) $bezahlt = "JA";
  elseif($bezahlt == 2) $bezahlt = "(Team)";

  if(preg_match("/^Sitz\: [A-HV]\-[0-9][0-9]?$/",$data['sitzplatz'])){
    if(strlen($data['sitzplatz']) == 9){
      $block = substr($data['sitzplatz'],-3,1);
      $platz = substr($data['sitzplatz'],-1);
      $ip = $ip.$subnetz[$block].".".$platz;
    }elseif(strlen($data['sitzplatz']) == 10){
      $block = substr($data['sitzplatz'],-4,1);
      $platz = substr($data['sitzplatz'],-2);
      $ip = $ip.$subnetz[$block].".".$platz;
    }else $ip = "bitte am Support fragen";
  }else $ip = "bitte am Support fragen";

  require('fpdf16/fpdf.php');

  $pdf=new FPDF();
  $pdf->AddPage();
  # Barcode
  $pdf->Image($URL."/barcode/image.php?code=".$user_id_long."&tmp=.png",120,42,50);
  # Logo
  $pdf->Image($URL."/logo.gif",150,20,50);
  # Ueberschrift / Eventname
  $pdf->SetFont('Arial','B',20);
  $pdf->Cell(0,10,$EVENT->eventarr['name']);
  $pdf->Ln();
  # Unterschrift / Location
  $pdf->SetFont('Arial','',12);
  $pdf->Cell(0,5,$EVENT->eventarr['location']." - ".$EVENT->eventarr['strasse']." - ".$EVENT->eventarr['plz']." ".$EVENT->eventarr['ort']);
  $pdf->Ln();
  # Unterschrift / Datum
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,5,$EVENT->eventarr['begin']." - ".$EVENT->eventarr['end']);
  $pdf->Ln();
  # Leerraum
  $pdf->Cell(0,7,'');
  $pdf->Ln();
  # Erster Block
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(22,4,"Bezahlt: ");
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,4,$bezahlt);
  $pdf->Ln();
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(22,4,"Status: ");
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,4,$data['status']);
  $pdf->Ln();
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(22,4,"SitzNr.: ");
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,4,$data['sitzplatz']);
  $pdf->Ln();
  # Leerraum
  $pdf->Cell(0,3,'');
  $pdf->Ln();
  # Zweiter Block
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(22,4,"UserID: ");
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,4,$user_id_long);
  $pdf->Ln();
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(22,4,"Nick: ");
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,4,$nick);
  $pdf->Ln();
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(22,4,"Vorname: ");
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,4,$vorname);
  $pdf->Ln();
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(22,4,"Nachname: ");
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,4,$nachname);
  $pdf->Ln();
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(22,4,"Geb.: ");
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,4,$geb_date);
  $pdf->Ln();
  # Leerraum
  $pdf->Cell(0,10,'');
  $pdf->Ln();
  # Satz vor Settings
  $pdf->SetFont('Arial','',10);
  $pdf->MultiCell(0,4,$text_ueber_settings);
  $pdf->Ln();
  # Leerraum
  $pdf->Cell(0,2,'');
  $pdf->Ln();
  # Dritter Block - IP Settings
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(30,4,"Rechnername: ");
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,4,$nick." (ohne Sonderzeichen)");
  $pdf->Ln();
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(30,4,"IP-Adresse: ");
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,4,$ip);
  $pdf->Ln();
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(30,4,"Subnetzmaske: ");
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,4,$subnetzmaske);
  $pdf->Ln();
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(30,4,"Gateway: ");
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,4,$gateway);
  $pdf->Ln();
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(30,4,"1. DNS-Server: ");
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,4,$dns1);
  $pdf->Ln();
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(30,4,"2. DNS-Server: ");
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,4,$dns2);
  $pdf->Ln();
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(30,4,"WINS-Server: ");
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,4,$wins);
  $pdf->Ln();
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(30,4,"Arbeitsgruppe: ");
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,4,$workgroup);
  $pdf->Ln();
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(30,4,"Switchport: ");
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,4,$platz);
  # Leerraum
  $pdf->Cell(0,10,'');
  $pdf->Ln();
  # Text nach Settings
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(20,4,"WICHTIG:");
  $pdf->SetFont('Arial','',10);
  $pdf->MultiCell(0,4,$text_wichtig);
  $pdf->Ln();
  # Leerraum
#  $pdf->Cell(0,10,'');
  $pdf->Cell(0,3,'');
  $pdf->Ln();
  # Serveradressen
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(0,4,"Ein paar Server-Adressen");
  $pdf->Ln();
  $pdf->SetFont('Arial','',10);
  $pdf->MultiCell(0,4,$text_server);
  $pdf->Ln();
  # Leerraum
#  $pdf->Cell(0,10,'');
  $pdf->Cell(0,4,'');
  $pdf->Ln();
  # Gruss
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(0,4,$text_abschluss);
  # Leerraum
#  $pdf->Cell(0,30,'');
  $pdf->Cell(0,10,'');
  $pdf->Ln();
  # Pop-Up Hinweis
  $pdf->SetFont('Arial','B',12);
  $pdf->MultiCell(0,4,$text_hinweis);

# Teamplay aktion
# Logo
#  $pdf->Image($URL."/teamplay_logo.jpg",10,245,40);
#  $pdf->Cell(0,15,'');
#  $pdf->Ln();
#  $pdf->SetFont('Arial','B',10);
#  $pdf->Cell(42,4,"");
#  $pdf->SetFont('Arial','B',10);
#  $pdf->Cell(0,4,"Gutschein für teamplay.de im Wert von 50 EUR");
#  $pdf->Ln();
#  $pdf->SetFont('Arial','B',10);
#  $pdf->Cell(42,4,"");
#  $pdf->SetFont('Arial','',10);
#  $pdf->Cell(0,4,"Bei Einreichung dieses PDF-Tickets - pro Anmeldung ein Ticket gültig.");
#  $pdf->Ln();
#  $pdf->Cell(42,4,"");
#  $pdf->SetFont('Arial','',10);
#  $pdf->Cell(1,4,"Zeitraum: 01. - 31. Dezemeber 2011");
#  $pdf->Ln();
#  $pdf->SetFont('Arial','B',10);
#  $pdf->Cell(42,4,"");
#  $pdf->SetFont('Arial','',10);
#  $pdf->Cell(0,4,"Für Gameserver, nicht für Sonderaktionen und Voiceserver.");
#  $pdf->Ln();
#  $pdf->SetFont('Arial','B',10);
#  $pdf->Cell(42,4,"");
#  $pdf->SetFont('Arial','B',10);
#  $pdf->Cell(0,4,"Code: FAF-4BF-A41");

  $pdf->Output("maxlan_ticket.pdf","I");
}
?>
