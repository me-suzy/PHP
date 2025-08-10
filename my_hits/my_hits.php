<?php

/* Don't forget to CHMOD $file to 777 */
$file = "my_hits.txt";


// Obtain users IP address
$ipadd = getenv(REMOTE_ADDR);
$addip = "TRUE";
$hits = 0;


if (file_exists($file))
{
} else
{
echo "$file does not exist!";
exit;
}


// Open $file and search each line (IP address) for a match
$fp = fopen($file,"r");
while (!feof($fp))
{
$line = fgets($fp, 4096); //gets one line at a time
$line=trim($line);
if ($line != "")
{
$hits++;
}
// If IP is already logged
if ($line==$ipadd)
{
$addip = "FALSE";
}
}
fclose($fp);

// If the IP was not previously logged, append it to $file
if ($addip == "TRUE")
{
$fp = fopen($file,"a");
fwrite($fp, "\n");
fwrite($fp, $ipadd);
fclose($fp);
$hits++;
}

// Display hits
echo $hits;
?>