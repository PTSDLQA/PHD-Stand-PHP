var cur_page=0;
function indir(dir)
{ var req = new JsHttpRequest();
  message_loading('fa_gridbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    $('fa_gridbox').innerHTML = req.responseJS.html;
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=fileadmin&authcode='+AUTHCODE+'&action=getdir',true);
  req.send({ dir: dir, oid: fa_oid });
}
function inback()
{ var req = new JsHttpRequest();
  message_loading('fa_gridbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
	$('fa_gridbox').innerHTML = req.responseJS.html;
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=fileadmin&authcode='+AUTHCODE+'&action=back',true);
  req.send({ oid: fa_oid });
}
function renamefolder(folder)
{ newname=prompt('Введите новое имя каталога:',folder);
  if(!newname) return;
  if(newname.length==0)
  { alert('Пожалуйста, введите корректное имя каталога!'); return; }
  var req = new JsHttpRequest();
  message_loading('fa_gridbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('fa_gridbox').innerHTML = req.responseJS.html;
	  if(!req.responseJS.result) alert('Каталог с таким именем уже существует!');
    }
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=fileadmin&authcode='+AUTHCODE+'&action=rename&page='+cur_page,true);
  req.send({ name: folder, newname: newname, oid: fa_oid });
}
function  delfolder(folder)
{ if(!confirm('Вы действительно хотите удалить каталог "'+folder+'"? Все содержимое будет тоже удалено.')) return;
  var req = new JsHttpRequest();
  message_loading('fa_gridbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
	{ $('fa_gridbox').innerHTML = req.responseJS.html;
	  if(!req.responseJS.result) alert('Не удалось удалить каталог!');
    }
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=fileadmin&authcode='+AUTHCODE+'&action=delfolder&page='+cur_page,true);
  req.send({ name: folder, oid: fa_oid });
}
function renamefile(file)
{ newname=prompt('Введите новое имя файла:',file);
  if(!newname) return;
  if(newname.length==0 || !/^[a-zA-Zа-яА-Я0-9_\-]+\.[a-zA-Zа-яА-Я0-9_\-]+$/i.test(newname))
  { alert('Пожалуйста, введите корректное имя файла!'); return; }
  var req = new JsHttpRequest();
  message_loading('fa_gridbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('fa_gridbox').innerHTML = req.responseJS.html;
	  if(!req.responseJS.result) alert('Некорректное имя или файл с таким именем уже существует!');
    }
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=fileadmin&authcode='+AUTHCODE+'&action=rename&page='+cur_page,true);
  req.send({ name: file, newname: newname, oid: fa_oid });
}
function editfile(file)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { if(req.responseJS.type==1)
	  modal_window_html(req.responseJS.title,req.responseJS.html,950,'codemirror_ini('+req.responseJS.height+',"'+req.responseJS.ext+'")');
	  else if(req.responseJS.type==2)
	  { endLoading();
	    open_window(req.responseJS.fileimg,req.responseJS.width,req.responseJS.height,'imgwin',true);
	  }
	  else
	  { endLoading();
	    alert('Данный тип файла нельзя просмотреть или редактировать!');
	  }
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=fileadmin&authcode='+AUTHCODE+'&action=editfile',true);
  req.send({ name: file, oid: fa_oid });
}
function savefile(form)
{ var req = new JsHttpRequest();
  nname=form.name.value;
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
  req.open('POST', 'request.php?mode=object&item=fileadmin&authcode='+AUTHCODE+'&action=savefile',true);
  req.send({ name: nname, text: text, oid: fa_oid });
}
function applyfile(form)
{ var req = new JsHttpRequest();
  nname=form.name.value;
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
  req.open('POST', 'request.php?mode=object&item=fileadmin&authcode='+AUTHCODE+'&action=savefile',true);
  req.send({ name: nname, text: text, oid: fa_oid });
}
function  delfile(file)
{ if(!confirm('Вы действительно хотите удалить файл "'+file+'"')) return;
  var req = new JsHttpRequest();
  message_loading('fa_gridbox');
  req.onreadystatechange = function()
  { if(req.readyState == 4 && req.responseJS)
    { $('fa_gridbox').innerHTML = req.responseJS.html;
	  if(!req.responseJS.result) alert('Не удалось удалить файл!');
    }
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=fileadmin&authcode='+AUTHCODE+'&action=delfile&page='+cur_page,true);
  req.send({ name: file, oid: fa_oid });
}
function createfolder()
{ newname=prompt('Введите имя каталога:','');
  if(!newname) return;
  if(newname.length==0 || !/^[a-zA-Zа-яА-Я0-9_\-]+$/i.test(newname))
  { alert('Пожалуйста, введите корректное имя нового каталога!'); return; }
  var req = new JsHttpRequest();
  message_loading('fa_gridbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('fa_gridbox').innerHTML = req.responseJS.html;
	  if(!req.responseJS.result)
	  alert('Не удалось создать каталог! Возможно каталог с таким именем уже существует.');
    }
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=fileadmin&authcode='+AUTHCODE+'&action=mkfolder&page='+cur_page,true);
  req.send({ name: newname, oid: fa_oid });
}
function createfile()
{ newname=prompt('Введите имя файла:','');
  if(!newname) return;
  if(newname.length==0 || !/^[a-zA-Zа-яА-Я0-9_\-]+\.[a-zA-Zа-яА-Я0-9_\-]+$/i.test(newname))
  { alert('Пожалуйста, введите корректное имя файла!'); return; }
  var req = new JsHttpRequest();
  message_loading('fa_gridbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('fa_gridbox').innerHTML = req.responseJS.html;
	  if(!req.responseJS.result)
	  alert('Не удалось создать файл! Возможно файл с таким именем уже существует.');
    }
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=fileadmin&authcode='+AUTHCODE+'&action=mkfile&page='+cur_page,true);
  req.send({ name: newname, oid: fa_oid });
}
function uploadform()
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Загрузить файл',req.responseJS.html,600);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=fileadmin&authcode='+AUTHCODE+'&action=uploadform&page='+cur_page,true);
  req.send({ oid: fa_oid });
}
function runupload(form)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('fa_gridbox').innerHTML = req.responseJS.html;
	  endLoading();
	  Windows.closeAll();
	  for(i=0;i<req.responseJS.messages.length;i++)
	  alert(req.responseJS.messages[i]);
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=fileadmin&authcode='+AUTHCODE+'&action=upload&page='+cur_page,true);
  req.send({ oid: fa_oid, form: form });
}
function fagopage(page)
{ var req = new JsHttpRequest();
  message_loading('fa_gridbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('fa_gridbox').innerHTML = req.responseJS.html;
	  cur_page=page;
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=fileadmin&authcode='+AUTHCODE+'&action=gettable&page='+page,true);
  req.send({ oid: fa_oid });
}
function morefiles()
{ if(obj=$('morefiles'))
  obj.style.display=obj.style.display=='' ? 'none' : '';
  modal_refresh();
}