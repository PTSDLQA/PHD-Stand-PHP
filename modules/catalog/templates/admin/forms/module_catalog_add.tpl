<form name="additemform" method="post" onsubmit="return item_form(this)" enctype="multipart/form-data">
<table class="invisiblegrid" width="100%">
<tr>
<td width="70%">Название:<sup style="color:gray">*</sup></td>
<td width="30%">&nbsp;&nbsp;Идентификатор (URL):</td>
</tr>
<tr>
<td width="70%">{editbox name="name"}</td>
<td width="30%">&nbsp;&nbsp;{editbox name="urlname" max=100 width="80%"}.html</td>
</tr>
{if $options.usecats && $form.categories}
<tr>
<td width="70%">Категория:</td>
<td width="30%">&nbsp;&nbsp;{if $options.usedate}Дата:{/if}</td>
</tr>
<tr>
<td width="70%">{treeselect name="idcat" items=$form.categories selected=$form.idcat title="Выбор категории" width="60%"}</td>
<td width="30%">&nbsp;&nbsp;{if $options.usedate}{dateselect name="date" usetime=true}{/if}</td>
{/if}
</table>
<p>Текст:</p>
{if $options.fckeditor}
{fckeditor name="content" height=400}
{else}
{textarea name="content" rows=6}
{/if}
{if !$options.autoanons}
<p>Аннотация:</p>
<p>{textarea name="description" rows=3}</p>
{/if}
{if $options.usetags}
<p>Теги (через запятую):</p>
<p>{tags}</p>
{else}
{hidden name="tags" value=""}
{/if}
{if $options.useimages}
<div class="box">
<div id="imageitems1">
<table class="invisiblegrid" width="100%">
<tr>
<td>Фото 1:</td>
<td width="80%">Описание 1:</td>
</tr>
<tr>
<td><input type="file" name="image1"></td>
<td width="80%"><input type="text" name="imagedescription1" style="width:100%"></td>
</tr>
</table>
</div>
<div id="imageitems2"></div>
<div id="imageitems3"></div>
<div id="imageitems4"></div>
<div id="imageitems5"></div>
<p><a href="javascript:additemimage()" title="Добавить"><img src="/templates/admin/images/add.gif" width="16" height="16" alt="Добавить"></a></p>
</div>
{/if}
{if $options.usefiles}
<p>Прикрепленные файлы:</p>
<div class="box">
<div id="fileitems1">
<table class="invisiblegrid" width="100%">
<tr>
<td>Файл 1:</td>
<td width="80%">Описание 1:</td>
</tr>
<tr>
<td><input type="file" name="file1"></td>
<td width="80%"><input type="text" name="filedescription1" style="width:100%"></td>
</tr>
</table>
</div>
<div id="fileitems2"></div>
<div id="fileitems3"></div>
<div id="fileitems4"></div>
<div id="fileitems5"></div>
<p><a href="javascript:additemfile()" title="Добавить"><img src="/templates/admin/images/add.gif" width="16" height="16" alt="Добавить"></a></p>
</div>
{/if}
{include file="objcomp_fieldseditor_include.tpl"}
{if !$options.usecats || !$form.categories}
{if $options.usedate}
<p>Дата:</p>
<p>{dateselect name="date" usetime=true}</p>
{/if}
{hidden name="idcat" value=0}
{/if}
{hidden name="tab" value="items"}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="authcode" value=$system.authcode}
{hidden name="action" value="additem"}
<div align="right" style="margin-top:10px">
<p style="float:left"><label><input type="checkbox" name="active" checked>&nbsp;Активно</label></p>
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>