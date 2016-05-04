{include file="_header.tpl"}

<h1>{$category.name}</h1>

{$category.description}

{if $categories}
<ul>
{section name=i loop=$categories}
<li><a href="{$categories[i].link}">{$categories[i].name} ({$categories[i].citems})</a></li>
{/section}
</ul>
{/if}

{if $items}
{section name=i loop=$items}
<h3><a href="{$items[i].link}">{$items[i].name}</a></h3>
<p>
{image data=$items[i].images width=80 height=80 style="float:left"}
{$items[i].description}
</p>
<div class="clear"></div>
{/section}
{object obj=$items_pager}
{/if}

{include file="_footer.tpl"}

