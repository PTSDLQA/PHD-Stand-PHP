{include file="_header.tpl"}

{tabcontrol search="Поиск" opt="Настройки"}

{tabpage id="search"}
<table class="actiongrid">
<tr>
<td>
<form method="get">
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{editbox name="query" width="200" text=$smarty.get.query}
<select name="idsec">
<option value="0">Все разделы</option>
{html_options options=$sections selected=$smarty.get.idsec}
</select>
{submit caption="Искать"}
</form>
</td>
<td width="170" align="right">Доступно материалов: <b>{$indexall}</b></td>
{if $indexdate}
<td width="210" align="right">Дата обновления: <b>{$indexdate|date_format:"%d.%m.%Y %T"}</b></td>
{/if}
<td width="125" align="right">{button caption="Проиндексировать" onclick="getindexform()"}</td>
</tr>
</table>

{if $tags}
<div class="box">
{section name=i loop=$tags}
{if $tags[i].tag==$smarty.get.tag}
<b style="font-size:{$tags[i].cluster*10+100}%">{$tags[i].tag}({$tags[i].count})</b> 
{else}
<a href="{$tags[i].link}" style="font-size:{$tags[i].cluster*10+100}%">{$tags[i].tag}({$tags[i].count})</a> 
{/if}
{/section}
</div>
{/if}

{if $items}
<table width="100%" class="gridfix">
{section name=i loop=$items}
<tr class="row0">
<td>
<b>{$items[i].num}.</b>
<a href="{$items[i].link}" target="_blank">{$items[i].name}</a></td>
</tr>
<tr class="row1">
<td>
{image id=$items[i].idimg style="float:left;margin-right:5px;" width=80 height=50 popup=true}
{$items[i].description}
{if $items[i].tags}
<div class="clear"></div>
<div class="box" align="left">
{section name=j loop=$items[i].tags}
<a href="{$items[i].tags[j].link}">{$items[i].tags[j].name}</a>{if !$smarty.section.j.last}, {/if}
{/section}
</div>
{/if}
</td>
</tr>
{/section}
</table>
{object obj=$items_pager}
{/if}
{/tabpage}

{tabpage id="opt"}
{object obj=$optbox}
{/tabpage}

{include file="_footer.tpl"} 
