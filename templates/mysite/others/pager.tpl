<div class="pager">
{if $prevlink}<a href="{$prevlink}">&laquo;</a>&nbsp;{/if}
{section name=i loop=$pagelinks}
{if $pagelinks[i].selected}
<b>{$pagelinks[i].name}</b>
{else}
<a href="{$pagelinks[i].link}">{$pagelinks[i].name}</a>
{/if}
&nbsp;
{/section}
{if $nextlink}<a href="{$nextlink}">&raquo;</a></a>&nbsp;{/if}
</div>
