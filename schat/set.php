<?php include "incl/hdr.inc"; 
if(!isset($n)||!isset($b)){sdd('index.php');}
$nick=trim($n);$bick=trim($b);$nick=htmse($nick);
if($nick==''||$bick==''||strlen($nick)>60||strlen($bick)>2){sdd('index.php');}else{

$gtk="$pp:|:$REMOTE_ADDR:|:$nick:|:$bick";setcookie('nnk',$gtk);
$j=0;$fm=array();$fs=opu();$fs=explode("\n",$fs);

for($i=0;$i<count($fs);$i++){
if(isset($fs[$i])&&$fs[$i]!=''){$rt=explode(":|:",$fs[$i]);
if($pp-$rt[0]<70&&$rt[2]!=$nick){$fm[$j]=$fs[$i];$j++;}
}}$fm=implode("\n",$fm);$fm="$gtk\n$fm";wru($fm);}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"><html><head>
<?php print $crl[1];?><title><?php print $crl[2];?></title>
</head><frameset rows="*,90,20">
<frame src="main.php" name="a" marginwidth="8" marginheight="0" frameborder="0" scrolling="auto" noresize>
<frame src="insert.php" name="b" marginwidth="2" marginheight="12" frameborder="0" scrolling="no" noresize>
<frame src="non.php" name="c" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" noresize>
<noframes><?php print $crl[21];?></noframes></frameset></html>