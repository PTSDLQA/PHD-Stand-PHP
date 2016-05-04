function getaddblockform()
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Новый блок',req.responseJS.html,900,"onchangetype_add('text');");
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=site&item=blocks&authcode='+AUTHCODE+'&action=getaddblockform', true);
  req.send({  });
}
function geteditblockform(id)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Свойства блока',req.responseJS.html,900,"onchangetype_edit("+id+",'"+req.responseJS.block+"','"+req.responseJS.block+"');");
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=site&item=blocks&authcode='+AUTHCODE+'&action=geteditblockform', true);
  req.send({ id: id });
}
function delblock(id)
{ if(confirm('Действительно удалить блок?'))
  goactionurl('/admin.php?mode=site&item=blocks&authcode='+AUTHCODE+'&action=del&id='+id);
}
function onchangetype_add(type)
{ var req = new JsHttpRequest();
  message_loading('optionsbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('optionsbox').innerHTML = req.responseJS.html;
	  tags_ini();
	  modal_refresh();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=block&item='+type+'&authcode='+AUTHCODE+'&action=add', true);
  req.send({  });
}
function onchangetype_edit(id,type,type2)
{ if(type!=type2)
  { onchangetype_add(type);
    return;
  }
  var req = new JsHttpRequest();
  message_loading('optionsbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('optionsbox').innerHTML = req.responseJS.html;
	  tags_ini();
	  modal_refresh();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=block&item='+type+'&authcode='+AUTHCODE+'&action=edit', true);
  req.send({ id: id });
}
function selalign(form,align)
{ if(align=='left')
  form.frame.value='leftblock.tpl';
  else if(align=='right')
  form.frame.value='rightblock.tpl';
  else
  form.frame.value='';
  $('showoptions').style.display = align=='free' ? 'none' : '';
  modal_refresh();
}
function block_form(form)
{ if(form.elements.caption.value.replace(/\s+/,'').length==0)
  { alert("Пожалуйста, укажите название."); return false; }
  else if(form.elements.align.value=='free' && !/^[a-zA-Z0-9_\-]+$/i.test(form.elements.name.value))
  { alert("Пожалуйста, корректно заполните идентификатор (только латинские буквы и цифры)."); return false; }
  else if(form.elements.alldomains && form.elements.alldomains.checked && !/^[a-zA-Z0-9_\-]+$/i.test(form.elements.name.value))
  { alert("Пожалуйста, корректно заполните идентификатор (только латинские буквы и цифры)."); return false; }
  return true;
}
function sellang(id,lang)
{ var req = new JsHttpRequest();
  message_loading('showoptions');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('showoptions').innerHTML = req.responseJS.html;
	  modal_refresh();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=site&item=blocks&authcode='+AUTHCODE+'&action=getshowoptions', true);
  req.send({ id: id, lang: lang });
}
function showallcheck(check)
{ $('showoptions2').style.display = check ? 'none' : '';
  modal_refresh();
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
  { if(obj.checked)
	count++;
  }
  else
  break;
  if(count==0)
  { form.elements.action.value='';
    return;
  }
  if(action=='delete')
  { if(confirm('Действительно удалить отмеченные блоки?'))
    form.submit();
  }
  else
  form.submit();
}
function setblocksort(obj)
{ var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=site&item=blocks&authcode='+AUTHCODE+'&action=setsort', true);
  req.send({ sort: Sortable.sequence(obj).join(',') });
}