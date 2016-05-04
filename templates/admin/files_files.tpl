{include file="_header.tpl"}

{if $errors.existsfile}
<div class="warning">Файл с таким реальным именем уже существует!</div>
{/if}

{if $cursection}
<h3><a href="{$cursectionlink}" title="Перейти">{$cursection}:</a></h3>
{/if}

{if $files}
<div id="filesbox">
<form name="filesform" method="post">
<table class="grid" width="100%">
<tr>
<th width="20">&nbsp;</th>
<th align="left" width="20">ID</th>
<th align="left">Файл</th>
<th align="left">Описание</th>
<th align="left">Раздел</th>
<th align="left" width="70">Размер</th>
<th align="left" width="30">Скач.</th>
<th align="left" width="20">&nbsp;</th>
<th align="left" width="20">&nbsp;</th>
</tr>
{section name=i loop=$files}
<tr class="{cycle values="row0,row1"}">
<td width="20"><input type="checkbox" id="check{$smarty.section.i.index}" name="checkfile[]" value="{$files[i].id}"></td>
<td width="20">{$files[i].id}</td>
<td><a href="javascript:fm_geteditform({$files[i].id})" title="Редактировать">{$files[i].basename}</a></td>
<td>{$files[i].caption}</td>
<td nowrap>{$files[i].section}</td>
<td width="70">{$files[i].size}</td>
<td width="30">{$files[i].dwnl}</td>
<td width="20"><a href="download.php?id={$files[i].id}" title="Скачать"><img src="/templates/admin/images/save.gif" width="16" height="16" alt="Скачать"></a></td>
<td width="20"><a href="javascript:fm_del({$files[i].id})" title="Удалить"><img src="/templates/admin/images/del.gif" width="16" height="16" alt="Удалить"></a></td>
</tr>
{/section}
</table>
{object obj=$files_pager}
{else}
<div class="box">Нет файлов.</div>
{/if}
<table class="actiongrid" width="100%">
<tr>
{if $files}
<td nowrap>
<label><input type="checkbox" onclick="checkall(this.checked)">&nbsp;
Отмеченные:</label>&nbsp;
<select name="action" onchange="runaction(this.value,this.form)">
<option value="">-</option>
<option value="delete">Удалить</option>
</select>
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="authcode" value=$system.authcode}
</form>
</td>
<td nowrap>
<form method="get">
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
Разделы:&nbsp;
<select name="idsec" onchange="this.form.submit()">
<option value="0">Все</option>
{html_options options=$sections selected=$smarty.get.idsec}
</select>
{hidden name="authcode" value=$system.authcode}
</form>
</td>
<td nowrap>
<form name="rowsform" method="post">
Строк:
<select name="rows" onchange="this.form.submit()">
<option value="20">20</option>
<option value="50">50</option>
<option value="100">100</option>
</select>
<script type="text/javascript">document.forms.rowsform.rows.value='{$rows}';</script>
{hidden name="action" value="setrows"}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="authcode" value=$system.authcode}
</form>
</td>
{/if}
<td width="80%" align="right">
{if $auth->isSuperAdmin()}
{button caption="Зарегистрировать" onclick="fm_getregisterform()"}
{/if}
{button caption="Загрузить" onclick="fm_getuploadform()"}
</td>
</tr>
</table>
</div>
<div id="fm_editbox"></div>

{include file="_footer.tpl"}
