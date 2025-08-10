<?php
# admin_volume.php
# 
# The volume page

/*
 * tunez
 *
 * Copyright (C) 2003, Ivo van Doesburg <idoesburg@outdare.nl>
 *  
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 */

include ("tunez.inc.php");

if (!($_SESSION[perms][p_volume])) {
	header("Location: access_denied.php?page=$_SERVER[REQUEST_URI]");
	die;
}
$title = "Turn up the volume!";
include ("header.inc.php");

$tmixer_output = `./tmixer/tmixer -p`;

$tmixer = explode("\n", $tmixer_output);

?>
<SCRIPT src="js/slider3.js"></SCRIPT>


<SCRIPT>

function writeSliders() {
<?php
for($i=0;$i < count($tmixer) -1;$i++)
{
	$device = trim(substr($tmixer[$i],0,8));
	$volume = trim(substr($tmixer[$i],8,4));

    echo "window." . $device . "Slider = new Slider(\"" . $device . "Slider\");\n";
    echo $device . "Slider.imgPath = \"images/\";\n";
    echo $device . "Slider.onchange = \"document.frictionForm." . $device . "Input.value=toDecimals(this.getValue(),0);\";\n";
    echo $device . "Slider.displaySize = 3;\n";
    echo $device . "Slider.leftValue = 100;\n";
    echo $device . "Slider.rightValue = 0;\n";
    echo $device . "Slider.defaultValue = $volume;\n";
	echo $device . "Slider.orientation = \"v\";\n\n";
	echo $device . "Slider.writeSlider();\n\n";

	//echo "<tr><td>$device</td><td><input type=\"text\" name=\"device[$device]\" value=\"$volume\"></td></tr>";

	}
?>

}


function onLoad() {
<?php
for($i=0;$i < count($tmixer) -1;$i++)
{
	$device = trim(substr($tmixer[$i],0,8));

    echo $device . "Slider.placeSlider(\"" . $device . "Rail\");\n";
}
?>
}

</SCRIPT>

<FORM name=frictionForm onsubmit="return false">

<table>
<tr>
<?php
for($i=0;$i < count($tmixer) -1;$i++)
{
	$device = trim(substr($tmixer[$i],0,8));

	echo "<td>$device<br><img src=\"images/sliderbg.gif\" name=\"" . $device . "Rail\" ALIGN=\"middle\"></td>";
}
?>


</tr>
<tr>
<?php
for($i=0;$i < count($tmixer) -1;$i++)
{
	$device = trim(substr($tmixer[$i],0,8));
	$volume = trim(substr($tmixer[$i],8,4));

	echo "<td><INPUT onchange=" . $device . "Slider.setValue(this.value) size=5 value=$volume name=" . $device . "Input></td>";
}
?>
</form>
</tr>
</table>
<SCRIPT>


writeSliders();
onLoad();
</SCRIPT>

<?php

echo "<form method=\"get\" action=\"admin_volume_action.php\">";
echo "<table>";
for($i=0;$i < count($tmixer) -1;$i++)
{
	$device = trim(substr($tmixer[$i],0,8));
	$volume = trim(substr($tmixer[$i],8,4));
	echo "<tr><td>$device</td><td><input type=\"text\" name=\"device[$device]\" value=\"$volume\"></td></tr>";
}
echo "</table>";
echo "<input value=\"Submit\" class=\"button\" type=\"submit\"";

echo "</form>";


include ("footer.inc.php");

?>
