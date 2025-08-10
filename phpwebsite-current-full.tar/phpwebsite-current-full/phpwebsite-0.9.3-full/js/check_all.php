<?php
//Code from SquirrelMail

$this->js_func[] = "
function CheckAll() {
   for (var i = 0; i < document.$form_name.elements.length; i++) {
       if( document.$form_name.elements[i].type == 'checkbox' ) {
           document.$form_name.elements[i].checked = !(document.$form_name.elements[i].checked);
       }
   }
}
";

$js = "<a href=\"#\" onclick=\"CheckAll()\" >Toggle All</a>";

?>