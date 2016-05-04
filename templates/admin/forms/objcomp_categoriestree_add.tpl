<form name="addcatform" method="post" onsubmit="return cat_form(this)" enctype="multipart/form-data">
<table class="invisiblegrid" width="100%">
<tr>
<td width="70%">Название:<sup style="color:gray">*</sup></td>
<td width="30%">&nbsp;&nbsp;Идентификатор (URL):</td>
</tr>
<tr>
<td width="70%">{editbox name="name"}</td>
<td width="30%">&nbsp;&nbsp;{editbox name="urlname" max=100 width="90%"}</td>
</tr>
</table>
{if $form.id>0}
<p><label><input type="checkbox" name="subitem" checked>&nbsp;Подуровень</label></p>
{/if}
<p><a class="cp_link_headding" href="javascript:togglepbox('{$system.item}_catpbox')">Дополнительно</a></p>
<div id="{$system.item}_catpbox" style="display:none">
<p>Описание:</p>
<p>{fckeditor name="description" toolbar=Medium height=200}</p>
<p>Изображение:</p>
<p><input type="file" name="catimage"></p>
{include file="objcomp_fieldseditor_include.tpl"}
</div>
<input type="hidden" name="id" value="{$form.id}">
<input type="hidden" name="idker" value="{$form.idker}">
<input type="hidden" name="level" value="{$form.level}">
<input type="hidden" name="tab" value="cat">
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="obj_action" value="ct_add"}
{hidden name="authcode" value=$system.authcode}
<div align="right" style="margin-top:10px">
<p style="float:left"><label><input type="checkbox" name="active" checked>&nbsp;Активно</label></p>
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>