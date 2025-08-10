<?php
##############################################################################################
##								 - ZZ:PHPBoard 1.4 -										##
##~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~##
## CONFIGURATION_FILE									  			script: |zehnet.de|  	##
##~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~##
##																							##
##   ZZ:PHPBoard Version 1.4 																##
##   *PHP-based PostingForum*																##
##																							##
##   Copyright (c) 2002 # Nicolas Zeh <mail@zehnet.de>										##
##   																						##
##   This File is part of the distribution of ZZ:PHPBoard 1.4. 								##
##   This License inlcudes all the other files of the distribution.							##
##   ZZ:PHPBoard is free software; you can redistribute it and/or modify					##
##   it under the terms of the GNU General Public License as published by					##
##   the Free Software Foundation; either version 2 of the License, or						##
##   (at your option) any later version.													##
##   																						##
##   This program is distributed in the hope that it will be useful,						##
##   but WITHOUT ANY WARRANTY; without even the implied warranty of							##
##   MERCHANTABILITY. 																		##
##																							##
##   See the GNU General Public License for more details.									##
##   You should have received a copy of the GNU General Public License						##
##   along with this program [gpl.html]														##		
##   																						##
##------------------------------------------------------------------------------------------##
##							scripts at: | download.zehnet.de |								##
##------------------------------------------------------------------------------------------##
##																							##
##																							##
##																							##
## Pleas read this carefully !!																##
## The MAIN-Section contains variables that HAVE to be changed !							##
## The SUB-Section contains variables that CAN be changed !									##
##																							##
##																							##
##																							##
##------------------------------------------------------------------------------------------##
## SUB:   No MAIN Section in this Porgram only cosmetic & language Variables to change		##
##------------------------------------------------------------------------------------------##
##																							##
##																							##
## COSMETIC																					##
##------------------------------------------------------------------------------------------##
## Setting the Width of the forum-table														##
$cfg_table_width ='550';																	##
$cfg_msgdir = 'msg';
## The rest of the cosmetic settings (colors, FontFace, etc) can be adjusted 				##
## in the styles.css-file in the 'inc'-directory.											##
## Further explanations are in that file !													##
##																							##
##																							##
## DATE																						##
##------------------------------------------------------------------------------------------##
## Name of the weekdays in your language:													##
$weekdays = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday');				##
## Number of letters for the abbreviation of the Day: 2= 'Mo' | 3= 'Mon'   for Monday		##
$datesub = '3'; 																			##
## Date-Format:																				##
## en= Month/Day/Year | eu= Day/Month/Year													##
$dateorder = 'en'; # = 'eu';																##
##																							##
##																							##
## LANGUAGE																					##
##------------------------------------------------------------------------------------------##
##																							##
## ERROR-page settings ---------------------------------------------------------------------##
$cfg_error_noname ='Submitting a name is <b>obligatory</b>.';								##
$cfg_error_wrongemail ='You entered an <b>invalid</b> email.';								##
$cfg_error_nosubject ='Submitting a subject is <b>obligatory</b>.';							##
$cfg_error_nomessage ='Submitting a message is <b>obligatory</b>.';							##
##																							##
## Main-page settings ----------------------------------------------------------------------##
$cfg_m_forum_titel = 'ZZ:PHPBoard';															##
##																							##
$cfg_m_post_m ='Post Message';																##
$cfg_m_faq = 'FAQ';																			##
$cfg_m_back = 'Back To Main';																##
$cfg_m_back_link ='./../';																	##
$cfg_m_no_message ='No message is posted yet !';											##
$cfg_m_write_m ='Write a message !';														##
## 																							##
## Submit-Form																				##
$cfg_m_f_name ='name:';																		##
$cfg_m_f_email ='e-mail:';																	##
$cfg_m_f_subject ='subject:';																##
$cfg_m_f_message ='message:';																##
$cfg_m_f_linkurl ='link URL:';																##
$cfg_m_f_linktitel ='link title:';															##
##																							##
## Form-Button-Text																			##
$cfg_m_submit ='Post Message';																##
$cfg_m_reset ='  Reset  ';																	##
##																							##
## Link at the bottom of the main page														##
$cfg_m_botlink ='download.zehnet.de';														##
$cfg_m_boturl ='http://download.zehnet.de';													##
##																							##
## Entry-page settings ---------------------------------------------------------------------##
$cfg_p_mesi1 ='posted by';																	##
$cfg_p_mesi2 ='on';																			##
$cfg_p_mesi3 = ',';																			##
$cfg_p_mesi4 = 'at';																		##
##																							##
$cfg_p_mesin1 ='answering on:';																##
$cfg_p_mesin2 ='by';																		##
##																							##
$cfg_p_followups ='Follow-Ups:';															##
$cfg_p_no_followups ='No Follow-Ups';														##
$cfg_p_write_fu ='Add Follow-Up:';															##
##																							##
$cfg_p_submit ='Post Follow-Up';															##
##																							##
$cfg_p_m_f_ups ='Follow-Ups';																##
$cfg_p_m_post_f_ups ='Post Follow-Up';														##
$cfg_p_m_forum ='Forum';																	##
$cfg_p_m_faq ='FAQ';																		##
##																							##
## Send-page settings ----------------------------------------------------------------------##
$cfg_sendmessage ='Your message was posted !';												##
##																							##
##------------------------------------------------------------------------------------------##
## CONFIGURATION END									  									##					
##############################################################################################
?>