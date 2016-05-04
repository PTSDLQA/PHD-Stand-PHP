<form name="addpageform" method="post" onsubmit="return addpage_form(this)" enctype="multipart/form-data">
<table class="invisiblegrid" width="100%" style="margin-bottom:5px">
<tr>
<td width="70%">Название:<sup style="color:gray">*</sup></td>
<td width="30%">&nbsp;&nbsp;Идентификатор (URL):</td>
</tr>
<tr>
<td>{editbox name="name"}</td>
<td>&nbsp;&nbsp;{editbox name="urlname" max=100 width="80%"}.html</td>
</tr>
</table>
{if $auth->isExpert()}
{fckeditor name="content" height=450}
{else}
{fckeditor name="content" height=500}
{/if}
{if $options.usetags}
<p>Теги (через запятую):</p>
<p>{tags}</p>
{else}
{hidden name="tags" value=""}
{/if}
{include file="objcomp_fieldseditor_include.tpl"}
{if $auth->isExpert()}
<p>Шаблон страницы:</p>
<p>{editbox name="template" max=50 text=$options.tpldefault width="20%"}</p>
{else}
{hidden name="template" value=$options.tpldefault}
{/if}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="page" value=$system.page}
{hidden name="authcode" value=$system.authcode}
{hidden name="action" value="addpage"}
<div align="right" style="margin-top:10px">
<p style="float:left">
<label><input type="checkbox" name="active" checked>&nbsp;Активно</label>&nbsp;&nbsp;&nbsp;&nbsp;
{if $form.usemap}<label><input type="checkbox" name="inmap" checked>&nbsp;В карте сайта</label>{else}{hidden name="inmap" value=1}{/if}
</p>
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>