{include file="_header.tpl"}

{object obj=$optbox1}

<form method="post">
<table class="grid" width="100%">
<tr>
<th align="left">Использовать разделы:</th>
</tr>
{section name=i loop=$sections}
<tr class="{cycle values="row0,row1"}">
<td>
<label><input id="check{$smarty.section.i.index}" type="checkbox" id name="ids[]" value="{$sections[i].id}"{if $sections[i].checked} checked{/if}>&nbsp;&nbsp;{$sections[i].caption}</label>
</td>
</tr>
{/section}
</table>
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="action" value="savesections"}

<table width="100%" class="actiongrid">
<tr>
<td align="right">
{submit caption="Сохранить"}
</td>
</tr>
</table>
{hidden name="authcode" value=$system.authcode}
</form>

{include file="_footer.tpl"}
