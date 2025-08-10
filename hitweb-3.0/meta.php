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
// $Id: meta.php,v 1.8 2001/09/22 13:36:24 hitweb Exp $

//######################################################################
//# PERSONNALISATION DE VOS BALISES META 
//######################################################################

// Information pour : aide, debug, etc...
$tpl->assign(VERSION, "version 3.0");

$tpl->assign(REVISIT, "30 days");
$tpl->assign(CONTENT_TYPE, "text/html; charset=iso-8859-1");
$tpl->assign(CONTENT_LANGUAGE, "fr");
 
//  Information sur l'AUTEUR
$tpl->assign(CORPORATE, "Brian FRAVAL"); 
$tpl->assign(AUTHOR, "Brian FRAVAL");
$tpl->assign(EMAIL, "webmaster@hitweb.org");

// date de révision passé en paramètre  
$tpl->assign(SITE, "http://www.hitweb.org/");
$tpl->assign(DATECREATION, "20000802");
$tpl->assign(DATEREVISION, $date);

$tpl->assign(TITRE_UK, "The best of the world wide web");
$tpl->assign(TITRE_FR, "Le classement des meilleurs sites web");
$tpl->assign(CATEGORY_UK, "Internet");
$tpl->assign(CATEGORY_FR, "Internet");
$tpl->assign(DESCRIPTION_UK, "");
$tpl->assign(DESCRIPTION_FR, "Classement des meilleurs sites internet par thèmes. Ce classement est dynamique par rapport au nombre de 
point de chaques sites référencés dans HITWEB");
$tpl->assign(KEYWORDS_UK, "best, site, web, internet, hitweb, brian, fraval, decaen, duhaut, gpl, free");
$tpl->assign(KEYWORDS_FR, "annuaire, moteur de recherche, meilleur, site, web, internet, hitweb, brian, fraval, référencement, decaen, 
duhaut, classement, gpl, libre");
$tpl->assign(PAGETITLE, "HITWEB : Le classement des meilleurs sites 
web.................................................................................");

?>
