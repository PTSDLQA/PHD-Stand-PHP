function voting_form(form)
{ if(form.elements.question.value.replace(/\s+/,'').length==0)
  { alert('Пожалуйста, заполните текст вопроса.'); return false; }
  return true;
}
function getaddvariantform()
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Новый вариант',req.responseJS.html,600);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=getaddvariantform', true);
  req.send({ });
}
function geteditvariantform(id)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Редактор варианта',req.responseJS.html,600);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=geteditvariantform', true);
  req.send({ id : id });
}
function delvariant(id)
{ if(confirm('Действительно удалить вариант?'))
  document.location='admin.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=delvariant&tab=vot&id='+id;
}
function variant_form(form)
{ if(form.elements.name.value.replace(/\s+/,'').length==0)
  { alert('Пожалуйста, заполните текст варианта.'); return false; }
  return true;
}
function setvariantsort(obj)
{ var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=setsort', true);
  req.send({ sort: Sortable.sequence(obj).join(',') });
}
function newvoting()
{ if(confirm('Действительно начать новое голосование?'))
  { if(confirm('Занести текущие результаты в архив?'))
    document.location='admin.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=newvoting&tab=vot&arch';
    else
    document.location='admin.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=newvoting&tab=vot';
  }
}
function getresult(id)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Результаты',req.responseJS.html,450);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&action=getresultform', true);
  req.send({ id : id });
}