function getaddadminform()
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Новый администратор',req.responseJS.html,800);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=system&item=admins&authcode='+AUTHCODE+'&action=getaddadminform', true);
  req.send({  });
}
function addadmin_form(form)
{ if(!/^[a-zA-Z0-9_\-]+$/i.test(form.elements.login.value))
  { alert("Пожалуйста, корректно укажите логин (только латинские буквы и цифры)."); return false; }
  if(form.elements.login.value.replace(/\s+/,'').length<3)
  { alert("Пожалуйста, укажите логин не короче 3х символов."); return false; }
  if(form.elements.password.value.replace(/\s+/,'').length<4)
  { alert("Пожалуйста, укажите пароль не короче 4х символов."); return false; }
  return true;
}
function geteditadminform(id)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Свойства администратора',req.responseJS.html,800);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=system&item=admins&authcode='+AUTHCODE+'&action=geteditadminform', true);
  req.send({ id: id });
}
function editadmin_form(form)
{ if(!/^[a-zA-Z0-9_\-]+$/i.test(form.elements.login.value))
  { alert("Пожалуйста, корректно укажите логин."); return false; }
  if(form.elements.login.value.length<3)
  { alert("Пожалуйста, укажите логин не короче 3х символов."); return false; }
  if(form.elements.password.value.length>0 && form.elements.password.value.length<4)
  { alert("Пожалуйста, укажите пароль не короче 4х символов."); return false; }
  if(form.elements.password.value.length>=4 &&
  !confirm("Вы действительно хотите установить новый пароль для этого администратора?")) return false;
  return true;
}
function deladmin(id)
{ if(confirm('Действительно удалить администратора?'))
  goactionurl('admin.php?mode=system&item=admins&authcode='+AUTHCODE+'&action=del&id='+id+'&page='+getParam('page',0));
}