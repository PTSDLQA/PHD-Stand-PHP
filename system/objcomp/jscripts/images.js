var images_iditem=0;
var images_rows=3;
var images_curpage=0;
function images_refresh()
{ var req = new JsHttpRequest();
  message_loading('images_gridbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('images_gridbox').innerHTML = req.responseJS.html;
	  modal_refresh();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=images&authcode='+AUTHCODE+'&action=refresh',true);
  req.send({ iditem: images_iditem, rows: images_rows, section: SECTION, item: ITEM });
}
function images_gopage(page)
{ var req = new JsHttpRequest();
  message_loading('images_gridbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('images_gridbox').innerHTML = req.responseJS.html;
      images_curpage=page;
	  modal_refresh();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=images&authcode='+AUTHCODE+'&action=refresh&page='+page,true);
  req.send({ iditem: images_iditem, rows: images_rows, section: SECTION, item: ITEM });
}
function images_getupload()
{ images_cancel();
  $('images_mainbox').style.display='none';
  $('images_filebox').innerHTML='<input type="file" id="images_file" name="images_file">';
  $('images_uploadbox').style.display='';
  modal_refresh();
}
function images_cancel()
{ $('images_uploadbox').style.display='none';
  $('images_editbox').style.display='none';
  $('images_messagebox').style.display='none';
  $('images_mainbox').style.display='';
  $('images_caption1').value='';
  $('images_caption2').value='';
  $('images_filebox').innerHTML='';
  modal_refresh();
}
function images_upload()
{ $('images_uploadbox').style.display='none';
  $('images_messagebox').style.display='';
  var req = new JsHttpRequest();
  message_loading('images_messagebox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('images_gridbox').innerHTML = req.responseJS.html;
      $("images_caption1").form.target='_self';
      $("images_caption1").form.action=document.location;
	  images_cancel();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=images&authcode='+AUTHCODE+'&action=upload&page='+images_curpage,true);
  req.send({ iditem: images_iditem, rows: images_rows, image: $("images_file"), caption: $("images_caption1").value, section: SECTION, item: ITEM });
}
function images_edit(id)
{ images_cancel();
  $('images_mainbox').style.display='none';
  $('images_messagebox').style.display='';
  var req = new JsHttpRequest();
  message_loading('images_messagebox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('images_filebox2').innerHTML='<input type="file" id="images_rfile" name="images_rfile">';
	  $('images_caption2').value=req.responseJS.caption;
	  $('images_id').value=id;
	  $('images_messagebox').style.display='none';
	  $('images_editbox').style.display='';
	  modal_refresh();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=images&authcode='+AUTHCODE+'&action=getcaption',true);
  req.send({ iditem: images_iditem, id: id, section: SECTION, item: ITEM });
}
function images_save()
{ $('images_editbox').style.display='none';
  $('images_messagebox').style.display='';
  var req = new JsHttpRequest();
  message_loading('images_messagebox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('images_gridbox').innerHTML = req.responseJS.html;
	  images_cancel();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=images&authcode='+AUTHCODE+'&action=save&page='+images_curpage,true);
  req.send({ iditem: images_iditem, rows: images_rows, id: $("images_id").value, image: $("images_rfile"), caption: $("images_caption2").value, section: SECTION, item: ITEM });

}
function images_sort(id,action)
{ var req = new JsHttpRequest();
  message_loading('images_gridbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('images_gridbox').innerHTML = req.responseJS.html;
	  images_cancel();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=images&authcode='+AUTHCODE+'&action='+action+'&page='+images_curpage,true);
  req.send({ iditem: images_iditem, rows: images_rows, id: id, section: SECTION, item: ITEM });
}
function images_del(id)
{ if(!confirm('Действительно удалить изображение?')) return;
  var req = new JsHttpRequest();
  message_loading('images_gridbox');
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { $('images_gridbox').innerHTML = req.responseJS.html;
	  images_cancel();
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=object&item=images&authcode='+AUTHCODE+'&action=del&page='+images_curpage,true);
  req.send({ iditem: images_iditem, rows: images_rows, id: id, section: SECTION, item: ITEM });
}