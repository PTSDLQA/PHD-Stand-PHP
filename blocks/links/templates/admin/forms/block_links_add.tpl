<p>Шаблон:</p>
<p>{editbox name="b_template" text="links.tpl" max=50 width="20%"}</p>
<p>Cсылки:</p>
<div class="box">
<table id="lheader" class="grid"{if !$form.links} style="display:none"{/if}>
<tr>
<th align="left" width="25" nowrap style="font-size:10px">ур<sup style="font-size:10px">2</sup></th>
<th align="left" width="20%" style="font-size:11px">Выбрать</th>
<th align="left" width="30%" style="font-size:11px">Название</th>
<th align="left" style="font-size:11px">Ссылка</th>
<th align="left" width="60" style="font-size:11px">ID</th>
<th align="left" width="20" style="font-size:11px">&nbsp;</th>
<th align="left" width="20" style="font-size:11px">&nbsp;</th>
</tr>
</table>
<div id="b_links">
{section name=i loop=$form.links}
<div id="link{$smarty.section.i.index}">
<table width="100%">
<tr>
<td width="25"><input type="checkbox" id="b_sub{$smarty.section.i.index}" name="b_sub[]" value="{$smarty.section.i.index}"{if $form.links[i].sub} checked{/if}></td>
<td width="20%">
<select name="b_slink{$smarty.section.i.index}" onchange="links_select({$smarty.section.i.index},this.value)" style="width:100%">
<option value="0">---</option>
{html_options options=$form.alllinks selected=$form.links[i].link}
</select>
</td>
<td width="30%"><input type="text" name="b_caption{$smarty.section.i.index}" maxlength="50" style="width:100%" value="{$form.links[i].name}"></td>
<td><input type="text" name="b_link{$smarty.section.i.index}" maxlength="100" style="width:100%" value="{$form.links[i].link}"></td>
<td width="60">
<input type="text" name="b_id{$smarty.section.i.index}" maxlength="20" style="width:100%" value="{$form.links[i].id}">
<input type="hidden" name="b_section{$smarty.section.i.index}" value="{$form.links[i].section}"></td>
</td>
<td width="20"><a href="javascript:links_insert({$smarty.section.i.index})" title="Вставить"><img src="/templates/admin/images/add.gif" width="16" height="16"></a></td>
<td width="20"><a href="javascript:links_del({$smarty.section.i.index})" title="Удалить"><img src="/templates/admin/images/del.gif" width="16" height="16"></a></td>
</tr>
</table>
</div>
{/section}
</div>
<table width="100%">
<tr>
<td>Импорт (xls):&nbsp;<input type="file" name="xlsfile"></td>
<td width="20"><a href="javascript:links_addlink()" title="Добавить"><img src="/templates/admin/images/add.gif" width="16" height="16" alt="Добавить"></a></td>
<td width="20">&nbsp;</td>
</tr>
</table>
</div>
{hidden name="b_count" value="5"}
