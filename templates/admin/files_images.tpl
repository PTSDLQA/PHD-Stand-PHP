{include file="_header.tpl"}

{if $errors.existsfile}
<div class="warning">Файл с таким именем уже существует!</div>
{/if}

{if $cursection}
<h3><a href="{$cursectionlink}" title="Перейти">{$cursection}:</a></h3>
{/if}

{if $images}
<div id="imagesbox">
<form name="imagesform" method="post">
<table width="100%" class="grid">
<tr>
<th width="20">&nbsp;</th>
<th width="20">ID</th>
<th align="left" width="80">&nbsp;</th>
<th align="left" width="60">Размер</th>
<th align="left">Файл</th>
<th align="left">Описание</th>
<th align="left">Раздел</th>
<th width="20">&nbsp;</th>
</tr>
{section name=i loop=$images}
<tr class="{cycle values="row0,row1"}">
<td width="20"><input type="checkbox" id="check{$smarty.section.i.index}" name="checkimg[]" value="{$images[i].id}"></td>
<td width="20">{$images[i].id}</td>
<td align="center" width="80">{image id=$images[i].id width=80 height=50 popup=true}</td>
<td align="left" width="60">{$images[i].width}X{$images[i].height}</td>
<td><a href="javascript:im_geteditform({$images[i].id})" title="Редактировать">{$images[i].basename}</a></td>
<td>{$images[i].caption}</td>
<td nowrap>{$images[i].section}</td>
<td width="20"><a href="javascript:im_del({$images[i].id})" title="Удалить"><img src="/templates/admin/images/del.gif" width="16" height="16" align="Удалить"></a></td>
</tr>
{/section}
</table>
{object obj=$images_pager}
{else}
<div class="box">Нет изображений.</div>
{/if}
<table class="actiongrid" width="100%">
<tr>
{if $images}
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
{button caption="Загрузить" onclick="im_getuploadform()"}
</td>
</tr>
</table>
</div>
<div id="im_editbox"></div>

{include file="_footer.tpl"}
