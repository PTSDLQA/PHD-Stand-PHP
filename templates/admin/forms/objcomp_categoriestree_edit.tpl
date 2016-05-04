<form name="editcatform" method="post" onsubmit="return cat_form(this)" enctype="multipart/form-data">
<table class="invisiblegrid" width="100%">
<tr>
<td width="70%">Название:<sup style="color:gray">*</sup></td>
<td width="30%">&nbsp;&nbsp;Идентификатор (URL):</td>
</tr>
<tr>
<td width="70%">{editbox name="name" text=$form.name}</td>
<td width="30%">&nbsp;&nbsp;{editbox name="urlname" width="90%" max=100 text=$form.urlname}</td>
</tr>
</table>
<p><a class="cp_link_headding" href="javascript:togglepbox('{$system.item}_catpbox')">Дополнительно</a></p>
<div id="{$system.item}_catpbox" style="display:none">
<p>Описание:</p>
<p>{fckeditor name="description" toolbar=Medium height=200 text=$form.description}</p>
<p>Изображение:</p>
{if $form.idimg}
<table width="100%" class="invisiblegrid">
<tr>
<td width="80" height="80" align="center">
{image id=$form.idimg width=80 height=80 popup=true}
</td>
<td valign="top">
<p>Заменить:</p>
<p><input type="file" name="catimage"></p>
<p><label><input type="checkbox" name="imagedel">&nbsp;Удалить</label></p>
</td>
</tr>
</table>
{else}
<p><input type="file" name="catimage"></p>
{/if}
{include file="objcomp_fieldseditor_include.tpl"}
</div>
<input type="hidden" name="id" value="{$form.id}">
<input type="hidden" name="tab" value="cat">
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="obj_action" value="ct_edit"}
{hidden name="authcode" value=$system.authcode}
<div align="right" style="margin-top:10px">
<p style="float:left"><label><input type="checkbox" name="active"{if $form.active=="Y"} checked{/if}>&nbsp;Активно</label></p>
{submit caption="Сохранить"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>