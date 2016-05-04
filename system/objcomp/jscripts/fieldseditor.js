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
  req.open('POST', 'request.php?mode=object&item=fieldseditor&authcode='+AUTHCODE+'&action=getaddfieldform', true);
  if(MODE=='sections')
  req.send({ usefill: fields_usefill, usesearch: fields_usesearch, usenofront: fields_usenofront, section: SECTION, item: ITEM });
  else
  req.send({ usefill: fields_usefill, usesearch: fields_usesearch, usenofront: fields_usenofront, structure: STRUCTURE, item: ITEM });
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
  req.open('POST', 'request.php?mode=object&item=fieldseditor&authcode='+AUTHCODE+'&action=geteditfieldform', true);
  if(MODE=='sections')
  req.send({ id: id, usefill: fields_usefill, usesearch: fields_usesearch, usenofront: fields_usenofront, section: SECTION, item: ITEM });
  else
  req.send({ id: id, usefill: fields_usefill, usesearch: fields_usesearch, usenofront: fields_usenofront, structure: STRUCTURE, item: ITEM });
}
function field_form(form)
{ if(!/^[a-zA-Z0-9_\-]+$/i.test(form.field.value))
  { alert("Пожалуйста, корректно заполните идентификатор поля (только латинские буквы и цифры)."); return false; }
  if(/^[0-9]+$/i.test(form.field.value))
  { alert("Пожалуйста, корректно заполните идентификатор поля (только латинские буквы и цифры)."); return false; }
  if(form['name_'+LANG].value.replace(/\s+/,'').length==0)
  { alert("Пожалуйста, заполните название поля."); return false; }
  return true;
}
function delfield(id)
{ if(confirm('Действительно удалить поле?'))
  goactionurl('admin.php?mode='+MODE+'&item='+ITEM+'&authcode='+AUTHCODE+'&obj_action=fld_del&id='+id);
}
function fieldseltype(type)
{ $('field_typestringbox').style.display=type=='string'?'':'none';
  $('field_typeboolbox').style.display=type=='bool'?'':'none';
  $('field_typetextbox').style.display=type=='text'?'':'none';
  $('field_typeformatbox').style.display=type=='format'?'':'none';
  modal_refresh();
}
function setfieldssort(obj)
{ var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=fieldseditor&authcode='+AUTHCODE+'&action=setsort', true);
  if(MODE=='sections')
  req.send({ sort: Sortable.sequence(obj).join(','), section: SECTION, item: ITEM });
  else
  req.send({ sort: Sortable.sequence(obj).join(','), structure: STRUCTURE, item: ITEM });
}