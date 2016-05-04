{include file="_header.tpl"}

<h1>{$section_name}</h1>

<form method="get">
{editbox name="query" width="20%" text=$smarty.get.query}
{submit caption="Искать"}
</form>

{if $items}
{section name=i loop=$items}
<h3>{$items[i].num}. <a href="{$items[i].link}">{$items[i].name}</a></h3>
<p>
{image data=$items[i].images style="float:left" width=80 height=80}
{$items[i].description}
</p>
{if $items[i].tags}
<p>
{section name=j loop=$items[i].tags}
<a href="{$items[i].tags[j].link}">{$items[i].tags[j].name}</a>{if !$smarty.section.j.last}, {/if}
{/section}
{/if}
</p>
<div class="clear"></div>
{/section}
{object obj=$items_pager}
{/if}

{include file="_footer.tpl"}