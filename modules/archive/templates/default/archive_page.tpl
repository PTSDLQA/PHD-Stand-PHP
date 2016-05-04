{include file="_header.tpl"}

<h1>{$section_name}</h1>

{$calendar}

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
{else}
<p>Не найдено.</p>
{/if}

{include file="_footer.tpl"}