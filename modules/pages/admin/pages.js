var cur_page=0;
function indir(id)
{ var req = new JsHttpRequest();
  message_loading('gridfilesbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('gridfilesbox').innerHTML = req.responseJS.html;
	  $('checkboxall').checked=false;
	  Sortable.create('pagesgridbox',{tag:'table',onUpdate: setpagessort});
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=indir&page='+cur_page,true);
  req.send({ id: id });
}
function gopage(page)
{ var req = new JsHttpRequest();
  message_loading('gridfilesbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('gridfilesbox').innerHTML = req.responseJS.html;
	  $('checkboxall').checked=false;
	  Sortable.create('pagesgridbox',{tag:'table',onUpdate: setpagessort});
	  cur_page=page;
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=getgrid&page='+page, true);
  req.send({  });
}
function setpagessort(obj)
{ obj.onclick= function(e){e.stopPropagation?e.stopPropagation():(e.cancelBubble=true); e.preventDefault?e.preventDefault():(e.returnValue=false);obj.onclick='';};
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=setsort', true);
  req.send({ sort: Sortable.sequence(obj).join(','), page: cur_page });
}
function getadddirform()
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Новый подраздел',req.responseJS.html,600);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=getadddirform&page='+cur_page,true);
  req.send({  });
}
function getaddpageform()
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Новая страница',req.responseJS.html,950);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=getaddpageform&page='+cur_page,true);
  req.send({  });
}
function  geteditdirform(id)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Редактор подраздела',req.responseJS.html,600);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=geteditdirform&page='+cur_page,true);
  req.send({ id: id });
}
function  getmoveform(id)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html(req.responseJS.title,req.responseJS.html,600);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=getmoveform&page='+cur_page,true);
  req.send({ id: id });
}
function  geteditpageform(id)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Редактор страницы',req.responseJS.html,950);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=geteditpageform&page='+cur_page,true);
  req.send({ id: id });
}
function applypage(form)
{ var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { if(!req.responseJS.result)
	  alert('Не удалось сохранить страницу!');
	  else
	  $('applydate').innerHTML=req.responseJS.date;
    }
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=applypage',true);
  req.send({ form: form });
}
function delitem(id,oldname)
{ if(!confirm('Вы действительно хотите удалить "'+oldname+'"?')) return;
  var req = new JsHttpRequest();
  message_loading('gridfilesbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
	{ $('gridfilesbox').innerHTML = req.responseJS.html;
	  $('checkboxall').checked=false;
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=delitem&page='+cur_page,true);
  req.send({ id: id });
}
function adddir_form(form)
{ if(!/^[a-zA-Zа-яА-Я0-9_ \-]*$/i.test(form.urlname.value))
  { alert("Пожалуйста, корректно укажите идентификатор URL."); return false; }
  if(form.name.value.replace(/\s+/,'').length==0)
  { alert("Пожалуйста, заполните название."); return false; }
  return true;
}
function editdir_form(form)
{ if(!/^[a-zA-Zа-яА-Я0-9_ \-]*$/i.test(form.urlname.value))
  { alert("Пожалуйста, корректно укажите идентификатор URL."); return false; }
  if(form.name.value.replace(/\s+/,'').length==0)
  { alert("Пожалуйста, заполните название."); return false; }
  return true;
}
function addpage_form(form)
{ if(!/^[a-zA-Zа-яА-Я0-9_ \-]*$/i.test(form.urlname.value))
  { alert("Пожалуйста, корректно укажите идентификатор URL."); return false; }
  if(form.name.value.replace(/\s+/,'').length==0)
  { alert("Пожалуйста, заполните название."); return false; }
  form.content.value=FCKeditorAPI.GetInstance('content').GetHTML();
  return true;
}
function editpage_form(form)
{ if(!/^[a-zA-Zа-яА-Я0-9_ \-]*$/i.test(form.urlname.value))
  { alert("Пожалуйста, корректно укажите идентификатор URL."); return false; }
  if(form.name.value.replace(/\s+/,'').length==0)
  { alert("Пожалуйста, заполните название."); return false; }
  form.content.value=FCKeditorAPI.GetInstance('content').GetHTML();
  return true;
}
function moveitem(form)
{ if(form.idto.value<0)
  { alert("Пожалуйста, выберите куда перемещать."); return false; }
  return true;
}
function checkall(check)
{ for(i=0;;i++)
  if(obj=$('checkp'+i))
  obj.checked=check;
  else
  break;
}
function runaction(action,form)
{ var count=0;
  var ppages = new Array();
  for(i=0;;i++)
  if(obj=$('checkp'+i))
  { if(obj.checked)
	{ ppages[i]=obj.value;
	  count++;
	}
  }
  else
  break;
  if(count==0)
  { form.elements.action.value='';
    return;
  }
  if(action=='move')
  { runLoading();
    var req = new JsHttpRequest();
    req.onreadystatechange = function()
    { if(req.readyState==4 && req.responseJS)
      { modal_window_html('Перемещение',req.responseJS.html,600);
	    form.elements.action.value='';
	  }
	  if(req.responseText)
      $('debugbox').innerHTML = req.responseText;
    }
    req.caching = false;
    req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=getmovepagesform', true);
    req.send({ pages : ppages });
    return;
  }
  else if(action=='delete')
  { if(!confirm('Действительно удалить отмеченные элементы?'))
    return;
  }
  form.submit();
}