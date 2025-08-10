<?
function output_content($input_content) {

	$fix = str_replace("<br>"," <br> ",$input_content);

	$div_input_content = explode(" ",$fix);
	$count_input_content = count($div_input_content);

	for($i=0;$i<=$count_input_content;$i++):

		if (ereg("^.+@.+\\..+$", $div_input_content[$i])):
	$div_input_content[$i] = "[<a href=\"mailto:$div_input_content[$i]\">email</a>]";
			$check = true;
		elseif ((eregi("^[http://].+www.+\\..",$div_input_content[$i])) || (eregi("www.+\\..",$div_input_content[$i]))):
			$div_input_content[$i] = str_replace("http://","",$div_input_content[$i]);
			$div_input_content[$i] = "[<a href=\"http://$div_input_content[$i]\" target=\"_blank\">link</a>]";
			$check = true;
		else:
			$check = false;
		endif;

		if((strlen($div_input_content[$i])>25) && ($check==false)):
			$div_input_content[$i] = wordwrap($div_input_content[$i],25," ",1);
			if(!eregi("^[_\.0-9a-z-]",$div_input_content[$i])):
				$newtext .= "{f} ";
			else:
				$newtext .= $div_input_content[$i].' ';
			endif;
		else:
			$newtext .= $div_input_content[$i].' ';
		endif;

	endfor;

	return $newtext;
}
?>