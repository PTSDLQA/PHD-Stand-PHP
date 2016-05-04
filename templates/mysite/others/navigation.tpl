<div class="navigation">
{section name=i loop=$navigation}
{if !$smarty.section.i.first}&nbsp;&raquo;&nbsp;{/if}
{if $navigation[i].link}
<a href="{$navigation[i].link}">{$navigation[i].name}</a>
{else}
{$navigation[i].name}
{/if}
{/section}
</div>







