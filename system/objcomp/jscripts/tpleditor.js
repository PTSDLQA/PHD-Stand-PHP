function  edittpl(path)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { if(req.responseJS.html)
	  modal_window_html(req.responseJS.title,req.responseJS.html,950,'codemirror_ini('+req.responseJS.height+',"tpl")');
	  else
	  { endLoading();
	    alert('Данный тип файла нельзя редактировать!');
	  }
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=tpleditor&authcode='+AUTHCODE+'&action=edittpl',true);
  req.send({ path: path });
}
function gotpl(path)
{ Windows.closeAll();
  codemirror_editor=null;
  edittpl(path);
}
function savetpl(form)
{ var req = new JsHttpRequest();
  path=form.path.value;
  text=codemirror_editor.getCode();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { if(!req.responseJS.result)
	  alert('Не удалось сохранить файл!');
	  else
	  { Windows.closeAll();
	    codemirror_editor=null;
	  }
	  if(req.responseJS.refresh)
	  document.location=document.location;
    }
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=tpleditor&authcode='+AUTHCODE+'&action=savetpl',true);
  req.send({ path: path, text: text });
}
function applytpl(form)
{ var req = new JsHttpRequest();
  path=form.path.value;
  text=codemirror_editor.getCode();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { if(!req.responseJS.result)
	  alert('Не удалось сохранить файл!');
	  if(req.responseJS.refresh)
	  document.location=document.location;
    }
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=tpleditor&authcode='+AUTHCODE+'&action=savetpl',true);
  req.send({ path: path, text: text });
}