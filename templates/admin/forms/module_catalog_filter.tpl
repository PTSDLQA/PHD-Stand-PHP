<form name="filterform" method="post">
<p>Название:</p>
<p>{editbox name="name" text=$form.name width="20%"}</p>
<p>Текст:</p>
<p>{editbox name="content" text=$form.content width="20%"}</p>
{include file="objcomp_fieldseditor_includefilter.tpl"}
<p>Дата:</p>
<p><input type="checkbox" name="date"{if $form.date} checked{/if}>
от &nbsp;{dateselect name="from" date=$form.from onchange="this.form.date.checked=true"}
&nbsp;
до &nbsp;{dateselect name="to" date=$form.to maxtime=true onchange="this.form.date.checked=true"}
</p>
<p>Статус:</p>
<p>
<select name="status">
{html_options options=$form.statuss selected=$form.status}
</select>
</p>
{hidden name="authcode" value=$system.authcode}
{hidden name="action" value="setfilter"}
{hidden name="tab" value="items"}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="idcat" value=$form.idcat}
<div align="right" style="margin-top:10px">
{submit caption="Применить"}
{button caption="Сбросить" onclick="this.form.elements.action.value='unfilter'; this.form.submit();"}
{button caption="Отмена" onclick="cancelfilter()"}
</div>
</form>