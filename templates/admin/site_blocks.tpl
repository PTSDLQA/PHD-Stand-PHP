{include file="_header.tpl"}

<div>
{if $blocks}
<form name="blocksform" method="post">
<table class="grid gridsort" width="100%">
<tr>
<th width="25">&nbsp;</th>
<th width="20">&nbsp;</th>
<th align="left">Название</th>
<th align="left" width="20%">Базовый блок</th>
<th align="left" width="100">Идентификатор</th>
<th align="left" width="100">Позиция</th>
<th align="left" width="60">Создается</th>
<th align="left" width="22">&nbsp;</th>
<th align="left" width="25">&nbsp;</th>
</tr>
</table>
{if $leftblocks}
<div id="leftblocks" class="gridsortbox">
{section name=i loop=$leftblocks}
<table id="block_{$leftblocks[i].id}" class="grid gridsort" width="100%">
<tr class="{if $leftblocks[i].active=='Y'}{cycle values="row0,row1"}{else}close{/if}">
<td width="20"><input type="checkbox" id="check{$leftblocks[i].index}" name="checkblock[]" value="{$leftblocks[i].id}"></td>
<td width="20"><img src="/templates/admin/images/icons/block.gif" width="16" height="16"></td>
<td><a href="javascript:geteditblockform({$leftblocks[i].id})" title="Редактировать">{$leftblocks[i].caption}</a></td>
<td width="20%">{$leftblocks[i].block}</td>
<td width="100">{$leftblocks[i].name}</td>
<td width="100">{$leftblocks[i].align}</td>
<td width="60">{$leftblocks[i].show}</td>
<td width="20">{$leftblocks[i].tpl}</td>
<td width="20">{$leftblocks[i].del}</td>
</tr>
</table>
{/section}
</div>
{literal}<script type="text/javascript">
Sortable.create('leftblocks',{tag:'table',onUpdate: setblocksort});
</script>{/literal}
{/if}
{if $rightblocks}
<div id="rightblocks" class="gridsortbox">
{section name=i loop=$rightblocks}
<table id="block_{$rightblocks[i].id}" class="grid gridsort" width="100%">
<tr class="{if $rightblocks[i].active=='Y'}{cycle values="row0,row1"}{else}close{/if}">
<td width="20"><input type="checkbox" id="check{$rightblocks[i].index}" name="checkblock[]" value="{$rightblocks[i].id}"></td>
<td width="20"><img src="/templates/admin/images/icons/block.gif" width="16" height="16"></td>
<td><a href="javascript:geteditblockform({$rightblocks[i].id})" title="Редактировать">{$rightblocks[i].caption}</a></td>
<td width="20%">{$rightblocks[i].block}</td>
<td width="100">{$rightblocks[i].name}</td>
<td width="100">{$rightblocks[i].align}</td>
<td width="60">{$rightblocks[i].show}</td>
<td width="20">{$rightblocks[i].tpl}</td>
<td width="20">{$rightblocks[i].del}</td>
</tr>
</table>
{/section}
</div>
{literal}<script type="text/javascript">
Sortable.create('rightblocks',{tag:'table',onUpdate: setblocksort});
</script>{/literal}
{/if}
{if $freeblocks}
<div id="freeblocks" class="gridsortbox">
{section name=i loop=$freeblocks}
<table id="block_{$freeblocks[i].id}" class="grid gridsort" width="100%">
<tr class="{if $freeblocks[i].active=='Y'}{cycle values="row0,row1"}{else}close{/if}">
<td width="20"><input type="checkbox" id="check{$freeblocks[i].index}" name="checkblock[]" value="{$freeblocks[i].id}"></td>
<td width="20"><img src="/templates/admin/images/icons/block.gif" width="16" height="16"></td>
<td><a href="javascript:geteditblockform({$freeblocks[i].id})" title="Редактировать">{$freeblocks[i].caption}</a></td>
<td width="20%">{$freeblocks[i].block}</td>
<td width="100">{$freeblocks[i].name}</td>
<td width="100">{$freeblocks[i].align}</td>
<td width="60">{$freeblocks[i].show}</td>
<td width="20">{$freeblocks[i].tpl}</td>
<td width="20">{$freeblocks[i].del}</td>
</tr>
</table>
{/section}
</div>
{literal}<script type="text/javascript">
Sortable.create('freeblocks',{tag:'table',onUpdate: setblocksort});
</script>{/literal}
{/if}
{else}
<div class="box">Не созданы блоки.</div>
{/if}
<table class="actiongrid" width="100%">
<tr>
{if $blocks}
<td nowrap>
<label><input type="checkbox" onclick="checkall(this.checked)">&nbsp;
Отмеченные:</label>&nbsp;
<select name="action" onchange="runaction(this.value,this.form)">
<option value="">-</option>
<option value="setactive">Включить</option>
<option value="setunactive">Отключить</option>
<option value="delete">Удалить</option>
</select>
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="authcode" value=$system.authcode}
</td>
{/if}
<td align="right" width="60%">
{button caption="Добавить" onclick="getaddblockform()"}
</td>
</tr>
</table>
{if $blocks}</form>{/if}
</div>

{include file="_footer.tpl"}
