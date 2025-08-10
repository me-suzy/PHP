<?php

$this->js_func[] = "

var body=0;
var opcode='';

function ".$section_name."_addtag(tag) {
        document.".$form_name.".".$section_name.".value=document.".$form_name.".".$section_name.".value + tag
}

function ".$section_name."_addBold(){
        document.".$form_name.".".$section_name.".value=document.".$form_name.".".$section_name.".value + \"<b>Bold Text</b>\";
}

function ".$section_name."_addBreak(){
        document.".$form_name.".".$section_name.".value=document.".$form_name.".".$section_name.".value + \"<br />\\\n\";
}

function ".$section_name."_addItal(){
        document.".$form_name.".".$section_name.".value=document.".$form_name.".".$section_name.".value + \"<i>Italicized Text</i>\";
}

function ".$section_name."_addUnder(){
        document.".$form_name.".".$section_name.".value=document.".$form_name.".".$section_name.".value + \"<u>Underlined Text</u>\";
}

function ".$section_name."_addAleft(){
        document.".$form_name.".".$section_name.".value=document.".$form_name.".".$section_name.".value + \"<div align=\\\"left\\\">Left Justified Text</div>\";
}

function ".$section_name."_addAcenter(){
        document.".$form_name.".".$section_name.".value=document.".$form_name.".".$section_name.".value + \"<div align=\\\"center\\\">Centered Text</div>\";
}

function ".$section_name."_addAright(){
        document.".$form_name.".".$section_name.".value=document.".$form_name.".".$section_name.".value + \"<div align=\\\"right\\\">Right Justified Text</div>\";
}

function ".$section_name."_addUlist(){ 
        document.".$form_name.".".$section_name.".value=document.".$form_name.".".$section_name.".value + \"<ul type=\\\"disc\\\">\\r\\n  <li>Item 1</li>\\r\\n  <li>Item 2</li>\\r\\n  <li>Item 3</li>\\r\\n</ul>\\r\\n\";
}

function ".$section_name."_addOlist(){ 
        document.".$form_name.".".$section_name.".value=document.".$form_name.".".$section_name.".value + \"<ol type=\\\"1\\\">\\r\\n  <li>Item 1</li>\\r\\n  <li>Item 2</li>\\r\\n  <li>Item 3</li>\\r\\n</ol>\\r\\n\";
}

function ".$section_name."_addBlock(){ 
        document.".$form_name.".".$section_name.".value=document.".$form_name.".".$section_name.".value + \"<blockquote>\\r\\n  <p>Your indented text here...</p>\\r\\n</blockquote>\\r\\n\";
}

function ".$section_name."_addEmail(){ 
        document.".$form_name.".".$section_name.".value=document.".$form_name.".".$section_name.".value + \"<a href=\\\"mailto:email@address.here\\\">Click Text Here</a>\";
}

function ".$section_name."_addLink(){ 
        document.".$form_name.".".$section_name.".value=document.".$form_name.".".$section_name.".value + \"<a href=\\\"http://www.web_address.here\\\">Click Text Here</a>\";
}

";

$js = "<a name=\"$section_name\" />\n";
$js .= "<a href=\"#".$section_name."\" onclick=\"".$section_name."_addBold()\">" . phpws_text::imageTag($this->source_http ."js/wysiwyg/bold.gif", "Bold", 21, 20) . "</a>\n";
$js .= "<a href=\"#".$section_name."\" onclick=\"".$section_name."_addItal()\">" . phpws_text::imageTag($this->source_http ."js/wysiwyg/italic.gif", "Italic", 21, 20) . "</a>\n";
$js .= "<a href=\"#".$section_name."\" onclick=\"".$section_name."_addUnder()\">" . phpws_text::imageTag($this->source_http ."js/wysiwyg/underline.gif", "Underlined", 21, 20) . "</a>\n";
$js .= "<a href=\"#".$section_name."\" onclick=\"".$section_name."_addAleft()\">" . phpws_text::imageTag($this->source_http ."js/wysiwyg/aleft.gif", "Left Justified", 21, 20) . "</a>\n";
$js .= "<a href=\"#".$section_name."\" onclick=\"".$section_name."_addAcenter()\">" . phpws_text::imageTag($this->source_http ."js/wysiwyg/acenter.gif", "Center Text", 21, 20) . "</a>\n";
$js .= "<a href=\"#".$section_name."\" onclick=\"".$section_name."_addAright()\">" . phpws_text::imageTag($this->source_http ."js/wysiwyg/aright.gif", "Right Justified", 21, 20) . "</a>\n";
$js .= "<a href=\"#".$section_name."\" onclick=\"".$section_name."_addUlist()\">" . phpws_text::imageTag($this->source_http ."js/wysiwyg/bullet.gif", "Bulleted List", 21, 20) . "</a>\n";
$js .= "<a href=\"#".$section_name."\" onclick=\"".$section_name."_addOlist()\">" . phpws_text::imageTag($this->source_http ."js/wysiwyg/numbered.gif", "Numbered List", 21, 20) . "</a>\n";
$js .= "<a href=\"#".$section_name."\" onclick=\"".$section_name."_addBlock()\">" . phpws_text::imageTag($this->source_http ."js/wysiwyg/increase.gif", "Increase", 21, 20) . "</a>\n";
$js .= "<a href=\"#".$section_name."\" onclick=\"".$section_name."_addEmail()\">" . phpws_text::imageTag($this->source_http ."js/wysiwyg/email.gif", "Email", 21, 20) . "</a>\n";
$js .= "<a href=\"#".$section_name."\" onclick=\"".$section_name."_addLink()\">" . phpws_text::imageTag($this->source_http ."js/wysiwyg/link.gif", "Link", 21, 20) . "</a>\n";
$js .= "<a href=\"#".$section_name."\" onclick=\"".$section_name."_addBreak()\">" . phpws_text::imageTag($this->source_http ."js/wysiwyg/break.gif", "Break", 20, 20) . "</a><br />\n";

?>