<form name="addsectionform" method="post" onsubmit="return section_addform(this)" enctype="multipart/form-data">
<table class="invisiblegrid" width="100%">
<tr>
<td width="70%">Название:<sup style="color:gray">*</sup></td>
<td width="30%">&nbsp;&nbsp;Идентификатор (URL):</td>
</tr>
<tr>
<td width="70%">{editbox name="caption"}</td>
<td width="30%">&nbsp;&nbsp;{editbox name="urlname" max=100 width="90%"}</td>
</tr>
</table>
<table width="100%" class="invisiblegrid">
<tr>
<td width="120">Идентификатор:<sup style="color:gray">*</sup></td>
<td width="200">Базовый модуль:<sup style="color:gray">*</sup></td>
<td>&nbsp;</td>
</tr>
<tr>
<td width="120">
{editbox name="name" max=50 width="95%" text="newsection"}
</td>
<td width="200">
<select name="module" onchange="selmodule(this.form,this.value)" style="width:100%">
<option value="" selected>Не выбрано</option>
{html_options options=$form.modules}
</select>
</td>
<td>&nbsp;</td>
</tr>
</table>
<p><a class="cp_link_headding" href="javascript:togglepbox('sectionpbox')">Дополнительно</a></p>
<div id="sectionpbox" style="display:none">
{section name=i loop=$form.languages}
<div id="lang_{$form.languages[i].name}"{if $form.languages[i].name!=$system.lang} style="display:none"{/if}>
{if count($form.languages)>1}
<h3><div id="tlang_{$form.languages[i].name}"{if $form.languages[i].name==$system.lang} style="display:none"{/if}>{$form.languages[i].caption}</div></h3>
<div class="box">
{else}
<h3></h3>
<div>
{/if}
<p>Название на сайте:</p>
<p>{editbox name=$form.languages[i]._caption.field max=100 width="40%"}</p>
<p>Заголовок (title):</p>
<p>{editbox name=$form.languages[i]._title.field}</p>
</div>
</div>
{/section}
{if $auth->isSuperAdmin()}
<p>Изображение:</p>
<p><input type="file" name="image"></p>
{/if}
</div>
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="action" value="add"}
{hidden name="authcode" value=$system.authcode}
<div align="right" style="margin-top:10px">
<p style="float:left"><label><input type="checkbox" name="active" value="Y" checked>&nbsp;Активен</label>&nbsp;&nbsp;<label><input type="checkbox" name="icon" value="Y" checked>&nbsp;Иконка на главной панели</label>&nbsp;&nbsp;<label><input type="checkbox" name="menu" value="Y" checked>&nbsp;Меню панели</label></p>
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>