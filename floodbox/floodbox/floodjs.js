function openpopupall(){
var popurl="floodbox.php?action=viewall"
winpops=window.open(popurl,"","width=300,height=500,scrollbars,resizable,")
}
function openpopuphelp(){
var popurl="floodbox.php?action=help"
winpops=window.open(popurl,"","width=320,height=500,scrollbars,resizable,")
}
//count chars

var ns6=document.getElementById&&!document.all

function restrictinput(maxlength,e,placeholder){
if (window.event&&event.srcElement.value.length>=maxlength)
return false
else if (e.target&&e.target==eval(placeholder)&&e.target.value.length>=maxlength){
var pressedkey=/[a-zA-Z0-9\.\,\/]/ 
if (pressedkey.test(String.fromCharCode(e.which)))
e.stopPropagation()
}
}

function countlimit(maxlength,e,placeholder){
var theform=eval(placeholder)
var lengthleft=maxlength-theform.value.length
var placeholderobj=document.all? document.all[placeholder] : document.getElementById(placeholder)
if (window.event||e.target&&e.target==eval(placeholder)){
if (lengthleft<0)
theform.value=theform.value.substring(0,maxlength)
placeholderobj.innerHTML=lengthleft
}
}


function displaylimit(theform,thelimit){
var limit_text='<font face=Verdana><b><span id="'+theform.toString()+'">'+thelimit+'</span></b></font>'
if (document.all||ns6)
document.write(limit_text)
if (document.all){
eval(theform).onkeypress=function(){ return restrictinput(thelimit,event,theform)}
eval(theform).onkeyup=function(){ countlimit(thelimit,event,theform)}
}
else if (ns6){
document.body.addEventListener('keypress', function(event) { restrictinput(thelimit,event,theform) }, true); 
document.body.addEventListener('keyup', function(event) { countlimit(thelimit,event,theform) }, true); 
}
}

// sobmit once
function submitonce(theform){
if (document.all||document.getElementById){
for (i=0;i<theform.length;i++){
var tempobj=theform.elements[i]
if(tempobj.type.toLowerCase()=="submit"||tempobj.type.toLowerCase()=="reset")
tempobj.disabled=true
}
}
}
