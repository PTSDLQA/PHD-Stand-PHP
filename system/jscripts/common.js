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
Array.prototype.inArray = function (value)
{ var i;
  for(i=0;i<this.length;i++)
  if(this[i]===value) return true;
  return false;
};
function getParam(paramName,dvalue)
{ var url=document.URL.replace(paramName,'');
  if ((left=url.indexOf('?='))<0) if ((left=url.indexOf('&='))<0) return dvalue;
  return (right=url.indexOf('&',left+1))<0?url.substr(left+2):url.substr(left+2,right-left-2);
}
function getRadioValue(obj)
{ for(i=0;i<obj.length;i++)
  if(obj[i].checked) return obj[i].value;
  return null;
}
function message_loading(id)
{ if(obj=$(id))
  obj.innerHTML = '<div class="subloading">Загружается...</div>';
}
function message_saving(id)
{ if(obj=$(id))
  obj.innerHTML = '<div class="subloading">Сохраняется...</div>';
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
{ var obj=$(idcontent);
  if(obj)
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
function before_modal_window()
{ if(flashobj=document.getElementsByTagName('embed'))
  for(i=0;i<flashobj.length;i++)
  flashobj[i].style.display='none';
}
function after_modal_window()
{ if(flashobj=document.getElementsByTagName('embed'))
  for(i=0;i<flashobj.length;i++)
  flashobj[i].style.display='';
}
var modalwin=null;
var loadwin=null;
var loadingtimer;
function modal_window_html(title,html,width,afterfun)
{ if(typeof loadingtimer != 'undefined')
  clearTimeout(loadingtimer);
  if(navigator.userAgent.indexOf("IE")>=0)
  { modal_window_html_ie(title,html,width,afterfun?afterfun:null);
    if(loadwin)
    loadwin.options.hideEffect=Element.hide;
    setTimeout('Dialog.closeInfo();loadwin=null;',2000);
    return;
  }
  before_modal_window();
  modalwin = new Window({
  className:'alphacube',
  title:title,
  width:width,
  height:0,
  showEffect:Element.show,
  hideEffect:Element.hide,
  minimizable:false,
  maximizable:false,
  resizable: false,
  destroyOnClose:true,
  recenterAuto:true});
  showObserver = { onShow: function(eventName, win)
  { if(win==modalwin)
    { if(typeof FWC != 'undefined')
	  FWC.evalSmartSelectTags();
	  tags_ini();
      modal_refresh();
      if(afterfun) eval(afterfun); }
	}
  }
  closeObserver = { onClose: function(eventName, win)
  { if(win==modalwin)
    { Windows.removeObserver(showObserver);
	  Windows.removeObserver(this);
	  after_modal_window();
	  modalwin=null; }
	}
  }
  Windows.addObserver(showObserver);
  Windows.addObserver(closeObserver);
  modalwin.setHTMLContent(html);
  modalwin.showCenter(true);
  if(loadwin)
  loadwin.options.hideEffect=Element.hide;
  setTimeout('Dialog.closeInfo();loadwin=null;',2000);
}
function modal_window_html_ie(title,html,width,afterfun)
{ before_modal_window();
  modalwin = new Window({
  className:'alphacube',
  title:title,
  width:width,
  height:0,
  showEffect:Element.show,
  hideEffect:Element.hide,
  minimizable:false,
  maximizable:false,
  resizable: false,
  destroyOnClose:true,
  recenterAuto:true});
  showObserver = { onShow: function(eventName, win)
  { if(win==modalwin)
    { modalwin.setHTMLContent(html);
      if(typeof FWC != 'undefined')
	  FWC.evalSmartSelectTags();
	  tags_ini();
	  modal_refresh();
	  if(afterfun) eval(afterfun); }
	}
  }
  closeObserver = { onClose: function(eventName, win)
  { if(win==modalwin)
    { Windows.removeObserver(showObserver);
	  Windows.removeObserver(this);
	  after_modal_window();
	  modalwin=null; }
	}
  }
  Windows.addObserver(showObserver);
  Windows.addObserver(closeObserver);
  modalwin.showCenter(true);
}
function modal_refresh()
{ if(!modalwin || typeof modalwin=='undefined') return;
  size={width: modalwin.width, height: modalwin.getContent().childElements()[0].getHeight()};
  modalwin.setSize(size.width,size.height);
  if(size.height>document.body.clientHeight-30 && modalwin.options.recenterAuto)
  { modalwin.options.recenterAuto=false;
    WindowUtilities.disableScreen(modalwin.options.className,'overlay_modal',modalwin.overlayOpacity,modalwin.getId(),modalwin.options.parent);
    modalwin.toFront();
  }
  else
  modalwin._center();
}
function modal_window_url(title,url,width,height)
{ urlwin = new Window({
  className:'alphacube',
  title:title,
  width:width,
  height:height,
  url: url,
  showEffect:Element.show,
  hideEffect:Element.hide,
  minimizable:false,
  maximizable:false,
  resizable: false,
  destroyOnClose:true,
  recenterAuto:true});
  urlwin.showCenter(true);
}
var treeWin=null;
var treeId='';
var treeOldAuto;
function modal_treeview(id,title,struct,attributes)
{ treeId=id;
  pos=$(id+'_img').cumulativeOffset();
  if(modalwin)
  { treeOldAuto=modalwin.options.recenterAuto;
    modalwin.options.recenterAuto=false;
  }
  treeWin = new Window({
  className:'alphacube',
  title:title,
  maximizable: false,
  minimizable:false,
  resizable: false,
  showEffect:Element.show,
  hideEffect:Element.hide,
  top: pos.top-20,
  left: pos.left+20,
  width: 380,
  height: 150,
  destroyOnClose: true});
  treeWin.show(true);
  closeObserver = {
  onClose: function(eventName, win)
  { if (win == treeWin)
	{ treeWin = null;
	  if(modalwin)
	  { modalwin.options.recenterAuto=treeOldAuto;
	    modalwin.toFront();
	  }
      Windows.removeObserver(this);
    }
  }}
  Windows.addObserver(closeObserver);
  treeWin.setHTMLContent('<div id="'+id+'" class="tafelTree"></div>');
  tree = new TafelTree(id,struct,attributes);
  branch=tree.getBranchById(id+$(id+'_value').value);
  if(branch) branch.select();
}
function modal_treeview_select(branch)
{ id=branch.getId();
  $(treeId+'_txt').value=branch.getText();
  $(treeId+'_value').value=id.substring(treeId.length,id.length);
  $(treeId+'_txt').onchange();
  treeWin.close();
}
function treeview_open(branch,response)
{ return response;
}
function runLoading()
{ loadingtimer=setTimeout('showLoading()',2000);
}
function showLoading()
{ loadwin=Dialog.info("Загружается ...",{width:200,height:50,showProgress:true,destroyOnClose:true});
}
function endLoading()
{ if(typeof loadingtimer != 'undefined')
  clearTimeout(loadingtimer);
  loadingtimer=setTimeout('Dialog.closeInfo();loadwin=null;',500);
}
function goactionurl(url)
{ document.location=url;
}
function genPass()
{ vo="aeiouAEU";
  co="bcdfgjklmnprstvwxzBCDFGHJKMNPQRSTVWXYZ0123456789_$%#";
  s=Math.round(Math.random());
  l=8;
  p='';
  for(i=0;i<l;i++)
  { if(s)
    { letter=vo.charAt(Math.round(Math.random()*(vo.length-1)));
	  s=0;
	}
	else
	{ letter=co.charAt(Math.round(Math.random()*(co.length-1)));
	  s=1;
	}
	p=p+letter;
  }
  return p;
}
var genpassWin=null;
var genpassId='';
var genpassOldAuto;
function opengenpass(id)
{ genpassId=id;
  pos=$(id+'_img').cumulativeOffset();
  if(modalwin)
  { genpassOldAuto=modalwin.options.recenterAuto;
    modalwin.options.recenterAuto=false;
  }
  genpassWin = new Window({
  className:'alphacube',
  title:'Генерация пароля',
  maximizable: false,
  minimizable:false,
  resizable: false,
  showEffect:Element.show,
  hideEffect:Element.hide,
  top: pos.top-20,
  left: pos.left+20,
  width: 200,
  height: 70,
  destroyOnClose: true});
  genpassWin.show(true);
  closeObserver = {
  onClose: function(eventName, win)
  { if (win == genpassWin)
	{ genpassWin = null;
	  genpassbox = null;
	  if(modalwin)
	  { modalwin.options.recenterAuto=genpassOldAuto;
	    modalwin.toFront();
	  }
      Windows.removeObserver(this);
    }
  }}
  Windows.addObserver(closeObserver);
  html='<h1 id="genpassid" align="center">пароль</h1>\
        <p align="center">\
        <input type="button" class="button" value="Другой" onclick="genpass()" style="width:80px;">\
        <input type="button" class="button" value="Применить" onclick="applypass()" style="width:80px;">\
        </p>';
  genpassWin.setHTMLContent(html);
  genpass();
}
function genpass()
{ $('genpassid').innerHTML = genPass();
}
function applypass()
{ $(genpassId+'_text').value=$('genpassid').innerHTML;
  Windows.close(genpassWin.getId());
}
var codemirror_editor=null;
function codemirror_ini(height,ext)
{ if(ext=='tpl')
  { parserfile=['parsexml.js','parsecss.js','tokenizejavascript.js','parsejavascript.js','parsehtmlmixed.js'];
    stylesheet=['/templates/admin/mirrorcss/xmlcolors.css','/templates/admin/mirrorcss/jscolors.css','/templates/admin/mirrorcss/csscolors.css'];
  }
  else if(ext=='css')
  { parserfile='parsecss.js';
    stylesheet='/templates/admin/mirrorcss/csscolors.css';
  }
  else if(ext=='js')
  { parserfile='parsejavascript.js';
    stylesheet='/templates/admin/mirrorcss/jscolors.css';
  }
  else
  { parserfile=['parsexml.js','parsecss.js','tokenizejavascript.js','parsejavascript.js','parsehtmlmixed.js','tokenizephp.js','parsephp.js','parsephphtmlmixed.js'];
    stylesheet=['/templates/admin/mirrorcss/xmlcolors.css','/templates/admin/mirrorcss/jscolors.css','/templates/admin/mirrorcss/csscolors.css','/templates/admin/mirrorcss/phpcolors.css'];
  }
  codemirror_editor = CodeMirror.fromTextArea('codearea', {
  height: height+'px',
  parserfile: parserfile,
  stylesheet: stylesheet,
  path: '/system/jscodemirror/',
  textWrapping: false });
}
function tags_ini()
{ if(obj=$('tags_editor'))
  { new Ajax.Autocompleter('tags_editor', 'tags_choices', 'request.php?mode=object&item=tags&authcode='+AUTHCODE+'&action=gettags', {paramName: 'query', minChars: 1, indicator: 'indicator', tokens: ','});
    $('tags_editor').observe('focus', function(){ if($('tags_editor').form) $('tags_editor').form.observe('submit', Event.stop); });
    $('tags_editor').observe('blur',  function(){ if($('tags_editor').form) $('tags_editor').form.stopObserving('submit', Event.stop); });
  }
}
function domain_form(form)
{ if(!/^[a-zA-Z0-9]+$/i.test(form.name.value))
  { alert("Пожалуйста, корректно заполните идентификатор (только латинские буквы и цифры)."); return false; }
  if(form.domain.value.replace(/\s+/, '').length==0)
  { alert("Пожалуйста, укажите домен."); return false; }
  showLoading();
  return true;
}
function setsectionssort(obj)
{ obj.onclick= function(e){e.stopPropagation?e.stopPropagation():(e.cancelBubble=true); e.preventDefault?e.preventDefault():(e.returnValue=false);obj.onclick='';};
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=site&item=sections&authcode='+AUTHCODE+'&action=setsort', true);
  req.send({ sort: Sortable.sequence(obj).join(',') });
}
function setstructuressort(obj)
{ obj.onclick= function(e){e.stopPropagation?e.stopPropagation():(e.cancelBubble=true); e.preventDefault?e.preventDefault():(e.returnValue=false);obj.onclick='';};
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=site&item=structures&authcode='+AUTHCODE+'&action=setsort', true);
  req.send({ sort: Sortable.sequence(obj).join(',') });
}
function setblockssort(obj)
{ obj.onclick= function(e){e.stopPropagation?e.stopPropagation():(e.cancelBubble=true); e.preventDefault?e.preventDefault():(e.returnValue=false);obj.onclick='';};
  var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=site&item=blocks&authcode='+AUTHCODE+'&action=setsort', true);
  req.send({ sort: Sortable.sequence(obj).join(',') });
}
function inipbox(box)
{ if(obj=$(box))
  { obj.style.display=getCookie(box)==1?'':'none';
    if(obj.style.display=='')
    modal_refresh();
  }
}
function togglepbox(box)
{ if(obj=$(box))
  { obj.style.display=obj.style.display=='none'?'':'none';
	setCookie(box,obj.style.display==''?1:0,1000);
	modal_refresh();
  }
}