{if $pagelinks}
<table class="grid" style="margin-top:3px;margin-bottom:3px;">
<tr>
<th align="center">
{if $prevlink}
<span class="pagenav"><a href="{$prevlink}" title="Предыдущая">&laquo;</a></span>
{/if}
{section name=i loop=$pagelinks}
{if $pagelinks[i].selected}
<span class="pagenav"><b>{$pagelinks[i].name}</b></span>
{else}
<span class="pagenav"><a href="{$pagelinks[i].link}">{$pagelinks[i].name}</a></span>
{/if}
{/section}
{if $nextlink}
<span class="pagenav"><a href="{$nextlink}" title="Следующая">&raquo;</a></span>
{/if}
</th>
</tr>
</table>
{/if}
