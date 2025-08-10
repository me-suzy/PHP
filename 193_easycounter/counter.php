<?php
 /***********************************************
  * Snippet Name : Easy Counter                 *
  * Scripted By  : Hermawan Haryanto            *
  * Website      : http://hermawan.dmonster.com *
  * Email        : hermawan@dmonster.com        *
  * License      : GPL (Gnu Public License)     *
  * Created Date : 10/08/2002                   *
  * Instruction  : 1. Upload all pack           *
  *                2. CHMOD +777 to counter.dat *
  *                3. You're done               *
  ***********************************************/
  $cf = "counter.dat";
  $fp = fopen($cf,"r");
  $ct = trim(fread($fp,filesize($cf)));
  if ($ct != "") $ct++;
  else $ct = 1;
  @fclose($fp);
  $fp = fopen($cf,"w");
  @fputs($fp,$ct);
  for($i=0;$i<strlen($ct);$i++) {
    $imgnum = substr($ct,$i,1);
    $counter .= "<img src='$imgnum.gif'>";
  }
  @fclose($fp);
  print $counter;
?>
