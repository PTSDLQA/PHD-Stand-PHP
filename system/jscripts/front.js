function $(id)
{ return document.getElementById(id);
}
function getElementsByClass(searchClass,node,tag)
{ var classElements = new Array();
  if(node == null) node=document;
  if(tag==null)	tag = '*';
  var els=node.getElementsByTagName(tag);
  var elsLen=els.length;
  var pattern=new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
  var j=0;
  for(i=0;i<elsLen;i++)
  { if(pattern.test(els[i].className))
    { classElements[j] = els[i];
	  j++;
	}
  }
  return classElements;
}
function addEvent(elm,evType,fn,useCapture)
{ if(elm.addEventListener)
  { elm.addEventListener(evType,fn,useCapture);
    return true;
  }
  else if(elm.attachEvent)
  { var r=elm.attachEvent('on'+evType,fn);
    return r;
  }
  else
  elm['on'+evType]=fn;
}
function getCookie(name)
{ var start=document.cookie.indexOf(name+'=');
  var len=start+name.length+1;
  if((!start)&&(name!=document.cookie.substring(0,name.length)))
  return null;
  if(start==-1) return null;
  var end=document.cookie.indexOf(';',len);
  if(end==-1) end=document.cookie.length;
  return unescape(document.cookie.substring(len,end));
}
function setCookie(name,value,expires,path,domain,secure)
{ var today = new Date();
  today.setTime(today.getTime());
  if(expires)
  expires=expires*1000*60*60*24;
  var expires_date=new Date(today.getTime()+(expires));
  document.cookie=name+'='+escape(value)+
  ((expires)?';expires='+expires_date.toGMTString():'')+
  ((path)?';path='+path:';path=/')+
  ((domain)?';domain='+domain:'')+
  ((secure)?';secure':'');
}
function deleteCookie(name,path,domain)
{ if(getCookie(name)) document.cookie=name+'='+((path)?';path='+path:'')+((domain)?';domain='+domain:'')+';expires=Thu, 01-Jan-1970 00:00:01 GMT';
}
function getRadioValue(obj)
{ for(i=0;i<obj.length;i++)
  if(obj[i].checked) return obj[i].value;
  return null;
}
function getElementPosition(elem)
{ var w=elem.offsetWidth;
  var h=elem.offsetHeight;
  var l=0;
  var t=0;
  while(elem)
  { l+=elem.offsetLeft;
    t+=elem.offsetTop;
    elem=elem.offsetParent;
  }
  return {"left":l, "top":t, "width": w, "height":h};
}
function open_window(url,w,h,id,center,scroll)
{ scroll=scroll?'yes':'no';
  if(center)
  var win = 'width='+w+',height='+h+',Left='+Math.ceil(document.body.clientWidth/2-w/2)+',Top='+Math.ceil(document.body.clientHeight/2-h/2)+',menubar=no,location=no,resizable=yes,scrollbars='+scroll;
  else
  var win = 'width='+w+',height='+h+',menubar=no,location=no,resizable=yes,scrollbars='+scroll;
  var newWin = window.open(url,id,win);
  newWin.focus();
}
function open_imgwindow(src,title,w,h,css)
{ css=css||'/templates/admin/imagewin.css';
  var newWin = window.open('','popupimage','width='+(w+20)+',height='+(h+25)+',Left='+Math.ceil(document.body.clientWidth/2-w/2)+',Top='+Math.ceil(document.body.clientHeight/2-h/2)+',menubar=no,location=no,resizable=yes,scrollbars=no');
  newWin.document.open();
  newWin.document.write('\
  <html>\
  <head>\
  <title>'+title+'</title>\
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">\
  <link rel="stylesheet" href="'+css+'" type="text/css">\
  </head><body>\
  <table width="100%" border="0" cellspacing="0" cellpadding="0">\
  <tr><td align="center"><img src="'+src+'" border="0"></td></tr>\
  <tr><td align="center"><a href="javascript:window.close()">Закрыть</a></td></tr>\
  </table>\
  </body>\
  </html>');
  newWin.document.close();
  newWin.focus();
}
function open_cwindow(idcontent,w,h,id,center,scroll)
{ if(obj=$(idcontent))
  { scroll=scroll?'yes':'no';
    if(center)
    var win = 'width='+w+',height='+h+',Left='+Math.ceil(document.body.clientWidth/2-w/2)+',Top='+Math.ceil(document.body.clientHeight/2-h/2)+',menubar=no,location=no,resizable=yes,scrollbars='+scroll;
	else
	var win = 'width='+w+',height='+h+',menubar=no,location=no,resizable=yes,scrollbars='+scroll;
    var newWin = window.open('',id,win);
    newWin.document.open();
    newWin.document.write(obj.value);
    newWin.document.close();
    newWin.focus();
  }
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
function loadselectlist(form,selectname,list,field,value)
{ var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { if(form[selectname][0] && form[selectname][0].value==0)
	  { form[selectname].length=req.responseJS.list.length+1;
	    for(i=1;i<=req.responseJS.list.length;i++)
	    { form[selectname][i].text=req.responseJS.list[i-1].name;
	      form[selectname][i].value=req.responseJS.list[i-1].id;
	      form[selectname].value=0;
	    }
	  }
	  else
	  { form[selectname].length=req.responseJS.list.length;
	    for(i=0;i<req.responseJS.list.length;i++)
	    { form[selectname][i].text=req.responseJS.list[i].name;
	      form[selectname][i].value=req.responseJS.list[i].id;
          if(req.responseJS.list[0].id)
          form[selectname].value=req.responseJS.list[0].id;
	    }
	  }
	  if(form[selectname].onchange)
	  form[selectname].onchange();
	}
	if(req.responseText && $('debugbox'))
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', '/request.php?mode=front&item='+list+'&action=loadlist',true);
  req.send({ field: field, value: value });
}
function autoheightbar(id)
{ var bar=$(id);
  var footer=$('footer');
  if(bar && footer)
  { pbar=getElementPosition(bar);
    pfooter=getElementPosition(footer);
    bar.style.height=(pfooter.top-pbar.top)+'px';
  }
}
function autoheightbars()
{ autoheightbar('sl');
  autoheightbar('content');
  autoheightbar('sr');
}
function runtheme()
{ autoheightbars();
  addEvent(window,'resize',autoheightbars,false);
}