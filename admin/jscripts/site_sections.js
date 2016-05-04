function getaddsectionform()
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Новый раздел',req.responseJS.html,620,"inipbox('sectionpbox')");
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=site&item=sections&authcode='+AUTHCODE+'&action=getaddsectionform', true);
  req.send({  });
}
function section_addform(form)
{ if(form.elements.caption.value.replace(/\s+/,'').length==0)
  { alert("Пожалуйста, заполните название раздела."); return false; }
  else if(!/^[a-zA-Zа-яА-Я0-9_ \-]*$/i.test(form.urlname.value))
  { alert("Пожалуйста, корректно укажите идентификатор URL."); return false; }
  else if(!/^[a-zA-Z0-9]+$/i.test(form.elements.name.value))
  { alert("Пожалуйста, корректно заполните идентификатор (только латинские буквы и цифры)."); return false; }
  else if(form.elements.module.value=='')
  { alert("Пожалуйста, укажите базовый модуль."); return false; }
  return true;
}
function geteditsectionform(id)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Свойства раздела',req.responseJS.html,620,"inipbox('sectionpbox')");
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=site&item=sections&authcode='+AUTHCODE+'&action=geteditsectionform', true);
  req.send({ id: id });
}
function section_editform(form)
{ if(form.elements.caption.value.replace(/\s+/,'').length==0)
  { alert("Пожалуйста, заполните название раздела."); return false; }
  else if(!/^[a-zA-Zа-яА-Я0-9_ \-]*$/i.test(form.urlname.value))
  { alert("Пожалуйста, корректно укажите идентификатор URL."); return false; }
  else if(!/^[a-zA-Z0-9]+$/i.test(form.elements.name.value))
  { alert("Пожалуйста, корректно заполните идентификатор (только латинские буквы и цифры)."); return false; }
  return true;
}
function delsection(id)
{ if(confirm('Все данные из этого раздела будут утеряны! Действительно удалить раздел?'))
  goactionurl('admin.php?mode=site&item=sections&authcode='+AUTHCODE+'&action=del&id='+id);
}
function sellang(lang)
{ for(i=0;i<languages.length;i++)
  { $('lang_'+languages[i]).style.display = lang=='all' || languages[i]==lang ? '':'none';
    $('tlang_'+languages[i]).style.display = lang!='all' && languages[i]==lang ? 'none':'';
  }
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
  { if(confirm('Действительно удалить отмеченные разделы? Все данные из них будут потеряны!'))
    form.submit();
  }
  else
  form.submit();
}
function selmodule(form,module)
{ var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { if(form.name.value.length==0 || form.name.value=='newsection')
      form.name.value=req.responseJS.name;
      else
      { for(i=0;i<form.module.options.length;i++)
        if(form.name.value==form.module.options[i].value)
        { form.name.value=req.responseJS.name;
          break;
        }
      }
    }
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=site&item=sections&authcode='+AUTHCODE+'&action=getnewname', true);
  req.send({ module: module });
}
function setsectionsort(obj)
{ var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=site&item=sections&authcode='+AUTHCODE+'&action=setsort', true);
  req.send({ sort: Sortable.sequence(obj).join(',') });
}