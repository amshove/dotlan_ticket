dotlan ticket
=============

Dieses Modul fuer DOTLAN ermoeglicht den Usern, ein "Online-Ticket" auszudrucken.  
Auf dem Ticket (Ausgegeben als PDF) stehen die wichtigsten Daten und ein Barcode  
(UserID) zum Einscannen am Check-In.  
  
Das Ticket beinhaltet auch die IP-Einstellungen fuer jeden User, sodass diese   
nicht mehr auf der LAN-Party verteilt werden muessen. Wer sein Ticket nicht   
ausdruckt, kann es auf der LAN drucken lassen. Die IP's berechnen sich bei  
uns aus dem Block (A-H und V) und der Sitzplatznummer.  
  
Bei Admins mit den Rechten "User" wird zusaetzlich ein Feld angezeigt, mit dem  
nach Eingabe der BenutzerID jedes beliebige Ticket aufgerufen werden kann.  
Normale Benutzer sind dazu nicht in der Lage.  
  
Die Daten fuer den Barcode hab ich in der barcode/image.php fest eingestellt (Typ,  
Breite, Rahmen, etc.) sodass nurnoch die UserID uebergeben werden muss.  
  
  
Das Projekt, Hilfe, usw. findet ihr hier:    
http://www.amshove.net/wiki/dotlan:ticket  
  
  
  
Die Library fuer den Barcode hab ich von:  
http://www.mribti.com/barcode/home.php  
  
Die Library fuer die PDF-Generierung hab ich von:  
http://www.fpdf.org/  
