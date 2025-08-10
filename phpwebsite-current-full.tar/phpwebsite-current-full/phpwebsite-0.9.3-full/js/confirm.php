<?php


$this->js_func[] = "
function confirmData_" . $section_name . "()
{
	if (confirm(\"$message\\nOK = YES, CANCEL = NO\"))
	location='$location';
}";

$js = $this->formButton($name, $value, "confirmData_" . $section_name . "();");

?>