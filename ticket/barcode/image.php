<?php
/*
Barcode Render Class for PHP using the GD graphics library 
Copyright (C) 2001  Karim Mribti
								
   Version  0.0.7a  2001-04-01  
								
This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.
																  
This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.
											   
You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
																		 
Copy of GNU Lesser General Public License at: http://www.gnu.org/copyleft/lesser.txt
													 
Source code home page: http://www.mribti.com/barcode/
Contact author at: barcode@mribti.com
*/
  
  define (__TRACE_ENABLED__, false);
  define (__DEBUG_ENABLED__, false);
  
  require("barcode.php");  
  require("i25object.php");
  require("c39object.php");
  require("c128aobject.php");
  require("c128bobject.php");
  require("c128cobject.php");
              			   
# Style Einstellungen (Moegliche Einstellungen stehen in der barcode.php)
  if (!isset($style))  $style   = BCS_BORDER | BCS_ALIGN_CENTER | BCS_IMAGE_PNG | BCS_DRAW_TEXT | BCS_STRETCH_TEXT;
# Breite des Barcodes
  if (!isset($width))  $width   = "250";
# Hoehe des Barcodes
  if (!isset($height)) $height  = BCD_DEFAULT_HEIGHT;
# Hotizontale Aufloesung
  if (!isset($xres))   $xres    = BCD_DEFAULT_XRES;
# Schrift-Typ
  if (!isset($font))   $font    = BCD_DEFAULT_FONT;
# Barcode-Typ
  $type = "C128A";
# Inhalt des Barcodes (wird aus der GET-Variable genommen)
  $code = $_GET["code"];
  			    
  switch ($type)
  {
    case "I25":
			  $obj = new I25Object($width, $height, $style, $code);
			  break;
    case "C39":
			  $obj = new C39Object($width, $height, $style, $code);
			  break;
    case "C128A":
			  $obj = new C128AObject($width, $height, $style, $code);
			  break;
    case "C128B":
			  $obj = new C128BObject($width, $height, $style, $code);
			  break;
    case "C128C":
              $obj = new C128CObject($width, $height, $style, $code);
			  break;
	default:
			echo "Need bar code type ex. C39";
			$obj = false;
  }
   
  if ($obj) {
      $obj->SetFont($font);   
      $obj->DrawObject($xres);
  	  $obj->FlushObject();
  	  $obj->DestroyObject();
  	  unset($obj);  /* clean */
  }
?>
