var oEditor		= window.parent.InnerDialogLoaded() ;
var FCK			= oEditor.FCK ;
var FCKLang		= oEditor.FCKLang ;
var FCKConfig	= oEditor.FCKConfig ;
var variable 	= null;
var textEdit = null;
var textView = null;
var textOut = null;
var zgIn = null;
var zgOut = null;
var btnEdit  = null;
var btnView  = null;

function OnLoad()
{
  textEdit = document.getElementById('txtEditTypo');
  textView = document.getElementById('txtViewTypo');
  textOut = document.getElementById('txtOutTypo');
  btnEdit  = document.getElementById('btnEdit');
  btnView  = document.getElementById('btnView');
  zgIn  = document.getElementById('zgIn');
  zgOut  = document.getElementById('zgOut');
  oEditor.FCKLanguageManager.TranslatePage(document) ;
  textView.style.display = 'block';
  textEdit.style.display = 'none';
  btnView.style.display = 'none';
  zgIn.style.display = 'block';
  zgOut.style.display = 'none';
  textView.innerHTML = FCK.GetHTML();
  textEdit.value = FCK.GetHTML();
}

function typo_text()
{
  window.parent.SetOkButton( true ) ;

  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    document.getElementById('txtOutTypo').innerHTML=req.responseJS.html;
	if(req.responseText)
	document.getElementById('txtOutTypo').innerHTML=req.responseText;
  }
  req.caching = false;
  if(document.form1.radio[0].checked)
  req.open('POST','/request.php?mode=object&item=tipograf&authcode='+window.parent.parent.AUTHCODE+'&action=getjevix', true);
  if(document.form1.radio[1].checked)
  req.open('POST','/request.php?mode=object&item=tipograf&authcode='+window.parent.parent.AUTHCODE+'&action=getlebedev', true);
  req.send({ text: textView.innerHTML });

  textOut.style.display = 'block';
  textView.style.display = 'none';
  textEdit.style.display = 'none';
  btnView.style.display = '';
  btnEdit.style.display = 'none';
  zgIn.style.display = 'none';
  zgOut.style.display = 'block';
}

function edit_text()
{
  textEdit.style.display = 'block';
  textEdit.value = textView.innerHTML;
  textView.style.display = 'none';
  textOut.style.display = 'none';
  btnView.style.display = '';
  btnEdit.style.display = 'none';
  zgIn.style.display = 'block';
  zgOut.style.display = 'none';
}

function view_text()
{
  textView.innerHTML = textEdit.value;
  textOut.style.display = 'none';
  textView.style.display = 'block';
  textEdit.style.display = 'none';
  btnView.style.display = 'none';
  btnEdit.style.display = '';
  zgIn.style.display = 'block';
  zgOut.style.display = 'none';
}

function Ok()
{
  FCK.Focus();
  var B = FCK.SetHTML(document.getElementById('txtOutTypo').innerHTML);
  window.parent.Cancel( true ) ;
}

var oRange = null ;
