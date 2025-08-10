/* Creation date: 4.4.2003 */
  function Confirm(link,text) 
  {
   if (confirm(text))
      window.location=link
  }
  function expw(listID) 
  {
   if (listID.style.display=="none") {listID.style.display="";}
   else {listID.style.display="none";}
   window.event.cancelBubble=true;
  }
  function contw(listID) 
  {
   if (listID.style.display=="show") {listID.style.display="";}
   else {listID.style.display="none";}
   window.event.cancelBubble=true;
  }
  if (document.images) 
  { 
   img1on = new Image();
   img1on.src = "./gfx/but1.jpg"; 
   img1off = new Image(); 
   img1off.src = "./gfx/but2.jpg"; 
  }
  function imgOn(imgName) { if (document.images) { document[imgName].src = eval(imgName + "on.src"); } }
  function imgOff(imgName){ if (document.images) { document[imgName].src = eval(imgName + "off.src"); } }

function put_smiley(smiley,outp)
{
 if(outp == 'preview')
 {
  document.forms['novica'].preview.value += ' ' + smiley + ' ';
  document.forms['novica'].preview.focus();
 }
 if(outp == 'message')
 {
  document.forms['novica'].message.value += ' ' + smiley + ' ';
  document.forms['novica'].message.focus();
 }
}

function insert_tag(mytag,outp)
{
 if(outp == 'preview')
 {
  document.forms['novica'].preview.value += ' [' + mytag + '] ';
  document.forms['novica'].preview.focus();
 }
 if(outp == 'message')
 {
  document.forms['novica'].message.value += ' [' + mytag + '] ';
  document.forms['novica'].message.focus();
 }	
}
var winnew,ret;
winnew = 0;
winpix = 0;
ret = 0;
function PixWindow(handle)
{
 ret = handle;
 winpix=window.open("newspix.php","previewWin","toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=650,height=400");
}

function PreviewWindow()
{
  winnew=window.open("","previewWin","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=400,height=400");

 winnew.document.write('<html>');
 winnew.document.write('<head>');
 winnew.document.write('<title><?=_PREVIEW;?></title>');
 winnew.document.write('<link rel="stylesheet" type="text/css" href="css/preview.css" />');
 winnew.document.write('<meta http-equiv="Content-Type" content="text/html; charset=<?=_CHARSET;?>" />');
 winnew.document.write('</head>');
 winnew.document.write('<body bgcolor="#999999">');
 winnew.document.write('<table width="99%" bgcolor="#000000" cellspacing="1" cellpadding="3" align="center" class="MojText"><tr><td bgcolor="#ffffff">');
 winnew.document.write('<p id="tiph" style="width:99%; text-align:justify;"></p>');
 winnew.document.write('<p id="tipp" style="width:99%; text-align:justify;"></p>');
 winnew.document.write('<p id="tipm" style="width:99%; text-align:justify;"></p>');
 winnew.document.write('</td></tr></table></body></html>')
 if (winnew.focus) {winnew.focus()}
}

function ResetPreview()
{
  tiph.innerHTML = '';
  tipp.innerHTML = '';
  tipm.innerHTML = '';
}

function checkAll(field)
{
  for(i = 0; i < field.elements.length; i++)
     field[i].checked = true ;
}

function uncheckAll(field)
{
 for(i = 0; i < field.elements.length; i++)
    field[i].checked = false ;
}


function storeCaret (textEl)
{
 if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}

function insertAtCaret (textEl, text)
{
 if (textEl.createTextRange && textEl.caretPos)
  {
   var caretPos = textEl.caretPos;
   caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
  }
 else
  textEl.value  = text;
}
