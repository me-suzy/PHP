<?
  Header("Content-type: image/jpg");
  $fp = fopen('/opt/www/virtual/skintech/gfx/logo.jpg', 'rb');
  $read = fread($fp, 4096);
  echo $read;
  fclose($fp);

//  system("/usr/local/bin/convert /opt/www/virtual/skintech/gfx/logo.jpg -");
?> 
