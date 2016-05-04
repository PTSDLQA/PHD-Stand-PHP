{if $active}
{if $isvoting}
<p>{$question}</p>
<table>
{section name=i loop=$variants}
<tr>
<td nowrap>{$variants[i].name}</td>
<td>
{if $variants[i].count>0}
<img src="/modules/voting/images/{$smarty.section.i.index%6}.gif" height="10" width="{$variants[i].pr/2}">
{$variants[i].pr}% ({$variants[i].count})
{/if}
</td>
</tr>
{/section}
</table>
{else}
<p>{$question}</p>
<form action="{$sectionlink}" method="post">
{section name=i loop=$variants}
<input type="radio" name="id" value="{$variants[i].id}">{$variants[i].name}<br>
{/section}
<input type="hidden" name="action" value="addvote">
<input type="submit" value="Голосовать">
</form>
{/if}
{/if}