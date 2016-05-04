{section name=i loop=$tags}
<a href="{$tags[i].link}" style="font-size:{$tags[i].cluster*10+100}%">{$tags[i].tag}({$tags[i].count})</a> 
{/section}