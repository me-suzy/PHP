<?
if(isset($yousay)):



	if (empty($name) || empty($yousay)):
		
		$reconvert = ereg_replace("andmark","&",$query);

		echo "<script>window.location='$reconvert'</script>";		

	else:

		setcookie("ck_name","$name",time()+1800);
		setcookie("ck_email","$email",time()+1800);

		$name = str_replace("|","",$name);
		$name = str_replace("\\","",$name);
		$name = eregi_replace("<","&lt;",$name);
		$name = eregi_replace(">","&gt;",$name);

		$email = str_replace("|","",$email);
		$email = eregi_replace("<","&lt;",$email);
		$email = eregi_replace(">","&gt;",$email);
		
		$yousay = str_replace("|","",$yousay);
		$yousay = str_replace("\\","",$yousay);
		$yousay = eregi_replace("<","&lt;",$yousay);
		$yousay = eregi_replace(">","&gt;",$yousay);

		$savefile = "rover.db.php";

		if ( !file_exists($savefile) ) {
		$newfile = fopen($savefile);
		fclose($newfile);
		}

		$date = date("H:i:s d/M");

		$maxdata = 50; // 50 = 49 lines

		$lines = file("$savefile");
		$add = "<?die ('Access unempowered')?>|$name|$email|$yousay|$date";
		$openfile = fopen("$savefile","w");
		fwrite($openfile, "$add\n");
		for ($i = 0; $i < $maxdata; $i++){
		@fwrite($openfile, "$lines[$i]");
		}
		fclose($openfile);

		$reconvert = ereg_replace("andmark","&",$query);

		echo "<script>window.location='$reconvert'</script>";

	endif;

endif;
?>