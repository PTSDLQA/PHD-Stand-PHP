<form name="editsectionform" method="post" onsubmit="return section_editform(this)" enctype="multipart/form-data">
<table class="invisiblegrid" width="100%">
<tr>
<td width="70%">Название:<sup style="color:gray">*</sup></td>
<td width="30%">&nbsp;&nbsp;Идентификатор (URL):</td>
</tr>
<tr>
<td width="70%">{editbox name="caption" text=$form.caption}</td>
<td width="30%">&nbsp;&nbsp;{editbox name="urlname" width="90%" max=100 text=$form.urlname}</td>
</tr>
</table>
<table width="100%" class="invisiblegrid">
<tr>
<td width="120">Идентификатор:<sup style="color:gray">*</sup></td>
<td>&nbsp;</td>
</tr>
<tr>
<td width="120">
{editbox name="name" max=50 width="95%" text=$form.name}&nbsp;
</td>
<td>&nbsp;</td>
</tr>
</table>
<p><a class="cp_link_headding" href="javascript:togglepbox('sectionpbox')">Дополнительно</a></p>
<div id="sectionpbox" style="display:none">
{section name=i loop=$form.languages}
<div id="lang_{$form.languages[i].name}"{if $form.lang!='all' && $form.languages[i].name!=$form.lang} style="display:none"{/if}>
{if count($form.languages)>1}
<h3><div id="tlang_{$form.languages[i].name}"{if $form.languages[i].name==$form.lang} style="display:none"{/if}>{$form.languages[i].caption}</div></h3>
<div class="box">
{else}
<h3></h3>
<div>
{/if}
<p>Название на сайте:</p>
<p>{editbox name=$form.languages[i]._caption.field text=$form.languages[i]._caption.value max=100 width="40%"}</p>
<p>Заголовок (title):</p>
<p>{editbox name=$form.languages[i]._title.field text=$form.languages[i]._title.value}</p>
</div>
</div>
{/section}
{if $auth->isSuperAdmin()}
<p>Изображение:</p>
{if $form.idimg>0}
<table width="100%" class="invisiblegrid">
<tr>
<td width="80" align="center">
{image id=$form.idimg width=80 height=80 popup=true}
</td>
<td valign="top">
<p>Заменить:</p>
<p><input type="file" name="image"></p>
<p><label><input type="checkbox" name="imagedel">&nbsp;Удалить</label></p>
</td>
</tr>
</table>
{else}
<p><input type="file" name="image"></p>
{/if}
{/if}
</div>
{hidden name="id" value=$form.id}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="action" value="edit"}
{hidden name="authcode" value=$system.authcode}
<div align="right" style="margin-top:10px">
<p style="float:left"><label><input type="checkbox" name="active" value="Y" {if $form.active=='Y'}checked{/if}>&nbsp;Активен</label>&nbsp;&nbsp;<label><input type="checkbox" name="icon" value="Y" {if $form.icon=='Y'}checked{/if}>&nbsp;Иконка на главной панели</label>&nbsp;&nbsp;<label><input type="checkbox" name="menu" value="Y" {if $form.menu=='Y'}checked{/if}>&nbsp;Меню панели</label></p>
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>