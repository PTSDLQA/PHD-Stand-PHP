{include file="_header.tpl"}

{if $errors.doubleid}
<div class="warning">Некорректный идентификатор.</div>
{/if}
<div>
{if $sections}
<form name="sectionsform" method="post">
<table class="grid gridsort" width="100%">
<tr>
<th width="25">&nbsp;</th>
<th align="center" width="16">&nbsp;</th>
<th align="left" width="110">Идентификатор</th>
<th align="left">Название</th>
<th align="left" width="25%">Базовый модуль</th>
<th align="center" width="25">&nbsp;</th>
<th align="center" width="25">&nbsp;</th>
</tr>
</table>
<div id="sectionsbox" class="gridsortbox">
{section name=i loop=$sections}
<table id="section_{$sections[i].id}" class="grid gridsort" width="100%">
<tr class="{if $sections[i].active=='Y'}{cycle values="row0,row1"}{else}close{/if}">
<td width="20"><input type="checkbox" id="check{$smarty.section.i.index}" name="checksection[]" value="{$sections[i].id}"></td>
<td width="16">{$sections[i].ico}</td>
<td width="110"><a href="javascript:geteditsectionform({$sections[i].id})" title="Редактировать">{$sections[i].name}</a></td>
<td><a href="admin.php?mode=sections&item={$sections[i].section}" title="Перейти к управлению">{$sections[i].caption}</a></td>
<td width="25%">{$sections[i].modcaption}</td>
<td align="center" width="25"><a href="{$sections[i].link}" target="_blank" title="Просмотр на сайте"><img src="/templates/admin/images/browse.gif" width="16" height="16" alt="Просмотр на сайте"></a></td>
<td align="center" width="20"><a href="javascript:delsection({$sections[i].id})" title="Удалить"><img src="/templates/admin/images/del.gif" width="16" height="16" alt="Удалить"></a></td>
</tr>
</table>
{/section}
</div>
{literal}<script type="text/javascript">
Sortable.create('sectionsbox',{tag:'table',onUpdate: setsectionsort});
</script>{/literal}
{else}
<div class="box">Не созданы разделы.</div>
{/if}
<table width="100%" class="actiongrid">
<tr>
{if $sections}
<td nowrap>
<label><input type="checkbox" onclick="checkall(this.checked)">&nbsp;
Отмеченные:</label>&nbsp;
<select name="action" onchange="runaction(this.value,this.form)">
<option value="">-</option>
<option value="setactive">Включить</option>
<option value="setunactive">Отключить</option>
<option value="delete">Удалить</option>
</select>
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="authcode" value=$system.authcode}
</form>
</td>
{/if}
{if $sections}
<td nowrap>
<form name="optionsform" method="post">
Главный раздел:&nbsp;
<select name="value" onchange="document.forms.optionsform.submit()">
{html_options options=$optionsform.sections selected=$options.mainsection}
</select>
<input type="hidden" name="id" value="{$optionsform.id}">
<input type="hidden" name="mode" value="site">
<input type="hidden" name="item" value="sections">
<input type="hidden" name="action" value="save">
{hidden name="authcode" value=$system.authcode}
</form>
</td>
{/if}
<td align="right" width="60%">
{button caption="Добавить" onclick="getaddsectionform()"}
</td>
</tr>
</table>
</div>

{include file="_footer.tpl"}
