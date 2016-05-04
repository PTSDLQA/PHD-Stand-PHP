var images_count=0;
var files_count=0;
function getadditemform()
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Новая запись',req.responseJS.html,960,'files_count=1;images_count=1;');
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=getadditemform', true);
  req.send({ idcat: cur_idcat });
}
function after_showedititem(id)
{ if($('images_gridbox'))
  { images_iditem=id;
    images_refresh();
  }
  if($('files_gridbox'))
  { files_iditem=id;
    files_refresh();
  }
}
function getedititemform(id)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Редактор записи',req.responseJS.html,960,'after_showedititem('+id+');');
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=getedititemform', true);
  req.send({ id : id });
}
function item_form(form)
{ if(!/^[a-zA-Zа-яА-Я0-9_ \-]*$/i.test(form.urlname.value))
  { alert("Пожалуйста, корректно укажите идентификатор URL."); return false; }
  if(form.name.value.replace(/\s+/,'').length==0)
  { alert("Пожалуйста, заполните название."); return false; }
  if(typeof FCKeditorAPI!='undefined')
  form.content.value=FCKeditorAPI.GetInstance('content').GetHTML();
  return true;
}
function delitem(id)
{ if(confirm('Действительно удалить запись?'))
  goactionurl('admin.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=delitem&id='+id+'&page='+getParam('page',0)+'&tab=items');
}
function cancelfilter()
{ $('filterbox').innerHTML = '';
}
function runselectcat(form)
{ goactionurl('admin.php?mode=sections&item='+ITEM+'&idcat='+form.idcat.value+'&tab=items');
}
function getfilterform()
{ var req = new JsHttpRequest();
  message_loading('filterbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('filterbox').innerHTML = req.responseJS.html;
	  document.forms.filterform.elements.savebutton.focus();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=getfilterform', true);
  req.send({ idcat: cur_idcat });
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
  { if(confirm('Действительно удалить отмеченные записи?'))
    form.submit();
  }
  else
  form.submit();
}
function additemimage()
{ images_count++;
  if(images_count>5) return;
  obj=$('imageitems'+images_count);
  if(obj)
  { content='<table class="invisiblegrid" width="100%">';
    content+='<tr>';
    content+='<td>Фото '+images_count+':</td>';
    content+='<td width="80%">Описание '+images_count+':</td>';
    content+='</tr>';
    content+='<tr>';
    content+='<td><input type="file" name="image'+images_count+'"></td>';
    content+='<td width="80%"><input type="text" name="imagedescription'+images_count+'" style="width:100%"></td>';
    content+='</tr>';
    content+='</table>';
	obj.innerHTML = content;
	modal_refresh();
  }
}
function additemfile()
{ files_count++;
  if(files_count>5) return;
  obj=$('fileitems'+files_count);
  if(obj)
  { content='<table class="invisiblegrid" width="100%">';
    content+='<tr>';
    content+='<td>Файл '+files_count+':</td>';
    content+='<td width="80%">Описание '+files_count+':</td>';
    content+='</tr>';
    content+='<tr>';
    content+='<td><input type="file" name="file'+files_count+'"></td>';
    content+='<td width="60%"><input type="text" name="filedescription'+files_count+'" style="width:100%"></td>';
    content+='</tr>';
    content+='</table>';
	obj.innerHTML = content;
	modal_refresh();
  }
}
function applyitem(form)
{ var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { if(!req.responseJS.result)
	  alert('Не удалось сохранить данные!');
	  else
	  $('applydate').innerHTML=req.responseJS.date;
    }
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=applyitem',true);
  req.send({ form: form });
}