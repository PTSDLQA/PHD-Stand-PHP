function categories_getcategories(id)
{ var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { form=document.forms.addblockform;
	  if(!form)
	  form=document.forms.editblockform;
	  form.b_idcat.length=req.responseJS.ids.length+1;
	  form.b_idcat[0].text='Все';
	  form.b_idcat[0].value=0;
	  for(i=1;i<=req.responseJS.names.length;i++)
	  { form.b_idcat[i].text=req.responseJS.names[i-1];
	    form.b_idcat[i].value=req.responseJS.ids[i-1];
	  }
	  form.b_idcat.value=0;
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=block&item=categories&authcode='+AUTHCODE+'&action=getcategories',true);
  req.send({ idsec: id });
}
function categories_getownercategories(id)
{ var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { form=document.forms.addblockform;
	  if(!form)
	  form=document.forms.editblockform;
	  form.b_idcat.length=req.responseJS.ids.length+1;
	  form.b_idcat[0].text='Не выбрано';
	  form.b_idcat[0].value=0;
	  for(i=1;i<=req.responseJS.names.length;i++)
	  { form.b_idcat[i].text=req.responseJS.names[i-1];
	    form.b_idcat[i].value=req.responseJS.ids[i-1];
	  }
	  form.b_idcat.value=0;
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=block&item=categories&authcode='+AUTHCODE+'&action=getownercategories',true);
  req.send({ idsec: id });
}
function categories_curcheck(checked)
{
  $('catselectbox').style.display=checked?'none':'';
  modal_refresh();
}