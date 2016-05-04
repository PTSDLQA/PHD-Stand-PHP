<form name="edititemform" method="post" onsubmit="return item_form(this)" enctype="multipart/form-data">
<table class="invisiblegrid" width="100%">
<tr>
<td width="70%">Название:<sup style="color:gray">*</sup></td>
<td width="30%">&nbsp;&nbsp;Идентификатор (URL):</td>
</tr>
<tr>
<td width="70%">{editbox name="name" text=$form.name}</td>
<td width="30%">&nbsp;&nbsp;{editbox name="urlname" max=100 width="80%" text=$form.urlname}.html</td>
</tr>
</table>
{if $options.usecats && $form.categories}
<p>Категория:</p>
<p>{treeselect name="idcat" items=$form.categories selected=$form.idcat title="Выбор категории"}</p>
{else}
{hidden name="idcat" value=0}
{/if}
<p>Текст:</p>
{if $options.fckeditor}
{fckeditor name="description" height=200 toolbar="Medium" text=$form.description}
{else}
{textarea name="description" rows=4 text=$form.description}
{/if}
{include file="objcomp_fieldseditor_include.tpl"}
{if $options.usedate}
<p>Дата:</p>
<p>{dateselect name="date" usetime=true date=$form.date}</p>
{/if}
{if $options.usetags}
<p>Теги (через запятую):</p>
<p>{tags text=$form.tags}</p>
{else}
{hidden name="tags" value=$form.tags}
{/if}
{hidden name="id" value=$form.id}
{hidden name="tab" value="albums"}
{hidden name="tab_albums" value=""}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="authcode" value=$system.authcode}
{hidden name="action" value="edititem"}
<div align="right" style="margin-top:10px">
<p style="float:left"><label><input type="checkbox" name="active"{if $form.active=="Y"} checked{/if}>&nbsp;Активно</label></p>
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>