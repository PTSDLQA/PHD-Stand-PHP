<form name="addblockform" method="post" onsubmit="return block_form(this)" enctype="multipart/form-data">
<p>Название:<sup style="color:gray">*</sup></p>
<p>{editbox name="caption" width="40%"}</p>
<table width="100%" class="invisiblegrid">
<tr>
<td width="150">Расположение:<sup style="color:gray">*</sup></td>
<td width="150">Идентификатор:</td>
<td width="150">Шаблон обрамления:</td>
<td width="150">Иконка для раздела:</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>
<select name="align" onchange="selalign(this.form,this.value)" style="width:100%">
<option value="left">слева</option>
<option value="right">справа</option>
<option value="free">заданное</option>
</select>
</td>
<td width="150">{editbox name="name" max=50}</td>
<td width="150">{editbox name="frame" text="leftblock.tpl" max=50}</td>
<td width="150"><select name="itemeditor" style="width:100%"><option value="">Не выбрано</option>{html_options options=$form.items}</select></td>
<td>&nbsp;</td>
</tr>
</table>
<p>Базовый блок:<sup style="color:gray">*</sup></p>
<p>
<select name="block" onchange="onchangetype_add(this.value)">
{html_options options=$form.blocks selected="_text"}
</select>
</p>
<div id="optionsbox" class="blockopt"></div>
<div id="showoptions">
<p><label><input type="checkbox" name="check_showall" value="Y" checked onclick="showallcheck(this.checked)">&nbsp;Создается всегда</label></p>
<div id="showoptions2" style="display:none">
{if $form.sections}
<table width="100%" class="grid">
<tr>
<th align="left" width="100" style="font-size:11">Раздел</th>
<th align="left" style="font-size:11">Страницы</th>
</tr>
{section name=i loop=$form.sections}
<tr class="{cycle values="row0,row1"}">
<td width="100" nowrap>{$form.sections[i].caption}</td>
<td style="font-size:10">
{section name=j loop=$form.sections[i].pages}
<label><input type="checkbox" name="showcheck[]" value="{$form.sections[i].id}_{$form.sections[i].pages[j].page}">&nbsp;{$form.sections[i].pages[j].caption}</label>
{if !$smarty.section.j.last}, {/if}
{sectionelse}
<input type="checkbox" name="showcheck[]" value="{$form.sections[i].id}">
{/section}
</td>
</tr>
{/section}
</table>
{/if}
</div>
</div>
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="action" value="add"}
{hidden name="authcode" value=$system.authcode}
<div align="right" style="margin-top:10px">
<p style="float:left"><label><input type="checkbox" name="active" value="Y" checked>&nbsp;Активен</label>&nbsp;&nbsp;&nbsp;<label><input type="checkbox" name="checkicon">&nbsp;Иконка на главной панели</label></p>
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>
