<div>
<h3>{$form.name}</h3>
<div class="box">
<table>
{section name=i loop=$form.result}
<tr>
<td>{$form.result[i].name}</td>
<td>
<img src="/modules/voting/images/{$smarty.section.i.index%6}.gif" height="10" width="{$form.result[i].pr*2}">
<b>{$form.result[i].pr}% ({$form.result[i].count})</b>
</td>
</tr>
{/section}
</table>
</div>
<p>Всего ответило: <b>{$form.count}</b></p>
</div>