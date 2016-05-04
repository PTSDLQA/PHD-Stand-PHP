{if $comments}
<form method="post">
<table width="100%" class="gridfix">
{if $owneritems}
<tr>
<th align="left" width="20">&nbsp;</th>
<th align="left">{if $item.name}{$item.name}{else}Все{/if}</th>
<th align="right">{if $item.name}<a class="cp_link_headding" href="admin.php?mode={$system.mode}&item={$system.item}&tab={$tab}" style="float:right;">Все</a>{else}&nbsp;{/if}</th>
<th width="20">&nbsp;</th>
</tr>
{/if}
{section name=i loop=$comments}
<tr class="{if $comments[i].active=='Y'}row0{else}close{/if}">
<td width="20"><input id="checkcomm{$smarty.section.i.index}" type="checkbox" name="checkcomm[]" value="{$comments[i].id}"></td>
<td><a href="javascript:geteditcommentform({$comments[i].id})">{$comments[i].date|date_format:"%d.%m.%Y %T"} <b>{$comments[i].name}</b>:</a></td>
<td align="right"class="gray">{if !$item.name}{$comments[i].itemname}{else}&nbsp;{/if}</td>
<td width="20" align="center"><a href="javascript:delcomment({$comments[i].id})" title="Удалить"><img src="/templates/admin/images/del.gif"></a></td>
</tr>
<tr class="row1">
<td colspan="4">
{$comments[i].message}
</td>
</tr>
{/section}
</table>
{object obj=$comments_pager}
{else}
<table width="100%" class="gridfix">
{if $owneritems}
<tr>
<th align="left" width="20">&nbsp;</th>
<th align="left">{if $item.name}{$item.name}{else}Все{/if}</th>
<th align="right">{if $item.name}<a class="cp_link_headding" href="admin.php?mode={$system.mode}&item={$system.item}&tab={$tab}" style="float:right;">Все</a>{else}&nbsp;{/if}</th>
<th width="20">&nbsp;</th>
</tr>
{/if}
</table>
<div class="box">Нет комментариев.</div>
{/if}
<table width="100%" class="actiongrid">
<tr>
{if $comments}
<td nowrap>
<label><input type="checkbox" onclick="checkcommentall(this.checked)">&nbsp;
Отмеченные:</label>&nbsp;
<select name="obj_action" onchange="runcommentaction(this.value,this.form)">
<option value="">-</option>
<option value="comm_active">Разместить</option>
<option value="comm_unactive">Отключить</option>
<option value="comm_delete">Удалить</option>
</select>
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="authcode" value=$system.authcode}
{hidden name="iditem" value=$smarty.get.iditemcomm}
</form>
</td>
{/if}
{if $smarty.get.iditemcomm || !$owneritems}
<td width="80%" align="right">
{button caption="Добавить" onclick="getaddcommentform()"}
</td>
{/if}
</tr>
</table>