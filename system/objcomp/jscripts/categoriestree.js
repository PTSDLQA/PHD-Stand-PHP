function getaddcatform(id,idker,level)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Новая категория',req.responseJS.html,800,"inipbox('"+ITEM+"_catpbox')");
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=object&item=categoriestree&authcode='+AUTHCODE+'&action=getaddcatform', true);
  req.send({ id: id, idker: idker, level: level, section: SECTION, item: ITEM });
}
function geteditcatform(id)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html('Редактор категории',req.responseJS.html,800,"inipbox('"+ITEM+"_catpbox')");
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=object&item=categoriestree&authcode='+AUTHCODE+'&action=geteditcatform', true);
  req.send({ id : id, section: SECTION, item: ITEM });
}
function getmovecatform(id)
{ runLoading();
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    modal_window_html(req.responseJS.title,req.responseJS.html,600);
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=object&item=categoriestree&authcode='+AUTHCODE+'&action=getmovecatform', true);
  req.send({ id : id, section: SECTION, item: ITEM });
}
function cat_form(form)
{ if(!/^[a-zA-Zа-яА-Я0-9_ \-]*$/i.test(form.urlname.value))
  { alert("Пожалуйста, корректно укажите идентификатор URL."); return false; }
  if(form.name.value.length==0)
  { alert("Пожалуйста, корректно заполните название категории."); return false; }
  return true;
}
function delcat(id)
{ if(confirm('Действительно удалить категорию?'))
  goactionurl('admin.php?mode=sections&item='+ITEM+'&authcode='+AUTHCODE+'&obj_action=ct_del&id='+id+'&tab=cat');
}
function tc_moveitem(obj)
{ var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=categoriestree&authcode='+AUTHCODE+'&action=setsortbranch', true);
  req.send({ sort: Sortable.sequence(obj).join(','), section: SECTION, item: ITEM });
}
function tc_sortable()
{ var levels = new Array();
  var maxlevel=0;
  if(obj=$('dbetbj0'))
  { var items=obj.getElementsBySelector('input');
    for(i=0;i<items.length;i++)
    { var level=items[i].value;
      if(level>maxlevel)
      maxlevel=level;
      if(!levels[level])
	  levels[level] = new Array();
	  levels[level][levels[level].length]=items[i];
    }
    for(level=maxlevel;level>=0;level--)
    if(levels[level] && levels[level].length)
    for(i=levels[level].length-1;i>=0;i--)
    { id=levels[level][i].id.replace(/^treeiteml_/,'dbetbj');
      if($(id).innerHTML)
	  Sortable.create(id,{tag:'div',onUpdate: tc_moveitem});
    }
    Sortable.create('dbetbj0',{tag:'div',onUpdate: tc_moveitem});
  }
}
function tc_togglePlusJ(id)
{ if(!(img=$(id+'_bullet'))) return false;
  if(!(obj=$(id))) return false;
  if(!obj.style) return false;
  if(obj.style.display=="none")
  { obj.style.display="";
	img.src="/templates/admin/images/expand.gif";
    var req = new JsHttpRequest();
	message_loading(id);
    req.onreadystatechange = function()
    { if(req.readyState==4 && req.responseJS)
	  { obj.innerHTML = req.responseJS.html;
	    tc_sortable();
	  }
	  if(req.responseText)
      $('debugbox').innerHTML = req.responseText;
    }
    req.caching = true;
    req.open('POST', 'request.php?mode=object&item=categoriestree&authcode='+AUTHCODE+'&action=getbranch',true);
    req.send({ id: id, idcat: cur_idcat, tab: cat_tab, section: SECTION, item: ITEM });
  }
  else
  {	obj.style.display="none";
	img.src="/templates/admin/images/collapse.gif";
  }
}
function tc_expandbranch(id,finid)
{ if(id==finid)
  { tc_sortable();
    return;
  }
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { id = req.responseJS.id;
	  if(!(img=$('dbetbj'+id+'_bullet'))) { tc_sortable();return false; }
      if(!(obj=$('dbetbj'+id))) { tc_sortable();return false; }
      obj.innerHTML = req.responseJS.html;
	  obj.style.display="";
	  img.src="/templates/admin/images/expand.gif";
	  tc_expandbranch(id,finid);
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=object&item=categoriestree&authcode='+AUTHCODE+'&action=expandbranch',true);
  req.send({ idker: id , finid : finid, tab: cat_tab, section: SECTION, item: ITEM });
}
function movecat(form)
{ if(form.idto.value<0)
  { alert("Пожалуйста, выберите куда перемещать."); return false; }
  return true;
}