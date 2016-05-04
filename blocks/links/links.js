function links_addlink()
{ form=document.forms.addblockform;
  if(!form)
  form=document.forms.editblockform;
  var values = new Array();
  var i;
  for(i=0;i<form.elements.b_count.value;i++)
  if(obj=$('link'+i))
  values[i] = new Array($('b_sub'+i)?$('b_sub'+i).checked:false,form.elements['b_slink'+i].value,form.elements['b_caption'+i].value,form.elements['b_link'+i].value,form.elements['b_section'+i].value,form.elements['b_id'+i].value);
  html='<div id="link'+i+'">';
  html+='<table width="100%"><tr>';
  if(!$('expert') || $('expert').value>0)
  html+='<td width="25"><input type="checkbox" id="b_sub'+i+'" name="b_sub[]" value="'+i+'"></td>';
  html+='<td width="20%"><select name="b_slink'+i+'" onchange="links_select('+i+',this.value)" style="width:100%"></select></td>';
  html+='<td width="30%"><input type="text" name="b_caption'+i+'" maxlength="50" value="" style="width:100%"></td>';
  html+='<td><input type="text" name="b_link'+i+'" maxlength="100" value="" style="width:100%">';
  html+='<td width="60"><input type="text" name="b_id'+i+'" maxlength="20" value="" style="width:100%">';
  html+='<input type="hidden" name="b_section'+i+'" value=""></td>';
  html+='<td width="20"><a href="javascript:links_insert('+i+')" title="Вставить"><img src="/templates/admin/images/add.gif"></a></td>';
  html+='<td width="20"><a href="javascript:links_del('+i+')" title="Удалить"><img src="/templates/admin/images/del.gif"></a></td>';
  html+='</tr></table></div>';
  $('b_links').innerHTML+=html;
  form.elements['b_slink'+i].length=alllinks.length+1;
  form.elements['b_slink'+i][0].text='---';
  form.elements['b_slink'+i][0].value=0;
  for(j=0;j<alllinks.length;j++)
  { form.elements['b_slink'+i][j+1].text=alllinks[j][1];
    form.elements['b_slink'+i][j+1].value=alllinks[j][0];
  }
  for(i=0;i<form.elements.b_count.value;i++)
  if(obj=$('link'+i))
  { if(!$('expert') || $('expert').value>0)
    $('b_sub'+i).checked=values[i][0];
    form.elements['b_slink'+i].value=values[i][1];
	form.elements['b_caption'+i].value=values[i][2];
	form.elements['b_link'+i].value=values[i][3];
	form.elements['b_section'+i].value=values[i][4];
	form.elements['b_id'+i].value=values[i][5];
  }
  form.elements.b_count.value=i+1;
  $('lheader').style.display='';
  modal_refresh();
}
function links_select(id,link)
{ form=document.forms.addblockform;
  if(!form)
  form=document.forms.editblockform;
  for(i=0;i<alllinks.length;i++)
  if(alllinks[i][0]==link)
  { form.elements['b_caption'+id].value=alllinks[i][1].substr(2);
	form.elements['b_link'+id].value=alllinks[i][0];
	form.elements['b_section'+id].value=alllinks[i][2];
  }
}
function links_del(id)
{ if(obj=$('link'+id))
  { obj.remove();
    modal_refresh();
  }
}
function links_insert(id)
{ form=document.forms.addblockform;
  if(!form)
  form=document.forms.editblockform;
  links_addlink();
  count=form.elements.b_count.value;
  j=-1;
  for(i=count;i>=id;i--)
  { if(obj=$('link'+i))
	{ if(j>=0)
	  { if(!$('expert') || $('expert').value>0)
	    $('b_sub'+j).checked=$('b_sub'+i).checked;
	    form.elements['b_slink'+j].value=form.elements['b_slink'+i].value;
	    form.elements['b_caption'+j].value=form.elements['b_caption'+i].value;
	    form.elements['b_link'+j].value=form.elements['b_link'+i].value;
		form.elements['b_section'+j].value=form.elements['b_section'+i].value;
		form.elements['b_id'+j].value=form.elements['b_id'+i].value;
	  }
	  j=i;
	}
  }
  if(obj=$('link'+j))
  { if(!$('expert') || $('expert').value>0)
    $('b_sub'+j).checked=false;
    form.elements['b_slink'+j].value=0;
    form.elements['b_caption'+j].value='';
    form.elements['b_link'+j].value='';
    form.elements['b_section'+j].value='';
    form.elements['b_id'+j].value=''
  }
  modal_refresh();
}
