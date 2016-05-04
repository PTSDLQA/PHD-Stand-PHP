var files_iditem=0;
var files_rows=5;
var files_curpage=0;
function files_refresh()
{ var req = new JsHttpRequest();
  message_loading('files_gridbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('files_gridbox').innerHTML = req.responseJS.html;
	  modal_refresh();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=files&authcode='+AUTHCODE+'&action=refresh',true);
  req.send({ iditem: files_iditem, rows: files_rows, section: SECTION, item: ITEM });
}
function files_gopage(page)
{ var req = new JsHttpRequest();
  message_loading('files_gridbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('files_gridbox').innerHTML = req.responseJS.html;
      files_curpage=page;
	  modal_refresh();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=files&authcode='+AUTHCODE+'&action=refresh&page='+page,true);
  req.send({ iditem: files_iditem, rows: files_rows, section: SECTION, item: ITEM });
}
function files_getupload()
{ files_cancel();
  $('files_mainbox').style.display='none';
  $('files_filebox').innerHTML='<input type="file" id="files_file" name="files_file">';
  $('files_uploadbox').style.display='';
  modal_refresh();
}
function files_getregister()
{ files_cancel();
  $('files_mainbox').style.display='none';
  $('files_registerbox').style.display='';
  modal_refresh();
}
function files_cancel()
{ $('files_uploadbox').style.display='none';
  $('files_registerbox').style.display='none';
  $('files_editbox').style.display='none';
  $('files_messagebox').style.display='none';
  $('files_mainbox').style.display='';
  $('files_caption1').value='';
  $('files_caption2').value='';
  $('files_caption3').value='';
  $('files_filebox').innerHTML='';
  modal_refresh();
}
function files_upload()
{ $('files_uploadbox').style.display='none';
  $('files_messagebox').style.display='';
  var req = new JsHttpRequest();
  message_loading('files_messagebox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('files_gridbox').innerHTML = req.responseJS.html;
      $("files_caption1").form.target='_self';
      $("files_caption1").form.action=document.location;
	  files_cancel();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=files&authcode='+AUTHCODE+'&action=upload&page='+files_curpage,true);
  req.send({ iditem: files_iditem, rows: files_rows, file: $("files_file"), caption: $("files_caption1").value, section: SECTION, item: ITEM });
}
function files_register()
{ $('files_registerbox').style.display='none';
  $('files_messagebox').style.display='';
  var req = new JsHttpRequest();
  message_loading('files_messagebox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('files_gridbox').innerHTML = req.responseJS.html;
	  files_cancel();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=files&authcode='+AUTHCODE+'&action=register&page='+files_curpage,true);
  req.send({ iditem: files_iditem, rows: files_rows, path: $("files_path").value, caption: $("files_caption2").value, section: SECTION, item: ITEM });
}
function files_edit(id)
{ files_cancel();
  $('files_mainbox').style.display='none';
  $('files_messagebox').style.display='';
  var req = new JsHttpRequest();
  message_loading('files_messagebox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('files_filebox2').innerHTML='<input type="file" id="files_rfile" name="files_rfile">';
	  $('files_caption3').value=req.responseJS.caption;
	  $('files_id').value=id;
	  $('files_messagebox').style.display='none';
	  $('files_editbox').style.display='';
	  modal_refresh();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=files&authcode='+AUTHCODE+'&action=getcaption',true);
  req.send({ iditem: files_iditem, id: id, section: SECTION, item: ITEM });
}
function files_save()
{ $('files_editbox').style.display='none';
  $('files_messagebox').style.display='';
  var req = new JsHttpRequest();
  message_loading('files_messagebox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('files_gridbox').innerHTML = req.responseJS.html;
	  files_cancel();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=files&authcode='+AUTHCODE+'&action=save&page='+files_curpage,true);
  req.send({ iditem: files_iditem, rows: files_rows, id: $('files_id').value, file: $("files_rfile"), caption: $("files_caption3").value, section: SECTION, item: ITEM });
}
function files_sort(id,action)
{ var req = new JsHttpRequest();
  message_loading('files_gridbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('files_gridbox').innerHTML = req.responseJS.html;
	  files_cancel();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=files&authcode='+AUTHCODE+'&action='+action+'&page='+files_curpage,true);
  req.send({ iditem: files_iditem, rows: files_rows, id: id, section: SECTION, item: ITEM });
}
function files_del(id)
{ if(!confirm('Действительно удалить файл?')) return;
  var req = new JsHttpRequest();
  message_loading('files_gridbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('files_gridbox').innerHTML = req.responseJS.html;
	  files_cancel();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=files&authcode='+AUTHCODE+'&action=del&page='+files_curpage,true);
  req.send({ iditem: files_iditem, rows: files_rows, id: id, section: SECTION, item: ITEM });
}