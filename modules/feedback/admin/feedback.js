function geteditpageform()
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html(req.responseJS.title,req.responseJS.html,900);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=geteditpageform', true);
  req.send({ });
}
function getaddfieldform()
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Новое поле',req.responseJS.html,600);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=getaddfieldform', true);
  req.send({ });
}
function geteditfieldform(id)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Редактор поля',req.responseJS.html,600);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=geteditfieldform', true);
  req.send({ id: id });
}
function field_form(form)
{ if(!/^[a-zA-Z0-9_\-]+$/i.test(form.field.value))
  { alert("Пожалуйста, корректно заполните идентификатор поля (только латинские буквы и цифры)."); return false; }
  if(form.name.value.length==0)
  { alert("Пожалуйста, заполните название поля."); return false; }
  if((form.type.value=='select' || form.type.value=='mselect') && form.idvar.length==0)
  { alert("Пожалуйста, выберите список."); return false; }
  return true;
}
function page_form(form)
{ if(typeof FCKeditorAPI!='undefined')
  form.content.value=FCKeditorAPI.GetInstance('content').GetHTML();
  return true;
}
function delfield(id)
{ if(confirm('Действительно удалить поле?'))
  goactionurl('admin.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=fld_del&id='+id+'&tab=page');
}
function fieldseltype(type)
{ $('field_typestringbox').style.display=type=='string'?'':'none';
  $('field_typeboolbox').style.display=type=='bool'?'':'none';
  $('field_typetextbox').style.display=type=='text'?'':'none';
  $('field_typeformatbox').style.display=type=='format'?'':'none';
  $('field_typeselectbox').style.display=type=='select'||type=='mselect'?'':'none';
  modal_refresh();
}
function getarchmessageform(id)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Сообщение',req.responseJS.html,800);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=getmessageform', true);
  req.send({ id : id });
}
function delarch(id)
{ if(confirm('Действительно удалить сообщение?'))
  goactionurl('admin.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=delarch&id='+id+'&tab=arch');
}
function checkall(check)
{ for(i=0;;i++)
  if(obj=$('check'+i))
  obj.checked=check;
  else
  break;
}
function runaction(action,form)
{ count=0;
  for(i=0;;i++)
  if(obj=$('check'+i))
  { if(obj.checked)	count++;
  }
  else
  break;
  if(count==0)
  { form.elements.action.value='';
    return;
  }
  if(action=='delete')
  { if(confirm('Действительно удалить отмеченные записи?'))
    form.submit();
  }
}
function setfieldsort(obj)
{ var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=setsort', true);
  req.send({ sort: Sortable.sequence(obj).join(',') });
}