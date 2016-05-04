{include file="_header.tpl"}

{if $errors.doublelogin}
<div class="warning">Указанный логин уже используется!</div>
{/if}

<table class="grid">
<tr>
<th align="left" width="220">Логин</th>
<th align="left">Имя</th>
<th align="left" width="120">Последний визит</th>
<th width="20">&nbsp;</th>
<th width="20">&nbsp;</th>
<th width="20">&nbsp;</th>
</tr>
{section name=i loop=$admins}
<tr class="{if $admins[i].active=='Y'}{cycle values="row0,row1"}{else}close{/if}">
<td width="220"><a href="javascript:geteditadminform({$admins[i].id})" title="Редактировать">{$admins[i].login}</a></td>
<td>{$admins[i].name}</td>
<td width="120">{if $admins[i].dauth}{if $admins[i].dauth>=$smarty.now}на сайте{else}{$admins[i].dauth|date_format:"%D %T"}{/if}{else}не было{/if}</td>
<td width="20"><img src="/templates/admin/images/star.gif" width="16" height="16" alt="Суперадминистратор"></td>
<td width="20"><img src="/templates/admin/images/admin.gif" width="16" height="16" alt="Администратор"></td>
<td width="20">{if $admins[i].id!=$auth->id}<a href="javascript:deladmin({$admins[i].id})" title="Удалить"><img src="/templates/admin/images/del.gif" width="16" height="16" alt="Удалить"></a>{else}&nbsp;{/if}</td>
</tr>
{/section}
</table>
{object obj=$admins_pager}
<table class="actiongrid">
<tr>
<td align="right">
{button caption="Добавить" onclick="getaddadminform()"}
</td>
</tr>
</table>

{include file="_footer.tpl"}
