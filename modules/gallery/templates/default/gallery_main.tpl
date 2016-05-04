{include file="_header.tpl"}

<h1>{$section_name}</h1>

{if $categories}
<ul>
{section name=i loop=$categories}
<li><a href="{$categories[i].link}">{$categories[i].name} ({$categories[i].citems})</a></li>
{/section}
</ul>
{/if}

{if $albums}
{section name=i loop=$albums}
<h3><a href="{$albums[i].link}">{$albums[i].name}</a></h3>
<p>
<a href="{$albums[i].link}">{image id=$albums[i].idimg width=80 height=80 style="float:left"}</a>
{$albums[i].description|truncate:350}
</p>
<div class="clear"></div>
{/section}
{object obj=$albums_pager}
{/if}

{include file="_footer.tpl"}