function getadditemform()
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Новый альбом',req.responseJS.html,800);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=getadditemform', true);
  req.send({ idcat: cur_idcat });
}
function getedititemform(id)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Редактор альбома',req.responseJS.html,800);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=getedititemform', true);
  req.send({ id : id });
}
function item_form(form)
{ if(!/^[a-zA-Zа-яА-Я0-9_ \-]*$/i.test(form.urlname.value))
  { alert("Пожалуйста, корректно укажите идентификатор URL."); return false; }
  if(form.name.value.replace(/\s+/,'').length==0)
  { alert("Пожалуйста, заполните название."); return false; }
  return true;
}
function delitem(id)
{ if(confirm('Действительно удалить альбом?'))
  goactionurl('admin.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=delitem&id='+id+'&page='+getParam('page',0));
}
function runselectcat(form)
{ goactionurl('admin.php?mode=sections&item='+ITEM+'&idcat='+form.idcat.value+'&tab=albums');
}
function checkall(check)
{ for(i=0;;i++)
  if(obj=$('check'+i))
  obj.checked=check;
  else
  break;
}
function runaction(action,form)
{ var count=0;
  var items = new Array();
  for(i=0;;i++)
  if(obj=$('check'+i))
  { if(obj.checked)
	{ items[i]=obj.value;
	  count++;
	}
  }
  else
  break;
  if(count==0)
  { form.elements.action.value='save';
    return;
  }
  if(action=='move')
  { runLoading();
    var req = new JsHttpRequest();
    req.onreadystatechange = function()
    { if(req.readyState==4 && req.responseJS)
      { modal_window_html('Перемещение',req.responseJS.html,600);
	    form.elements.action.value='save';
	  }
	  if(req.responseText)
      $('debugbox').innerHTML = req.responseText;
    }
    req.caching = false;
    req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=getmoveitemsform', true);
    req.send({ idcat: cur_idcat, items : items });
  }
  else if(action=='delete')
  { if(confirm('Действительно удалить отмеченные альбомы?'))
    form.submit();
  }
  else
  form.submit();
}
function getaddimageform()
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Новое фото',req.responseJS.html,600);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=getaddimageform', true);
  req.send({ idalb: getParam('idalb',0) });
}
function checkall2(check)
{ for(i=0;;i++)
  if(obj=$('icheck'+i))
  obj.checked=check;
  else
  break;
}
function runaction2(action,form)
{ var count=0;
  var items = new Array();
  for(i=0;;i++)
  if(obj=$('icheck'+i))
  { if(obj.checked)
	{ items[i]=obj.value;
	  count++;
	}
  }
  else
  break;
  if(count==0)
  { form.elements.action.value='isave';
    return;
  }
  if(action=='idelete')
  { if(confirm('Действительно удалить отмеченные фото?'))
    form.submit();
  }
  else
  form.submit();
}
function setimagesort(obj)
{ obj.onclick= function(event){
  event.stopPropagation();
  event.preventDefault();
  obj.onclick='';};
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=setsort', true);
  req.send({ sort: Sortable.sequence(obj).join(',') });
}