<form name="edititemform" method="post" onsubmit="return item_form(this)" enctype="multipart/form-data">
<table class="invisiblegrid" width="100%">
<tr>
<td width="70%">Название:<sup style="color:gray">*</sup></td>
<td width="30%">&nbsp;&nbsp;Идентификатор (URL):</td>
</tr>
<tr>
<td width="70%">{editbox name="name" text=$form.name}</td>
<td width="30%">&nbsp;&nbsp;{editbox name="urlname" width="80%" max=100 text=$form.urlname}.html</td>
</tr>
{if $options.usecats && $form.categories}
<tr>
<td width="70%">Категория:</td>
<td width="30%">&nbsp;&nbsp;{if $options.usedate}Дата:{/if}</td>
</tr>
<tr>
<td width="70%">{treeselect name="idcat" items=$form.categories selected=$form.idcat title="Выбор категории" width="60%"}</td>
<td width="30%">&nbsp;&nbsp;{if $options.usedate}{dateselect name="date" date=$form.date usetime=true}{/if}</td>
{/if}
</table>
<p>Текст:</p>
{if $options.fckeditor}
{fckeditor name="content" height=400 text=$form.content}
{else}
{textarea name="content" rows=6 text=$form.content}
{/if}
{if !$options.autoanons}
<p>Аннотация:</p>
<p>{textarea name="description" rows=3 text=$form.description}</p>
{/if}
{if $options.usetags}
<p>Теги (через запятую):</p>
<p>{tags text=$form.tags}</p>
{else}
{hidden name="tags" value=$form.tags}
{/if}
{if $options.useimages}
<p>Фото:</p>
{$form.imagesbox->getContent()}
{/if}
{if $options.usefiles}
<p>Прикрепленные файлы:</p>
{$form.filesbox->getContent()}
{/if}
{include file="objcomp_fieldseditor_include.tpl"}
{if !$options.usecats || !$form.categories}
{if $options.usedate}
<p>Дата:</p>
<p>{dateselect name="date" date=$form.date usetime=true}</p>
{/if}
{hidden name="idcat" value=0}
{/if}
{hidden name="id" value=$form.id}
{hidden name="tab" value="items"}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="authcode" value=$system.authcode}
{hidden name="action" value="edititem"}
<div align="right" style="margin-top:10px">
<p style="float:left">
<label><input type="checkbox" name="active"{if $form.active=="Y"} checked{/if}>&nbsp;Активно</label>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:applyitem(document.forms.edititemform)" title="Сохранить не закрывая"><img src="/templates/admin/images/save.gif" width="16" height="16" style="vertical-align:middle"></a>
<span id="applydate">{$form.mdate|date_format:"%d.%m.%Y %T"}</span>
</p>
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>