
# KIMB-CMS E-Mail-Versand

## S/MIME Signatur

Sie können hier S/MIME Zertifikate für das CMS hinterlegen,
damit die vom CMS versandten E-Mails digital unterschrieben werden!  
  
Das CMS verwendet die PHP Funktion openssl_pkcs7_sign() für die Erstellung
der Signatur.  

### Installation der CMS-Funktion

Sie benötigen ein vertrauenswürdiges S/MIME Zertifikat, welches auf
die Absenderadresse des CMS ausgestellt ist. 
("mailvon" in der Systemkonfiguration)  
  
Außerdem müssen Sie die ini-Datei in diesem Verzeichnis anpassen.  
Diese enthält, unter der Absenderadresse, ...
 - den Dateinamen des öffentlichen Zertifikats (pem-Datei)  
 - den Dateinamen des Schlüssels (pem-Datei)  
 - das Passwort für den Schlüssel  
  
Bitte stellen sie sicher, dass der richtige Pfad zu den Zertifikaten (und der ini-Datei) 
in der Systemkonfiguration angegeben ist. Außderdem sollte dort der Dateiname
der ini-Datei angeben sein.  
  
Die ini-Datei und die Zertifikate können beliebig auf dem Server verschoben werden,
sie müssen jedoch für PHP zu lesen sein und der Ordner muss schreibbar sein.
(Die Dateien selbst nicht.)  
  
** Bitte stellen Sie sicher, dass man die Zertifikate und die ini-Datei nicht **
** aus dem Internet aufrufen kann. (".htaccess"-Datei oder sicherer Ordner) **
