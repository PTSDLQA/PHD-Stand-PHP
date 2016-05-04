{include file="_header.tpl"}

{if $options.usecats}
{if $options.usecomments}
{tabcontrol cat="Категории" items="Записи" comm="Комментарии" opt="Настройки"}
{else}
{tabcontrol cat="Категории" items="Записи" opt="Настройки"}
{/if}
{else}
{if $options.usecomments}
{tabcontrol items="Записи" comm="Комментарии" opt="Настройки"}
{else}
{tabcontrol items="Записи" opt="Настройки"}
{/if}
{/if}

{if $options.usecats}
{tabpage id="cat"}
{object obj=$treebox}
{/tabpage}
{/if}

{tabpage id="items"}
{if $options.usecats}
<div class="actionbox">
<form>{treeselect id="artidcat" name="idcat" items=$categories selected=$smarty.get.idcat emptytxt="Все категории" title="Выбор категории" onchange="runselectcat(this.form)" width="320px"}</form>
</div>
{/if}
<div id="itemsbox">
{if $items}
<form name="itemsform" method="post">
<table width="100%" class="gridfix">
{section name=i loop=$items}
<tr class="{if $items[i].active=="Y"}row0{else}close{/if}">
<td width="20"{if $smarty.section.i.first} class="first"{/if}><input id="check{$smarty.section.i.index}" type="checkbox" name="checkitem[]" value="{$items[i].id}"><input type="hidden" name="edititem[]" value="{$items[i].id}"></td>
<td{if $smarty.section.i.first} class="first"{/if}><a href="javascript:getedititemform({$items[i].id})" title="Редактировать"><b>{$items[i].name}</b></a></td>
{if $options.usecats && (!$smarty.get.idcat || $childcats>1)}<td align="right" nowrap class="gray{if $smarty.section.i.first} first{/if}"><span{if $items[i].catpath!=$items[i].category} {help text=$items[i].catpath}{/if}>{$items[i].category}</span></td>{/if}
<td width="120" align="right" nowrap{if $smarty.section.i.first} class="first"{/if}>{$items[i].date|date_format:"%D %T"}</td>
{if $sort=='sort'}<td width="40"{if $smarty.section.i.first} class="first"{/if}><input type="text" name="sort_{$items[i].id}" size="4" value="{$items[i].sort}" style="width:35px"></td>{/if}
{if $options.usevote}<td width="60"{if $smarty.section.i.first} class="first"{/if}><img src="/templates/admin/images/star.gif" width="16" height="16" alt="Оценка" style="vertical-align:middle">&nbsp;<input type="text" name="vote_{$items[i].id}" size="2" value="{$items[i].vote}" style="width:35px"></td>{/if}
<td width="25" align="center"{if $smarty.section.i.first} class="first"{/if}><a href="{$items[i].link}" target="_blank" title="Просмотр на сайте"><img src="/templates/admin/images/browse.gif" width="16" height="16" alt="Просмотр на сайте"></a></td>
{if $seo}<td width="25" align="center"{if $smarty.section.i.first} class="first"{/if}><a href="javascript:geturlseoform('{$items[i].link}')" title="SEO оптимизация"><img src="/templates/admin/images/addpage.gif" width="16" height="16"></a></td>{/if}
{if $options.usecomments}<td width="25"{if $smarty.section.i.first} class="first"{/if}><a href="admin.php?mode=sections&item={$system.item}{if $smarty.get.idcat}&idcat={$smarty.get.idcat}{/if}&tab=comm&iditemcomm={$items[i].id}{if $smarty.get.page}&page={$smarty.get.page}{/if}" title="Комментарии ({$items[i].comments})"><img src="/templates/admin/images/comments.gif" width="16" height="16" alt="Комментарии ({$items[i].comments})"></a></td>{/if}
<td width="25" align="center"{if $smarty.section.i.first} class="first"{/if}><a href="javascript:delitem({$items[i].id})" title="Удалить"><img src="/templates/admin/images/del.gif" width="16" height="16" alt="Удалить"></a></td>
</tr>
<tr>
{assign var="colspan" value=5}
{if $options.usecats && (!$smarty.get.idcat || $childcats>1)}{assign var="colspan" value=$colspan+1}{/if}
{if $seo}{assign var="colspan" value=$colspan+1}{/if}
{if $sort=='sort'}{assign var="colspan" value=$colspan+1}{/if}
{if $options.usevote}{assign var="colspan" value=$colspan+1}{/if}
{if $options.usecomments}{assign var="colspan" value=$colspan+1}{/if}
<td colspan="{$colspan}">
{if $options.useimages}{image id=$items[i].idimg style="float:left;margin-right:5px;" width=80 height=50 popup=true}{/if}
{$items[i].description}
{if $items[i].tags || ($options.usefiles && $items[i].idfile>0)}
<div class="clear"></div>
<div class="box">
<div class="clear"></div>
<div style="float:left">
{section name=j loop=$items[i].tags}
<a href="{$items[i].tags[j].link}">{$items[i].tags[j].name}</a>{if !$smarty.section.j.last}, {/if}
{/section}
</div>
<div style="float:right">
{if $options.usefiles}{download data=$items[i].files title="Скачать" size=true dwnl=true max=3}{/if}
</div>
<div class="clear"></div>
</div>
{/if}
</td>
</tr>
{/section}
</table>
{object obj=$items_pager}
{else}
<div class="box">Нет данных.</div>
{/if}
<table width="100%" class="actiongrid">
<tr>
{if $items}
<td nowrap>
<label><input type="checkbox" onclick="checkall(this.checked)">&nbsp;
Отмеченные:</label>&nbsp;
<select name="action" onchange="runaction(this.value,this.form)">
<option value="save">-</option>
<option value="setactive">Включить</option>
<option value="setunactive">Отключить</option>
{if $options.usecats}<option value="move">Переместить</option>{/if}
<option value="delete">Удалить</option>
</select>
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="tab" value="items"}
{hidden name="authcode" value=$system.authcode}
</form>
</td>
<td nowrap>
<form name="sortform" method="post">
Сортировать по:
<select name="sort" onchange="this.form.submit()">
<option value="date DESC">дате вверх</option>
<option value="date">дате вниз</option>
<option value="name">названию вниз</option>
<option value="name DESC">названию вверх</option>
{if $options.usevote}
<option value="vote">оценке вниз</option>
<option value="vote DESC">оценке вверх</option>
{/if}
{if $options.usecoments}
<option value="comments">комментариям вниз</option>
<option value="comments DESC">комментариям вверх</option>
{/if}
<option value="sort">заданному порядку</option>
</select>
<script type="text/javascript">document.forms.sortform.sort.value='{$sort}';</script>
{hidden name="action" value="setsort"}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="tab" value="items"}
{hidden name="authcode" value=$system.authcode}
</form>
</td>
<td nowrap>
<form name="rowsform" method="post">
На странице:
<select name="rows" onchange="this.form.submit()">
<option value="10">10</option>
<option value="20">20</option>
<option value="50">50</option>
<option value="0">Все</option>
</select>
<script type="text/javascript">document.forms.rowsform.rows.value='{$rows}';</script>
{hidden name="action" value="setrows"}
{hidden name="authcode" value=$system.authcode}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="tab" value="items"}
</form>
</td>
{/if}
<td width="60%" align="right">
{button caption="Фильтр" onclick="getfilterform()"}
{if $sort=='sort' || $options.usevote}
{button caption="Сохранить" onclick="document.forms.itemsform.submit()"}
{/if}
{button caption="Добавить" onclick="getadditemform()"}
</td>
</tr>
</table>
</div>
<div id="filterbox"></div>
{if $filter}<script type="text/javascript">getfilterform()</script>{/if}
{/tabpage}

{if $options.usecomments}
{tabpage id="comm"}
{object obj=$commbox}
{/tabpage}
{/if}

{tabpage id="opt"}
{tabcontrol id="opt" opt2="Опции" fields="Редактор&nbsp;полей"}
{tabpage idtab="opt" id="opt2"}
{object obj=$optbox1}
{object obj=$optbox2}
{object obj=$optbox3}
{object obj=$optbox4}
{/tabpage}
{tabpage idtab="opt" id="fields"}
{object obj=$fieldsbox}
{/tabpage}
{/tabpage}

{include file="_footer.tpl"}
