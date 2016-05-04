{include file="_header.tpl"}

{tabcontrol pages="Страницы" opt="Настройки"}

{tabpage id="pages"}

<div id="gridfilesbox_">
<form name="pagesform" method="post">
<div id="gridfilesbox"></div>
<table width="100%" class="actiongrid">
<tr>
<td nowrap>
<label><input type="checkbox" id="checkboxall" onclick="checkall(this.checked)">&nbsp;
Отмеченные:</label>&nbsp;
<select name="action" onchange="runaction(this.value,this.form)">
<option value="">-</option>
<option value="setactive">Включить</option>
<option value="setunactive">Отключить</option>
<option value="move">Переместить</option>
<option value="delete">Удалить</option>
</select>
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="authcode" value=$system.authcode}
</td>
</form>
<td nowrap>
<form name="rowsform" method="post">
На странице:
<select name="rows" onchange="this.form.submit()">
<option value="20">20</option>
<option value="50">50</option>
<option value="100">100</option>
<option value="0">Все</option>
</select>
<script type="text/javascript">document.forms.rowsform.rows.value='{$rows}';</script>
{hidden name="action" value="setrows"}
{hidden name="authcode" value=$system.authcode}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="tab" value="pages"}
</form>
</td>
<td align="right" width="80%">
{button caption="Новый подраздел" onclick="getadddirform()"}
{button caption="Новая страница" onclick="getaddpageform()"}
</td>
</tr>
</table>
</div>
<script type="text/javascript">
indir({$curdir});
</script>
{/tabpage}

{tabpage id="opt"}
{tabcontrol id="opt" opt2="Опции" fields="Редактор&nbsp;полей"}
{tabpage idtab="opt" id="opt2"}
{object obj=$optbox}
{/tabpage}
{tabpage idtab="opt" id="fields"}
{object obj=$fieldsbox}
{/tabpage}
{/tabpage}

{include file="_footer.tpl"}
