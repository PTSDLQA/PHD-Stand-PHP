function pages_getdirs(id)
{ var req = new JsHttpRequest();
  req.onreadystatechange = function() 
  { if(req.readyState==4 && req.responseJS) 
    { form=document.forms.addblockform;
	  if(!form)
	  form=document.forms.editblockform;	  		  	  
	  form.b_idcat.length=req.responseJS.ids.length+1;	  	  	  
	  form.b_idcat[0].text='Корневой';
	  form.b_idcat[0].value=0;	  
	  for(i=1;i<=req.responseJS.ids.length;i++)
	  { form.b_idcat[i].text=req.responseJS.names[i-1];	    
	    form.b_idcat[i].value=req.responseJS.ids[i-1];
	  }		
	}
	if(req.responseText)
    $('debugbox').innerHTML = req.responseText;    
  }
  req.caching = false;
  req.open('POST', 'request.php?mode=block&item=pages&authcode='+AUTHCODE+'&action=getdirs',true);
  req.send({ idsec: id });  
}
function pages_curcheck(checked)
{  
  $('catselectbox').style.display=checked?'none':'';
}