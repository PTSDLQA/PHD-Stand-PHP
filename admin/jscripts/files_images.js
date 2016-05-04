function im_getuploadform()
{ runLoading();
  var req = new JsHttpRequest();    
  req.onreadystatechange = function() 
  { if(req.readyState==4 && req.responseJS) 
    modal_window_html('Загрузить изображение',req.responseJS.html,600);    
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;    
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=files&item=images&authcode='+AUTHCODE+'&action=getuploadform',true);
  req.send({ });  
}
function im_geteditform(id)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function() 
  { if(req.readyState==4 && req.responseJS) 
    modal_window_html(req.responseJS.title,req.responseJS.html,600);  	 	
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;    
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=files&item=images&authcode='+AUTHCODE+'&action=geteditform',true);
  req.send({ id: id });
}
function im_runupload(form)
{ if(form.elements.uploadfile.value.length==0)
  { alert('Укажите файл!'); return false; }
  return true;
}
function  im_del(id)
{ if(confirm('Вы действительно хотите удалить изображение?'))
  goactionurl('/admin.php?mode=files&item=images&authcode='+AUTHCODE+'&action=del&id='+id+'&page='+getParam('page',0));
}
function checkall(check)
{ for(i=0;i<cimages;i++)  
  { obj=$('check'+i);
    if(obj)
	obj.checked=check;
  }	
}
function runaction(action,form)
{ count=0;
  for(i=0;i<cimages;i++) 
  { obj=$('check'+i);
    if(obj && obj.checked) 
	count++;	
  }	
  if(count==0) 
  { form.elements.action.value='';
    return;
  } 
  if(action=='delete')
  { if(confirm('Действительно удалить отмеченные изображения?'))
    form.submit();
  }
  else
  form.submit();
}
