var opt_fedit=false;
var opt_prev='';
var opt_tab='opt';
function geteditoptform(id,idgroup)
{ if(opt_fedit) return;
  opt_prev=$('opt_'+idgroup+'_'+id).innerHTML;
  var req = new JsHttpRequest();
  message_loading('opt_'+idgroup+'_'+id);
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('opt_'+idgroup+'_'+id).innerHTML = req.responseJS.html;
	  opt_fedit=true;
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=optionsbox&authcode='+AUTHCODE+'&action=geteditoptform', true);
  if(MODE=='sections')  
  req.send({ id: id, section: SECTION, item: ITEM });
  else if(MODE=='structures')  
  req.send({ id: id, structure: STRUCTURE, item: ITEM });
  else
  req.send({ id: id, item: ITEM });
}
function cancelopt(id,idgroup)
{ $('opt_'+idgroup+'_'+id).innerHTML = opt_prev;
  opt_fedit=false;
}
function saveopt(id,idgroup,type)
{ if(type=='bool')
  optvalue=document.forms.editoptform.elements.value.checked?1:0;
  else if(type=='date')
  optvalue=document.forms.editoptform.elements.date.value;
  else
  optvalue=document.forms.editoptform.elements.value.value;  
  var req = new JsHttpRequest();
  message_saving('opt_'+idgroup+'_'+id);
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('opt_'+idgroup+'_'+id).innerHTML = req.responseJS.newvalue;
	  if(req.responseJS.refresh)
	  goactionurl('admin.php?mode='+MODE+'&item='+ITEM+'&tab='+opt_tab);
	  else
	  opt_fedit=false;
	}  
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;  
  req.open('POST', 'request.php?mode=object&item=optionsbox&authcode='+AUTHCODE+'&action=saveopt', true);
  if(MODE=='sections')  
  req.send({ id: id, value: optvalue, type: type, section: SECTION, item: ITEM });
  else if(MODE=='structures')  
  req.send({ id: id, value: optvalue, type: type, structure: STRUCTURE, item: ITEM });
  else
  req.send({ id: id, value: optvalue, type: type, item: ITEM }); 
}

