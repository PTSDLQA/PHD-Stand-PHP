{include file="_header.tpl"}

{if $options.usecats}
{if $options.usecomments}
{tabcontrol cat="Категории" albums="Альбомы" comm="Комментарии" opt="Настройки"}
{else}
{tabcontrol cat="Категории" albums="Альбомы" opt="Настройки"}
{/if}
{else}
{if $options.usecomments}
{tabcontrol albums="Альбомы" comm="Комментарии" opt="Настройки"}
{else}
{tabcontrol albums="Альбомы" opt="Настройки"}
{/if}
{/if}

{if $options.usecats}
{tabpage id="cat"}
{object obj=$treebox}
{/tabpage}
{/if}

{tabpage id="albums"}
{if $album}
{tabcontrol id="albums" allalb="Все" images=$album.name}
{tabpage idtab="albums" id="allalb"}
{if $options.usecats}
<div class="actionbox">
<form>{treeselect id="artidcat" name="idcat" items=$categories selected=$smarty.get.idcat emptytxt="Все категории" title="Выбор категории" onchange="runselectcat(this.form)" width="320px"}</form>
</div>
{/if}
<div id="itemsbox">
{if $items}
<form name="itemsform" method="post">
<table width="100%" class="gridfix">
<tr>
<th width="20">&nbsp;</th>
<th width="20">&nbsp;</th>
<th align="left">Название</th>
{if $options.usecats && (!$smarty.get.idcat || $childcats>1)}<th align="left">Категория</th>{/if}
<th width="100" align="left">Дата</th>
{if $sort=='sort'}<th width="40" align="left">Сорт.</th>{/if}
{if $options.usevote}<th width="60" align="left">Оценка</th>{/if}
<th width="25">&nbsp;</th>
<th width="25">&nbsp;</th>
{if $seo}<th width="25">&nbsp;</th>{/if}
{if $options.usecomments}<th width="25">&nbsp;</th>{/if}
<th width="25">&nbsp;</th>
</tr>
{section name=i loop=$items}
<tr class="{if $items[i].active=="Y"}{cycle values="row0,row1"}{else}close{/if}">
<td width="20"><input id="check{$smarty.section.i.index}" type="checkbox" name="checkitem[]" value="{$items[i].id}"><input type="hidden" name="edititem[]" value="{$items[i].id}"></td>
<td width="20">{if $items[i].idimg}{capture name=gimage}{image id=$items[i].idimg width=150}{/capture}<img src="/templates/admin/images/image.gif" width="16" height="16" {popup text=$smarty.capture.gimage fgcolor="#F3FCFF" width=150 bgcolor="#86BECD" left=true}>{else}&nbsp;{/if}</td>
<td><a href="admin.php?mode=sections&item={$system.item}{if $smarty.get.idcat}&idcat={$smarty.get.idcat}{/if}&tab=albums&tab_albums=images&idalb={$items[i].id}{if $smarty.get.page}&page={$smarty.get.page}{/if}">{if $items[i].id==$smarty.get.idalb}<b>{$items[i].name}</b>{else}{$items[i].name}{/if}</a> ({$items[i].cimages})</td>
{if $options.usecats && (!$smarty.get.idcat || $childcats>1)}<td class="gray" nowrap><span{if $items[i].catpath!=$items[i].category} {help text=$items[i].catpath}{/if}>{$items[i].category}</span></td>{/if}
<td width="100" nowrap>{$items[i].date|date_format:"%D %T"}</td>
{if $sort=='sort'}<td width="40"><input type="text" name="sort_{$items[i].id}" size="4" value="{$items[i].sort}" style="width:40px"></td>{/if}
{if $options.usevote}<td width="60"><input type="text" name="vote_{$items[i].id}" size="4" value="{$items[i].vote}" style="width:40px"></td>{/if}
<td width="25" align="center"><a href="javascript:getedititemform({$items[i].id})" title="Редактировать"><img src="/templates/admin/images/edit.gif" width="16" height="16" alt="Редактировать"></a></td>
<td width="25" align="center"><a href="{$items[i].link}" target="_blank" title="Просмотр на сайте"><img src="/templates/admin/images/browse.gif" width="16" height="16" alt="Просмотр на сайте"></a></td>
{if $seo}<td width="25" align="center"><a href="javascript:geturlseoform('{$items[i].link}')" title="SEO оптимизация"><img src="/templates/admin/images/addpage.gif" width="16" height="16"></a></td>{/if}
{if $options.usecomments}<td width="25"><a href="admin.php?mode=sections&item={$system.item}{if $smarty.get.idcat}&idcat={$smarty.get.idcat}{/if}&tab=comm&iditemcomm={$items[i].id}{if $smarty.get.page}&page={$smarty.get.page}{/if}"><img src="/templates/admin/images/comments.gif" width="16" height="16" alt="Комментарии ({$items[i].comments})"></a></td>{/if}
<td width="25" align="center"><a href="javascript:delitem({$items[i].id})" title="Удалить"><img src="/templates/admin/images/del.gif" width="16" height="16" alt="Удалить"></a></td>
</tr>
{/section}
</table>
{object obj=$items_pager}
{else}
<div class="box">Нет альбомов.</div>
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
{hidden name="tab" value="albums"}
{hidden name="tab_albums" value=""}
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
{hidden name="tab" value="albums"}
{hidden name="tab_albums" value=""}
{hidden name="authcode" value=$system.authcode}
</form>
</td>
<td nowrap>
<form name="rowsform" method="post">
На странице:
<select name="rows" onchange="this.form.submit()">
<option value="20">20</option>
<option value="50">50</option>
<option value="100">100</option>
<option value="0">Все</option>
</select>
<script type="text/javascript">document.forms.rowsform.rows.value='{$rows}';</script>
{hidden name="action" value="setrows"}
{hidden name="authcode" value=$system.authcode}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="tab" value="albums"}
{hidden name="tab_albums" value=""}
</form>
</td>
{/if}
<td width="60%" align="right">
{if $sort=='sort' || $options.usevote}
{button caption="Сохранить" onclick="document.forms.itemsform.submit()"}
{/if}
{button caption="Добавить" onclick="getadditemform()"}
</td>
</tr>
</table>
</div>
{/tabpage}
{tabpage idtab="albums" id="images"}
<form name="imagesform" method="post">

