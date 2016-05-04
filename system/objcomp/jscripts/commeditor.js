function getaddcommentform()
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Новый комментарий',req.responseJS.html,600);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST','request.php?mode=object&item=commeditor&authcode='+AUTHCODE+'&action=getaddcommentform', true);
  req.send({ iditemcomm: getParam('iditemcomm',0), section: SECTION, item: ITEM });
}
function geteditcommentform(id)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Редактор комментария',req.responseJS.html,600);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST','request.php?mode=object&item=commeditor&authcode='+AUTHCODE+'&action=geteditcommentform', true);
  req.send({ id : id, section: SECTION, item: ITEM });
}
function comment_form(form)
{ if(form.name.value.replace(/\s+/,'').length==0)
  { alert("Пожалуйста, заполните имя."); return false; }
  if(form.message.value.replace(/\s+/,'').length==0)
  { alert("Пожалуйста, заполните текст сообщения."); return false; }
  return true;
}
function delcomment(id)
{ if(confirm('Действительно удалить комментарий?'))
  goactionurl('admin.php?mode='+MODE+'&item='+ITEM+'&authcode='+AUTHCODE+'&obj_action=comm_del&id='+id+'&iditemcomm='+getParam('iditemcomm',0));
}
function checkcommentall(check)
{ for(i=0;;i++)
  if(obj=$('checkcomm'+i))
  obj.checked=check;
  else
  break;
}
function runcommentaction(action,form)
{ count=0;
  for(i=0;;i++)
  if(obj=$('checkcomm'+i))
  { if(obj.checked)
	count++;
  }
  else
  break;
  if(count==0)
  { form.elements.action.value='';
    return;
  }
  if(action=='comm_delete')
  { if(confirm('Действительно удалить отмеченные комментарии?'))
    form.submit();
  }
  else
  form.submit();
}
function InsertEditText(element, text, pos)
{ element.value = element.value.slice(0, pos) + text + element.value.slice(pos);
}
function addTag(tag)
{ var t1 = '[' + tag + ']', t2 = '[/' + tag + ']';
  if(((navigator.userAgent.toLowerCase().indexOf("msie") != -1)))
  { if(document.selection)
    { $('message').focus();
      var txt = $('message').value;
      var str = document.selection.createRange();
      if(str.text == '') str.text = t1 + t2;
      else if(txt.indexOf(str.text) >= 0) str.text = t1 + str.text + t2;
      else $('message').value = txt + t1 + t2;
      str.select();
    }
  }
  else
  { var element = $('message');
    var sel_start = element.selectionStart;
    var sel_end = element.selectionEnd;
    InsertEditText(element, t1, sel_start);
    InsertEditText(element, t2, sel_end+t1.length);
    element.selectionStart = sel_start;
    element.selectionEnd = sel_end+t1.length+t2.length;
    element.focus();
  }
}
function addSmile(tag)
{ if(((navigator.userAgent.toLowerCase().indexOf("msie") != -1)))
  { if(document.selection)
    { $('message').focus();
      var txt = $('message').value;
      var str = document.selection.createRange();
      if(str.text == '') str.text = tag;
      else if(txt.indexOf(str.text) >= 0) str.text = tag;
      else $('message').value = txt + tag;
      str.select();
    }
  }
  else
  { var element = $('message');
    var sel_start = element.selectionStart;
    var sel_end = element.selectionEnd;
    InsertEditText(element, tag, sel_end);
    element.selectionStart = sel_start;
    element.selectionEnd = sel_end+tag.length;
    element.focus();
  }
}