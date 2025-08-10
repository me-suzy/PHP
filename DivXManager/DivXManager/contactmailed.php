<?php
include("config.php");
include("header.php");
?>

<div align="center"><br>
<?php
$ontvanger = $jouwemail;
$onderwerp = "$naam $contacttitel";
$header = "From: \"$naam\" <$email>\n";
$header = $header. "Reply-To: \"$naam\" <$email>\n";
mail($ontvanger,$onderwerp,$bericht,$header);
?>

<?echo $contactsend;?>

<META HTTP-EQUIV="Refresh" CONTENT="4;URL=index.php">
</div>

<?
include("footer.php");
?>
