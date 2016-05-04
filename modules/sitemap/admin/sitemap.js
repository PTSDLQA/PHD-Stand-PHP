function checkall(check)
{ for(i=0;i<csections;i++)  
  { obj=$('check'+i);
    if(obj)
	obj.checked=check;
  }	
}