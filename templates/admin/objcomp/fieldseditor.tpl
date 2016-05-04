{if $message=="doublefield"}
<div class="warning">Поле с таким именем уже существует!</div>
{/if}
{if $fields}
<table width="100%" class="grid gridsort">
<tr>
<th align="left" width="103">Название</th>
<th align="left">Описание</th>
<th align="left" width="200">Тип</th>
{if $usefill}<th width="20">&nbsp;</th>{/if}
<th width="23">&nbsp;</th>
</tr>
</table>
<div id="fieldsbox" class="gridsortbox">
{section name=i loop=$fields}
<table id="field_{$fields[i].id}" width="100%" class="grid gridsort">
<tr class="{cycle values="row0,row1"}">
<td width="100"><a href="javascript:geteditfieldform({$fields[i].id})" title="Редактировать">{$fields[i].field}</a></td>
<td>{$fields[i].name}</td>
<td width="200">{$fields[i].type}</td>
{if $usefill}<td width="20">{if $fields[i].fill=="Y"}<img src="/templates/admin/images/checked2.gif" alt="Обязательно для заполнения" width="16" height="16">{else}&nbsp;{/if}</td>{/if}
<td width="20"><a href="javascript:delfield({$fields[i].id})" title="Удалить"><img src="/templates/admin/images/del.gif" width="16" height="16" alt="Удалить"></a></td>
</tr>
</table>
{/section}
</div>
{literal}<script type="text/javascript">
Sortable.create('fieldsbox',{tag:'table',onUpdate: setfieldssort});
</script>{/literal}
{else}
<div class="box">Нет дополнительных полей.</div>
{/if}
<table class="actiongrid">
<tr>
<td align="right">
{button caption="Добавить" onclick="getaddfieldform()"}
</td>
</tr>
</table>