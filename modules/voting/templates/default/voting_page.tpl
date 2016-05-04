{include file="_header.tpl"}

<h1>{$question}</h1>

<h4>Текущие результаты:</h4>

<table>
{section name=i loop=$result}
<tr>
<td>{$result[i].name}</td>
<td>
<img src="/modules/voting/images/{$smarty.section.i.index%6}.gif" height="10" width="{$result[i].pr*2}">
</td>
<td>
{$result[i].pr}% ({$result[i].count})
</td>
</tr>
{/section}
</table>

<p>Всего проголосовало: <b>{$allcount}</b></p>

{include file="_footer.tpl"}
