<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | HITWEB version 3.0                                                   |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation; either version 2 of the License, or    |
// | (at your option) any later version.                                  |
// |                                                                      |
// | This program is distributed in the hope that it will be useful, but  |
// | WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | General Public License for more details.                             |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the Free Software          |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA            |
// | 02111-1307, USA.                                                     |
// |                                                                      |
// | http://www.gnu.org/copyleft/gpl.html                                 |
// +----------------------------------------------------------------------+
// | Authors : Brian FRAVAL <brian@fraval.org>                            |
// +----------------------------------------------------------------------+
//
// $Id: mail.php,v 1.3 2001/06/19 22:54:26 hitweb Exp $

include "../conf/hitweb.conf" ;

function mail_newsite($WEBMASTER_EMAIL, $WEBMASTER_LIENS_ID) 
{

  global $EXT_PHP, $SITE, $MAIL;

// Revoir pour mettre le site et le mail

   $mailSubject = "Enregistrement dans HITWEB" ;	       // Sujet du mail
   $mailHitweb = "webmaster@hitweb.org" ;		       // Mail de HITWEB
   
   // Liens pour gagner des points
   $liensPoint = "<a href='http://www.hitweb.org/refererpoint$EXT_PHP?liens_id=$WEBMASTER_LIENS_ID' target='_top'><img src='http://www.hitweb.org/images/logo.jpg' border='0' width='80' height='29' alt='www.hitweb.org'></a>" ;					
   
   $entetedate  = date( "D, j M Y H:i:s -0600");	       // Offset horaire 
   	
   $entetemail  =  "From: $mailHitweb \n" ;		       // Adresse expéditeur 
   $entetemail .=  "Cc: $mailHitweb \n" ;		       // Personne en copie
   //$entetemail .=  "Bcc: \n" ;			       // Copies cachées si vous le desirez 
   $entetemail .=  "Organization: HITWEB \n" ;		       // Indique l'organisation
   $entetemail .=  "Reply-To: $mailHitweb \n" ;		       // Adresse de retour 
   //$entetemail .=  "X-Priority :  \n" ;		       // Indique la priorité du message
   $entetemail .=  "X-Mailer: PHP/" . phpversion() .  "\n" ;
   $entetemail .=  "Date: $entetedate" ;

mail(
     "$WEBMASTER_EMAIL",
     "$mailSubject",
     "FELICITATION !!!, votre site a bien été enregisté dans HITWEB. Il est pour l'instant disponible dans la partie Liens à valider, il sera valide après la visite du webmaster de HITWEB.\n\nComme vous le savez, tous les sites de HITWEB sont classés par point, vous gagnez des points quand un visiteur visite votre site à partir de HITWEB, plus votre site est visité, plus il sera en tête dans tout le site HITWEB (dans sa catégorie, dans le moteur de recherche, dans le TOP50, etc).\n\nPour faire gagner des points rapidement, vous pouvez mettre un liens sur votre site vers HITWEB, cela ne ralentira pas du tout le chargement de votre page web et en plus vous gagnerez des POINTS. Je suis en train de mettre en place d'autres façon pour vous faire gagner des points... Restez à l'écoute.\n\nVoici le liens à mettre sur votre site :\r\n$liensPoint\n\nPour diminuer le téléchargement, je vous conseil de télécharger le petit logo de HITWEB sur votre site web, à cette adresse :\r\nwww.hitweb.org/images/logo.jpg\n\nPS : Les informations vous concernant ne seront jamais distribuées.\n\nBrian FRAVAL\r\nwebmaster@hitweb.org", $entetemail); 

}
   
?>
