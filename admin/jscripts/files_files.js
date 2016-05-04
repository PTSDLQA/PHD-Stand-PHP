function fm_getuploadform()
{ runLoading();
  var req = new JsHttpRequest();   
  req.onreadystatechange = function() 
  { if(req.readyState==4 && req.responseJS) 
    modal_window_html('Загрузить файл',req.responseJS.html,600);    
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;    
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=files&item=files&authcode='+AUTHCODE+'&action=getuploadform',true);
  req.send({ });  
}
function fm_getregisterform()
{ runLoading();
  var req = new JsHttpRequest();  
  req.onreadystatechange = function() 
  { if(req.readyState==4 && req.responseJS) 
    modal_window_html('Зарегистрировать файл(ы)',req.responseJS.html,600);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;    
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=files&item=files&authcode='+AUTHCODE+'&action=getregisterform',true);
  req.send({ });  
}
function fm_runupload(form)
{ if(form.uploadfile.value.length==0)
  { alert('Укажите файл!'); return false; }
  return true;
}
function fm_runregister(form)
{ if($('type1').checked && form.path.value.length==0)
  { alert('Укажите путь к файлу или каталогу!'); return false; }  
  if($('type2').checked && form.xlsfile.value.length==0)
  { alert('Укажите файл excel!'); return false; }
  return true;
}
function fm_geteditform(id)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function() 
  { if(req.readyState==4 && req.responseJS)
    modal_window_html(req.responseJS.title,req.responseJS.html,600);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=files&item=files&authcode='+AUTHCODE+'&action=geteditform',true);
  req.send({ id: id });
}
function  fm_del(id)
{ if(confirm('Вы действительно хотите удалить Файл?'))
  goactionurl('/admin.php?mode=files&item=files&authcode='+AUTHCODE+'&action=del&id='+id+'&page='+getParam('page',0));
}
function checkall(check)
{ for(i=0;i<cfiles;i++)  
  { obj=$('check'+i);
    if(obj)
	obj.checked=check;
  }	
}
function runaction(action,form)
{ count=0;
  for(i=0;i<cfiles;i++) 
  { obj=$('check'+i);
    if(obj && obj.checked) 
	count++;	
  }	
  if(count==0) 
  { form.elements.action.value='';
    return;
  } 
  if(action=='delete')
  { if(confirm('Действительно удалить отмеченные файлы?'))
    form.submit();
  }
  else
  form.submit();
}
