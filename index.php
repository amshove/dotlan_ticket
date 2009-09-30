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
# ticket/index.php - Version 0.9                                       #
########################################################################

include_once("../global.php");
include_once($global['include_path']."class.event_frontend.php");
$PAGE->sitetitle = $PAGE->htmltitle = _("Ticket");

$event_id = $EVENT->next;
$user_id = $CURRENT_USER->id;
$EVENT->getevent($event_id);

$data = $DB->query_first("SELECT t.bezahlt AS bezahlt, s.status_extern AS status, t.sitz_nr AS sitzplatz FROM event_teilnehmer t, event_status s WHERE t.zahl_typ = s.id AND t.event_id = '".$event_id."' AND t.user_id = '".$user_id."' LIMIT 1");

if($data['bezahlt'] < 1){
  $output = "Du bist nicht zu diesem Event angemeldet oder hast noch nicht bezahlt. Den Status kannst du <a href='/party/?do=status'>hier</a> sehen.";
}else{
  $output = "Hier bekommst du dein Online-Ticket im PDF-Format und kannst es ausdrucken. Mit dem Ticket darfst du dich dann beim Check-In an die \"Fast Lane\" anstellen, und kommst schneller in die Halle.";
  $output .= "<br><br>";
  $output .= "<b>Ticket:</b> <a href='/ticket/export.php' target='_blank'>".sprintf("%04d",$user_id)." - ".$CURRENT_USER->nick." - ".$EVENT->eventarr['name']."</a>";
}

$PAGE->render($output);
?>
