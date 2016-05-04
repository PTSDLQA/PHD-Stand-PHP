{include file="_header.tpl"}

{tabcontrol vot="Голосование" arch="Архив"}

{tabpage id="vot"}
<div class="box">
<form method="post" onsubmit="return voting_form(this)">
<table width="100%">
<tr>
<td width="110">Вопрос:</td>
<td colspan="2">{editbox name="question" text=$question}</td>
</td>
</tr>
<tr>
<td width="110">Дата начала:</td>
<td>{dateselect name="begin" date=$datebegin}</td>
<td align="right">
{if $status==1}
<img src="/templates/admin/images/ball_red.gif" title="Завершен" width="16" height="16" style="float:right">
{elseif $status==2}
<img src="/templates/admin/images/ball_yellow.gif" title="Ожидает включения" width="16" height="16" style="float:right">
{elseif $status==3}
<img src="/templates/admin/images/ball_green.gif" title="В процессе" width="16" height="16" style="float:right">
{/if}
Статус:&nbsp;
</td>
</tr>
<tr>
<td width="110">Дата окончания:</td>
<td>{dateselect name="end" date=$dateend maxtime=true}</td>
<td align="right">
<label><input type="checkbox" name="active"{if $active} checked{/if}>&nbsp;Включен</label>&nbsp;&nbsp;&nbsp;
{submit caption="Применить"}
{button caption="Новое голосование" onclick="newvoting()" width=140}
</td>
</tr>
</table>
{hidden name="tab" value="vot"}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="authcode" value=$system.authcode}
{hidden name="action" value="save"}
</form>
</div>
<h3>Варианты:</h3>
{if $variants}
<table class="grid gridsort">
<tr>
<th align="left">Название</th>
<th align="left" width="200">Текущий результат</th>
<th align="left" width="22">&nbsp;</th>
</tr>
</table>
<div id="variantsbox" class="gridsortbox">
{section name=i loop=$variants}
<table id="variant_{$variants[i].id}" class="grid gridsort">
<tr class="{cycle values="row0,row1"}">
<td><a href="javascript:geteditvariantform({$variants[i].id})" title="Редактировать">{$variants[i].name}</a></td>
<td width="200">
<img src="/modules/voting/images/{$smarty.section.i.index%6}.gif" height="10" width="{$variants[i].pr}">
<b>{$variants[i].pr}% ({$variants[i].count})</b>
</td>
<td width="20" align="center"><a href="javascript:delvariant({$variants[i].id})" title="Удалить"><img src="/templates/admin/images/del.gif"></a></td>
</tr>
</table>
{/section}
</div>
{literal}<script type="text/javascript">
Sortable.create('variantsbox',{tag:'table',onUpdate: setvariantsort});
</script>{/literal}
{else}
<div class="box">Нет вариантов.</div>
{/if}
<table class="actiongrid">
<tr>
<td align="right">
{button caption="Добавить" onclick="getaddvariantform()"}
</td>
</tr>
</table>
<div id="variantbox"></div>
{/tabpage}

{tabpage id="arch"}
{if $arch}
<table class="grid">
<tr>
<th align="left" width="140">Опрос</th>
<th align="left">Вопрос</th>
</tr>
{section name=i loop=$arch}
<tr class="{cycle values="row0,row1"}">
<td width="140">{$arch[i].date1|date_format:"%d.%m.%Y"}&nbsp;-&nbsp;{$arch[i].date2|date_format:"%d.%m.%Y"}</td>
<td><a href="javascript:getresult({$arch[i].id})" title="Результаты">{$arch[i].name}</a></td>
</tr>
{/section}
</table>
{object obj=$arch_pager}
{else}
<div class="box">Нет записей.</div>
{/if}
{/tabpage}

{include file="_footer.tpl"}
