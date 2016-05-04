{section name=i loop=$links}
{if $links[i].selected}
<a href="{$links[i].link}"><b>{$links[i].name}</b></a><br>
{else}
<a href="{$links[i].link}">{$links[i].name}</a><br>
{/if}
{/section}