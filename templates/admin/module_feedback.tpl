{include file="_header.tpl"}

{tabcontrol page="Страница" arch="Архив сообщений" opt="Настройки"}

{tabpage id="page"}
<h3>Текст:</h3>
<div class="box">
{if $maincontent}{$maincontent}{else}<i>Нет текста.</i>{/if}
<div class="clear"></div>
</div>
<table class="actiongrid">
<tr>
<td align="right">
{button caption="Изменить" onclick="geteditpageform()"}
</td>
</tr>
</table>

<h3>Форма:</h3>
{if $errors.doublefield}
<div class="warning">Поле с таким именем уже существует!</div>
{/if}
{if $fields}
<table width="100%" class="grid gridsort">
<tr>
<th align="left" width="102">Название</th>
<th align="left">Описание</th>
<th align="left" width="200">Тип</th>
<th width="20">&nbsp;</th>
<th width="25">&nbsp;</th>
</tr>
</table>
<div id="fieldsbox" class="gridsortbox">
{section name=i loop=$fields}
<table id="field_{$fields[i].id}" width="100%" class="grid gridsort">
<tr class="{cycle values="row0,row1"}">
<td width="100">
<a href="javascript:geteditfieldform({$fields[i].id})" title="Редактировать">{$fields[i].field}</a>
</td>
<td>{$fields[i].name}</td>
<td width="200">{$fields[i].type}</td>
<td width="20">{if $fields[i].fill=="Y"}<img src="/templates/admin/images/checked2.gif" alt="Обязательно для заполнения" width="16" height="16">{else}&nbsp;{/if}</td>
<td width="20"><a href="javascript:delfield({$fields[i].id})" title="Удалить"><img src="/templates/admin/images/del.gif" width="16" height="16"></a></td>
</tr>
</table>
{/section}
</div>
{literal}<script type="text/javascript">
Sortable.create('fieldsbox',{tag:'table',onUpdate: setfieldsort});
</script>{/literal}
{else}
<div class="box">Нет созданых полей.</div>
{/if}
<table class="actiongrid">
<tr>
<td align="right">
{button caption="Добавить" onclick="getaddfieldform()"}
</td>
</tr>
</table>
{/tabpage}

{tabpage id="arch"}
{if $arch}
<form method="post">
<table class="grid">
<tr>
<th width="20">&nbsp;</th>
<th align="left" width="120">Дата</th>
<th align="left">Сообщение</th>
<th width="20">&nbsp;</th>
</tr>
{section name=i loop=$arch}
<tr class="{cycle values="row0,row1"}">
<td width="20"><input type="checkbox" id="check{$smarty.section.i.index}" name="checkarch[]" value="{$arch[i].id}"></td>
<td width="120">{$arch[i].date|date_format:"%d.%m.%Y %T"}</td>
<td><a href="javascript:getarchmessageform({$arch[i].id})">{$arch[i].message|strip_tags|truncate:120}</a></td>
<td width="20"><a href="javascript:delarch({$arch[i].id})" title="Удалить"><img src="/templates/admin/images/del.gif" width="16" height="16"></a></td>
</tr>
{/section}
</table>
{object obj=$arch_pager}
<table width="100%" class="actiongrid">
<tr>
<td nowrap>
<label><input type="checkbox" onclick="checkall(this.checked)">&nbsp;
Отмеченные:</label>&nbsp;
<select name="action" onchange="runaction(this.value,this.form)">
<option value="">-</option>
<option value="delete">Удалить</option>
</select>
{hidden name="tab" value="arch"}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="authcode" value=$system.authcode}
</form>
</td>
</tr>
</table>
{else}
<div class="box">Нет сообщений.</div>
{/if}
{/tabpage}

{tabpage id="opt"}
{object obj=$optbox}
{/tabpage}

{include file="_footer.tpl"}
