<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="refresh" content="10">
<title>User online</title>
<base target="detail"></head>
<body bgcolor='#FFFFCC' scroll="auto">
<br><font face="Tahoma"><u>User online:</u></font><br>
<?
$dir_handle=opendir('online/');
while ($file_entry = readdir ($dir_handle)):
    if ($file_entry != "." && $file_entry != ".."):
                $file_modified = filemtime("online/".$file_entry);
        $dir_list[$file_entry]=$file_modified;
    endif;
endwhile;
closedir($dir_handle);
arsort($dir_list);
$num_files=sizeof($dir_list);
reset($dir_list);
for ($i=0;$i<$num_files;$i++):
        list ($file_entry, $file_modified) = each ($dir_list);
        $fp=fopen("online/".$file_entry, "r");
        $datum=rtrim(fgets($fp, 4096));
        $headline=rtrim(fgets($fp, 4096));
        $image=rtrim(fgets($fp, 4096));
        echo "$headline";
        fpassthru($fp);
endfor;
?>
</body>
</html>