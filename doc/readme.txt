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
########################################################################


Dieses Modul fuer DOTLAN ermoeglicht den Usern, ein "Online-Ticket" auszudrucken.
Auf dem Ticket (Ausgegeben als PDF) stehen die wichtigsten Daten und ein Barcode
(UserID) zum Einscannen am Check-In.

Die Daten fuer den Barcode hab ich in der barcode/image.php fest eingestellt (Typ,
Breite, Rahmen, etc.) sodass nurnoch die UserID uebergeben werden muss.

Der Barcode ist vom Typ "Code128-A". Das kann in der Datei barcode/image.php
geändert werden


Das Projekt, Hilfe, usw. findet ihr hier:
http://sourceforge.net/projects/dotlanticket/



Die Library fuer den Barcode hab ich von:
http://www.mribti.com/barcode/home.php

Die Library fuer die PDF-Generierung hab ich von:
http://www.fpdf.org/
