<div>
<h3>{$form.title}</h3>
<table class="grid gridsort">
<tr>
<th align="left" width="20">&nbsp;</th>
<th align="left" width="25">&nbsp;</th>
<th align="left">Название</th>
{if $auth->isExpert()}<th align="left" width="120">Шаблон</th>{/if}
<th align="left" width="100">Дата</th>
<th align="left" width="25"></th>
<th align="left" width="25"></th>
{if $form.seo}<th align="left" width="27"></th>{/if}
<th align="left" width="28"></th>
<th align="left" width="30"></th>
</tr>
</table>
{if !$form.sub}<div id="pagesgridbox" class="gridsortbox">{/if}
{section name=i loop=$form.pages}
{if $form.sub && $smarty.section.i.iteration==2}<div id="pagesgridbox" class="gridsortbox">{/if}
<table id="pd_{$form.pages[i].id}" class="grid gridsort">
<tr class="{if !$form.pages[i].active || $form.pages[i].active=='Y'}{cycle values="row0,row1"}{else}close{/if}">
<td width="20">{$form.pages[i].0}</td>
<td width="20">{$form.pages[i].1}</td>
<td>{$form.pages[i].2}</td>
{if $auth->isExpert()}<td width="120">{$form.pages[i].3}</td>{/if}
<td width="100">{if $form.pages[i].4}{$form.pages[i].4|date_format:"%D %T"}{else}&nbsp;{/if}</td>
<td width="25" align="center">{$form.pages[i].5}</td>
<td width="25" align="center">{$form.pages[i].6}</td>
{if $form.seo && $form.pages[i].6!='&nbsp;'}<td width="25" align="center"><a href="javascript:geturlseoform('{$form.pages[i].link}')" title="SEO оптимизация"><img src="/templates/admin/images/addpage.gif" width="16" height="16"></a></td>{/if}
<td width="25" align="center">{$form.pages[i].7}</td>
<td width="25" align="center">{$form.pages[i].8}</td>
</tr>
</table>
{/section}
</div>
{object obj=$form.pager}
</div>