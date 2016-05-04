function gallery_getalbums(idsec,idcat)
{ var req = new JsHttpRequest();
  req.onreadystatechange = function()
  { if(req.readyState==4 && req.responseJS)
    { form=document.forms.addblockform;
	  if(!form)
	  form=document.forms.editblockform;
	  form.b_idalb.length=req.responseJS.ids.length+1;
	  form.b_idalb[0].text='Все';
	  form.b_idalb[0].value=0;
	  for(i=1;i<=req.responseJS.names.length;i++)
	  { form.b_idalb[i].text=req.responseJS.names[i-1];
	    form.b_idalb[i].value=req.responseJS.ids[i-1];
	  }
	  form.b_idalb.value=0;
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;
  }
  req.caching = true;
  req.open('POST', 'request.php?mode=block&item=gallery&authcode='+AUTHCODE+'&action=getalbums',true);
  req.send({ idsec: idsec, idcat: idcat });
}