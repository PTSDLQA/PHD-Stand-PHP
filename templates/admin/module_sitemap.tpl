{include file="_header.tpl"}

<form method="post">
<table class="grid" width="100%"> 
<tr>
<th align="left" width="20">&nbsp;</th>
<th align="left">Использовать разделы:</th>
</tr>
{section name=i loop=$sections}
<tr class="{cycle values="row0,row1"}"> 
<td width="20">
<input id="check{$smarty.section.i.index}" type="checkbox" id name="ids[]" value="{$sections[i].id}"{if $sections[i].checked} checked{/if}>
</td>
<td>{$sections[i].caption}</td>
</tr>
{/section}
</table>
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="authcode" value=$system.authcode} 
{hidden name="action" value="save"}

<table width="100%" class="actiongrid">
<tr>
<td align="right">
{submit caption="Сохранить"}
</td>
</tr>
</table>
</form>


{include file="_footer.tpl"} 
