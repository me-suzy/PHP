<?
/////////  FlatFileCounter v1.0     //////////
//     copyright & written in Aug 2003      //
//     by Lukas Stalder                     //
//     contact: flounter@planetluc.com      //
//     Visit www.planetluc.com!             //
//               enjoy it!                  //
//////////////////////////////////////////////

/* 

Installation

1. change the $expire variable's value to whatever you want
2. upload the 2 files (flount.php, flount.log)
3. chmod the flount.log file to 777
4. include the flount.php wherever you want to cont your visits
   by inserting   <? include('flount.php');?>   into your site
   
   
  that's it!

*/



$expire= 600;  					// ip expires after $expire seconds
$logfile= "flount.log"; 		// file where visits and ip logs are stored

// *************************** don't change anything below this line *************************
$ip= getenv('REMOTE_ADDR');
$visits=0;
$badhit= false;
$now= time();


$ips = array(array());
if (file_exists($logfile)){
	if ($loggedips=file($logfile)){
		$visits=trim($loggedips[0]);
		for ($i=1; $i< count($loggedips); $i++){
			$loggedips[$i]=trim($loggedips[$i]);
			$ips[$i] = explode('||', $loggedips[$i]);
			if (($ips[$i][0]==$ip) && ($now-$ips[$i][1]< $expire)) 
				$badhit= true;
		}
		if ($badhit)
			echo $visits;
		else{
			$visits++;
			$fp= fopen($logfile, 'w');
			fputs($fp,"$visits\n");
			for ($i=1; $i< count($loggedips); $i++){
				if ($now-$ips[$i][1] < $expire)
					fputs($fp, $ips[$i][0]."||".$ips[$i][1]."\n");
			}
			fputs($fp, "$ip||$now\n");
			fclose($fp);
			echo $visits;			
		}
	}
}else
	echo "logfile is missing";



?>