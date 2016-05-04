{include file="_header.tpl"}

{if $pages}
<table width="100%" class="actiongrid">
<tr>
<td nowrap>
<form method="get">
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
Разделы:&nbsp;
<select name="idsec" onchange="this.form.submit()">
<option value="0">Все</option>
{html_options options=$sections selected=$smarty.get.idsec}
</select>
{hidden name="authcode" value=$system.authcode}
</form>
</td>
</tr>
</table>
<div id="pagesbox">
<table class="grid" width="100%">
<tr>
<th width="20">&nbsp;</th>
<th align="left">Раздел - Страница</th>
<th align="left" width="220">Шаблон</th>
</tr>
{section name=i loop=$pages}
<tr class="{cycle values="row0,row1"}">
<td width="20"><img src="/templates/admin/images/template.gif" width="16" height="16"></td>
<td><a href="javascript:geteditpageform({$pages[i].id})" title="Редактировать">{$pages[i].section} - {$pages[i].caption}</a></td>
<td width="220"><a href="javascript:edittpl('{$pages[i].template}')" title="Редактировать">{$pages[i].template}</a></td>
</tr>
{/section}
</table>
</div>
{else}
<div class="box">Нет данных.</div>
{/if}

{include file="_footer.tpl"}