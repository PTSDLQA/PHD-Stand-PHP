function geteditpageform(id)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html(req.responseJS.title,req.responseJS.html,600);  
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;    
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=site&item=pages&authcode='+AUTHCODE+'&action=geteditpageform', true);
  req.send({ id: id });  
}
function checkall(check)
{ for(i=0;i<cpages;i++)  
  { obj=$('check'+i);
    if(obj)
	obj.checked=check;
  }	
}
function runaction(action,form)
{ count=0;
  for(i=0;i<cpages;i++)
  { obj=$('check'+i);
    if(obj && obj.checked) 
	count++;
  }	
  if(count==0) 
  { form.elements.action.value='';
    return;
  }
  form.submit();
} 