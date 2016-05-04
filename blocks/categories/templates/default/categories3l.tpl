{section name=i loop=$categories}
{if $categories[i].selected}
<a href="{$categories[i].link}"><b>{$categories[i].name}</b></a><br>
{else}
<a href="{$categories[i].link}">{$categories[i].name}</a><br>
{/if}
{/section}