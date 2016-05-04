<form name="editpageform" action="" method="post" onsubmit="return editpage_form(this)" enctype="multipart/form-data">
<table class="invisiblegrid" width="100%" style="margin-bottom:5px">
<tr>
<td width="70%">Название:<sup style="color:gray">*</sup></td>
<td width="30%">&nbsp;&nbsp;Идентификатор (URL):</td>
</tr>
<tr>
<td>{editbox name="name" text=$form.name}</td>
<td>&nbsp;&nbsp;{editbox name="urlname" width="80%" max=100 text=$form.urlname}.html</td>
</tr>
</table>
{if $auth->isExpert()}
{fckeditor name="content" height=450 text=$form.content}
{else}
{fckeditor name="content" height=500 text=$form.content}
{/if}
{if $options.usetags}
<p>Теги (через запятую):</p>
<p>{tags text=$form.tags}</p>
{else}
{hidden name="tags" value=$form.tags}
{/if}
{include file="objcomp_fieldseditor_include.tpl"}
{if $auth->isExpert()}
<p>Шаблон страницы:</p>
<p>{editbox name="template" max=50 text=$form.template width="20%"}</p>
{else}
{hidden name="template" value=$form.template}
{/if}
<div align="right" style="margin-top:10px">
<p style="float:left">
<label><input type="checkbox" name="active"{if $form.active=="Y"} checked{/if}>&nbsp;Активно</label>&nbsp;&nbsp;&nbsp;&nbsp;
{if $form.usemap}<label><input type="checkbox" name="inmap"{if $form.inmap=="Y"} checked{/if}>&nbsp;В карте сайта</label>&nbsp;&nbsp;&nbsp;&nbsp;{else}{hidden name="inmap" value=1}{/if}
<a href="javascript:applypage(document.forms.editpageform)" title="Сохранить не закрывая"><img src="/templates/admin/images/save.gif" width="16" height="16" style="vertical-align:middle"></a>
<span id="applydate">{$form.date|date_format:"%d.%m.%Y %T"}</span>
</p>
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
{hidden name="id" value=$form.id}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="page" value=$system.page}
{hidden name="authcode" value=$system.authcode}
{hidden name="action" value="editpage"}
</form>