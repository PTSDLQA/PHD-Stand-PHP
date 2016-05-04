function getindexform()
{ runLoading();
  var req = new JsHttpRequest();  
  req.onreadystatechange = function() 
  { if(req.readyState==4 && req.responseJS) 
    modal_window_html('Проиндексировать разделы',req.responseJS.html,600);  
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;    
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=getindexform', true);
  req.send({ });
}