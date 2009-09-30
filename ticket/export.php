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
# ticket/export.php - Version 0.9                                      #
########################################################################

include_once("../global.php");

$event_id = $EVENT->next;
$user_id = $CURRENT_USER->id;
$EVENT->getevent($event_id);

$data = $DB->query_first("SELECT t.bezahlt AS bezahlt, s.status_extern AS status, t.sitz_nr AS sitzplatz FROM event_teilnehmer t, event_status s WHERE t.zahl_typ = s.id AND t.event_id = '".$event_id."' AND t.user_id = '".$user_id."' LIMIT 1");

if($data['bezahlt'] < 1){
  echo "Du bist nicht zu diesem Event angemeldet oder hast noch nicht bezahlt. Den Status kannst du <a href='/party/?do=status'>hier</a> sehen."; 
}else{
  $user_id_long = sprintf("%04d",$user_id);

  $geb_date = $CURRENT_USER->geb_date;
  $geb_date = substr($geb_date,8,2).".".substr($geb_date,5,2).".".substr($geb_date,0,4);

  $bezahlt = $data['bezahlt'];
  if($bezahlt == 0) $bezahlt = "NEIN";
  elseif($bezahlt == 1) $bezahlt = "JA";
  elseif($bezahlt == 2) $bezahlt = "(Team)";

  require('fpdf16/fpdf.php');

  $pdf=new FPDF();
  $pdf->AddPage();
  $pdf->Cell(0,5,'');
  $pdf->Ln();
  # Ueberschrift / Eventname
  $pdf->SetFont('Arial','B',20);
  $pdf->Cell(10,10,'');
  $pdf->Cell(0,10,$EVENT->eventarr['name']);
  $pdf->Ln();
  # Unterschrift / Location
  $pdf->SetFont('Arial','',12);
  $pdf->Cell(10,5,'');
  $pdf->Cell(0,5,$EVENT->eventarr['location']." - ".$EVENT->eventarr['strasse']." - ".$EVENT->eventarr['plz']." ".$EVENT->eventarr['ort']);
  $pdf->Ln();
  # Unterschrift / Datum
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(10,5,'');
  $pdf->Cell(0,5,$EVENT->eventarr['begin']." - ".$EVENT->eventarr['end']);
  $pdf->Ln();
  # Leerraum
  $pdf->Cell(0,20,'');
  $pdf->Ln();
  # Erster Block
  $pdf->Cell(10,5,'');
  $pdf->SetFont('Arial','B',12);
  $pdf->Cell(25,5,"Bezahlt: ");
  $pdf->SetFont('Arial','',12);
  $pdf->Cell(0,5,$bezahlt);
  $pdf->Ln();
  $pdf->Cell(10,5,'');
  $pdf->SetFont('Arial','B',12);
  $pdf->Cell(25,5,"Status: ");
  $pdf->SetFont('Arial','',12);
  $pdf->Cell(0,5,$data['status']);
  $pdf->Ln();
  $pdf->Cell(10,5,'');
  $pdf->SetFont('Arial','B',12);
  $pdf->Cell(25,5,"SitzNr.: ");
  $pdf->SetFont('Arial','',12);
  $pdf->Cell(0,5,$data['sitzplatz']);
  $pdf->Ln();
  # Leerraum
  $pdf->Cell(0,10,'');
  $pdf->Ln();
  # Zweiter Block
  $pdf->Cell(10,5,'');
  $pdf->SetFont('Arial','B',12);
  $pdf->Cell(25,5,"UserID: ");
  $pdf->SetFont('Arial','',12);
  $pdf->Cell(0,5,$user_id_long);
  $pdf->Ln();
  $pdf->Cell(10,5,'');
  $pdf->SetFont('Arial','B',12);
  $pdf->Cell(25,5,"Nick: ");
  $pdf->SetFont('Arial','',12);
  $pdf->Cell(0,5,$CURRENT_USER->nick);
  $pdf->Ln();
  $pdf->Cell(10,5,'');
  $pdf->SetFont('Arial','B',12);
  $pdf->Cell(25,5,"Vorname: ");
  $pdf->SetFont('Arial','',12);
  $pdf->Cell(0,5,$CURRENT_USER->vorname);
  $pdf->Ln();
  $pdf->Cell(10,5,'');
  $pdf->SetFont('Arial','B',12);
  $pdf->Cell(25,5,"Nachname: ");
  $pdf->SetFont('Arial','',12);
  $pdf->Cell(0,5,$CURRENT_USER->nachname);
  $pdf->Ln();
  $pdf->Cell(10,5,'');
  $pdf->SetFont('Arial','B',12);
  $pdf->Cell(25,5,"Geb.: ");
  $pdf->SetFont('Arial','',12);
  $pdf->Cell(0,5,$geb_date);
  $pdf->Ln();
  # Barcode
  $pdf->Image(substr($_SERVER["SCRIPT_URI"],0,-10)."barcode/image.php?code=".$user_id_long."&tmp=.png",120,55,50);
  $pdf->Output();
}  
?>