{if $images}
<div id="albimagesbox">
{section name=i loop=$images}
<table id="image_{$images[i].id}" width="200" style="float:left; background: #FCFCFC; border: 1px solid #ddd; margin: 5px;" cellpadding="0" cellspacing="0">
<tr {if $album.idimg==$images[i].id} bgcolor="#ccffcc"{/if}>
<td width="20" style="padding:3px"><input id="icheck{$smarty.section.i.index}" type="checkbox" name="checkimg[]" value="{$images[i].id}"><input type="hidden" name="editimg[]" value="{$images[i].id}"></td>
<td style="padding:3px"><input type="text" name="icaption_{$images[i].id}" value="{$images[i].caption}" style="width:100%"></td>
</tr>
<tr {if $album.idimg==$images[i].id} bgcolor="#ccffcc"{/if}>
<td colspan="2" align="center" style="padding:5px">
{image id=$images[i].id height=120 popup=true}
</td>
</tr>
</table>
{/section}
</div>
{literal}<script type="text/javascript">
Sortable.create('albimagesbox',{tag:'table',constraint:'horizontal',onUpdate: setimagesort});
</script>{/literal}
{else}
<div class="box">Нет фото.</div>
{/if}
<div class="clear"></div>
<table width="100%" class="actiongrid">
<tr>
{if $images}
<td nowrap>
<label><input type="checkbox" onclick="checkall2(this.checked)">&nbsp;
Отмеченные:</label>&nbsp;
<select name="action" onchange="runaction2(this.value,this.form)">
<option value="isave">-</option>
<option value="isetmain">Главное фото в альбоме</option>
<option value="idelete">Удалить</option>
</select>
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="tab" value="albums"}
{hidden name="tab_albums" value="images"}
{hidden name="authcode" value=$system.authcode}
</td>
{/if}
<td align="right">
{if $images}
{button caption="Сохранить" onclick="document.forms.imagesform.submit()"}
{/if}
{button caption="Добавить" onclick="getaddimageform()"}
</td>
</tr>
</table>
</form>
{/tabpage}
{else}
{if $options.usecats}
<div class="actionbox">
<form>{treeselect id="artidcat" name="idcat" items=$categories selected=$smarty.get.idcat emptytxt="Все категории" title="Выбор категории" onchange="runselectcat(this.form)" width="320px"}</form>
</div>
{/if}
<div id="itemsbox">
{if $items}
<form name="itemsform" method="post">
<table width="100%" class="gridfix">
<tr>
<th width="20">&nbsp;</th>
<th width="20">&nbsp;</th>
<th align="left">Название</th>
{if $options.usecats && (!$smarty.get.idcat || $childcats>1)}<th align="left">Категория</th>{/if}
<th width="100" align="left">Дата</th>
{if $sort=='sort'}<th width="40" align="left">Сорт.</th>{/if}
{if $options.usevote}<th width="60" align="left">Оценка</th>{/if}
<th width="25">&nbsp;</th>
<th width="25">&nbsp;</th>
{if $seo}<th width="25">&nbsp;</th>{/if}
{if $options.usecomments}<th width="25">&nbsp;</th>{/if}
<th width="25">&nbsp;</th>
</tr>
{section name=i loop=$items}
<tr class="{if $items[i].active=="Y"}{cycle values="row0,row1"}{else}close{/if}">
<td width="20"><input id="check{$smarty.section.i.index}" type="checkbox" name="checkitem[]" value="{$items[i].id}"><input type="hidden" name="edititem[]" value="{$items[i].id}"></td>
<td width="20">{if $items[i].idimg}{capture name=gimage}{image id=$items[i].idimg width=150}{/capture}<img src="/templates/admin/images/image.gif" width="16" height="16" {popup text=$smarty.capture.gimage fgcolor="#F3FCFF" width=150 bgcolor="#86BECD" left=true}>{else}&nbsp;{/if}</td>
<td><a href="admin.php?mode=sections&item={$system.item}{if $smarty.get.idcat}&idcat={$smarty.get.idcat}{/if}&tab=albums&tab_albums=images&idalb={$items[i].id}{if $smarty.get.page}&page={$smarty.get.page}{/if}">{if $items[i].id==$smarty.get.idalb}<b>{$items[i].name}</b>{else}{$items[i].name}{/if}</a> ({$items[i].cimages})</td>
{if $options.usecats && (!$smarty.get.idcat || $childcats>1)}<td class="gray" nowrap><span{if $items[i].catpath!=$items[i].category} {help text=$items[i].catpath}{/if}>{$items[i].category}</span></td>{/if}
<td width="100" nowrap>{$items[i].date|date_format:"%D %T"}</td>
{if $sort=='sort'}<td width="40"><input type="text" name="sort_{$items[i].id}" size="4" value="{$items[i].sort}" style="width:40px"></td>{/if}
{if $options.usevote}<td width="60"><input type="text" name="vote_{$items[i].id}" size="4" value="{$items[i].vote}" style="width:40px"></td>{/if}
<td width="25" align="center"><a href="javascript:getedititemform({$items[i].id})" title="Редактировать"><img src="/templates/admin/images/edit.gif" width="16" height="16" alt="Редактировать"></a></td>
<td width="25" align="center"><a href="{$items[i].link}" target="_blank" title="Просмотр на сайте"><img src="/templates/admin/images/browse.gif" width="16" height="16" alt="Просмотр на сайте"></a></td>
{if $seo}<td width="25" align="center"><a href="javascript:geturlseoform('{$items[i].link}')" title="SEO оптимизация"><img src="/templates/admin/images/addpage.gif" width="16" height="16"></a></td>{/if}
{if $options.usecomments}<td width="25"><a href="admin.php?mode=sections&item={$system.item}{if $smarty.get.idcat}&idcat={$smarty.get.idcat}{/if}&tab=comm&iditemcomm={$items[i].id}{if $smarty.get.page}&page={$smarty.get.page}{/if}"><img src="/templates/admin/images/comments.gif" width="16" height="16" alt="Комментарии ({$items[i].comments})"></a></td>{/if}
<td width="25" align="center"><a href="javascript:delitem({$items[i].id})" title="Удалить"><img src="/templates/admin/images/del.gif" width="16" height="16" alt="Удалить"></a></td>
</tr>
{/section}
</table>
{object obj=$items_pager}
{else}
<div class="box">Нет альбомов.</div>
{/if}
<table width="100%" class="actiongrid">
<tr>
{if $items}
<td nowrap>
<label><input type="checkbox" onclick="checkall(this.checked)">&nbsp;
Отмеченные:</label>&nbsp;
<select name="action" onchange="runaction(this.value,this.form)">
<option value="save">-</option>
<option value="setactive">Разместить</option>
<option value="setunactive">Отключить</option>
{if $options.usecats}<option value="move">Переместить</option>{/if}
<option value="delete">Удалить</option>
</select>
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="tab" value="albums"}
{hidden name="tab_albums" value=""}
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
{hidden name="tab" value="albums"}
{hidden name="tab_albums" value=""}
{hidden name="authcode" value=$system.authcode}
</form>
</td>
<td nowrap>
<form name="rowsform" method="post">
На странице:
<select name="rows" onchange="this.form.submit()">
<option value="20">20</option>
<option value="50">50</option>
<option value="100">100</option>
<option value="0">Все</option>
</select>
<script type="text/javascript">document.forms.rowsform.rows.value='{$rows}';</script>
{hidden name="action" value="setrows"}
{hidden name="authcode" value=$system.authcode}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="tab" value="albums"}
{hidden name="tab_albums" value=""}
</form>
</td>
{/if}
<td width="60%" align="right">
{if $sort=='sort' || $options.usevote}
{button caption="Сохранить" onclick="document.forms.itemsform.submit()"}
{/if}
{button caption="Добавить" onclick="getadditemform()"}
</td>
</tr>
</table>
</div>
{/if}
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
