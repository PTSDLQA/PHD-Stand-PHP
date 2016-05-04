function getuploadform()
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Импорт расширения',req.responseJS.html,600);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=system&item=extensions&authcode='+AUTHCODE+'&action=getuploadform', true);
  req.send({ tab: tabs_id });
}
function extexport(id)
{ if(confirm('Экспортировать файл расширения?'))
  goactionurl('admin.php?mode=system&item=extensions&authcode='+AUTHCODE+'&action=export&id='+id);
}
function delextension(id)
{ if(confirm('Действительно удалить из системы?'))
  goactionurl('admin.php?mode=system&item=extensions&authcode='+AUTHCODE+'&action=uninstall&id='+id+'&tab='+tabs_id);
}
function moreextensions()
{ if(obj=$('moreextensions'))
  { obj.style.display=obj.style.display=='' ? 'none' : '';
  }
  modal_refresh();
}
function setextensionsort(obj)
{ var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=system&item=extensions&authcode='+AUTHCODE+'&action=setsort', true);
  req.send({ sort: Sortable.sequence(obj).join(',') });
}