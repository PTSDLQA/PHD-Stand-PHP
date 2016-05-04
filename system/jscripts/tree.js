function togglePlus(id)
{ if(!(img=$(id+'_bullet'))) return false;
  if(!(obj=$(id))) return false;
  if(!obj.style) return false;
  if(obj.style.display=="none")
  { obj.style.display="";
	img.src="/templates/admin/images/expand.gif";
  }
  else
  {	obj.style.display="none";
	img.src="/templates/admin/images/collapse.gif";
  }
}
function expandobj(object)
{ if(!(img=$(object+'_bullet'))) return false;
  if(!(obj=$(object))) return false;
  if(!obj.style) return false;
  if(obj.style.display=="none")
  { obj.style.display="";
	img.src="/templates/admin/images/expand.gif";
  }
}
function collapseobj(object)
{ if(!(img=$(object+'_bullet'))) return false;
  if(!(obj=$(object))) return false;
  if(!obj.style) return false;
  if(obj.style.display!="none")
  {	obj.style.display="none";
	img.src="/templates/admin/images/collapse.gif";
  }
}
function togglePlusJ(id)
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
	  obj.innerHTML = req.responseJS.html;
	  if(req.responseText)
      $('debugbox').innerHTML = req.responseText;
    }
    req.caching = true;
	if(SECTION)
    req.open('POST', 'jsrequest.php?mode=sections&item='+SECTION+'&authcode='+AUTHCODE+'&action=getbranch',true);
	else if(PLUGIN)
	req.open('POST', 'jsrequest.php?mode=plugins&item='+PLUGIN+'&authcode='+AUTHCODE+'&action=getbranch',true);
    req.send({ id: id });
  }
  else
  {	obj.style.display="none";
	img.src="/templates/admin/images/collapse.gif";
  }
}
function expandbranch(id,finid)
{ if(id==finid) return;
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { id = req.responseJS.id;
	  if(!(img=$('dbetbj'+id+'_bullet'))) return false;
      if(!(obj=$('dbetbj'+id))) return false;
      obj.innerHTML = req.responseJS.html;
	  obj.style.display="";
	  img.src="/templates/admin/images/expand.gif";
	  expandbranch(id,finid);
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  if(SECTION)
  req.open('POST', 'jsrequest.php?mode=sections&item='+SECTION+'&authcode='+AUTHCODE+'&action=expandbranch',true);
  else if(PLUGIN)
  req.open('POST', 'jsrequest.php?mode=plugins&item='+PLUGIN+'&authcode='+AUTHCODE+'&action=expandbranch',true);
  req.send({ idker: id , finid : finid});
}
function upitem(treeid,id)
{ goactionurl(document.location+'&treeaction=up&treeid='+treeid+'&id='+id);
}
function downitem(treeid,id)
{ goactionurl(document.location+'&treeaction=down&treeid='+treeid+'&id='+id);
}

