{include file="_header.tpl"}

{if $errors.invalidfile}
<div class="warning">Не удалось установить файл расширения, возможно неверный формат файла!</div>
{/if}
{if $errors.invalidsystem}
<div class="warning">Не удалось установить файл расширения, необходима версия системы {$errors.invalidsystem|regex_replace:"/([0-9][0-9])$/":".\\1"} или выше!</div>
{/if}
{if $errors.err_modules}
<div class="warning">Не удалось удалить модуль, т.к. существуют разделы, использующие его.</div>
{/if}
{if $errors.err_blocks}
<div class="warning">Не удалось удалить тип, т.к. существуют блоки, использующие его.</div>
{/if}

{tabcontrol module="&nbsp;Модули&nbsp;" plugin="&nbsp;Плагины&nbsp;" block="&nbsp;Блоки&nbsp;"}

{tabpage id="module"}
<table class="grid gridsort">
<tr>
<th width="23">&nbsp;</th>
<th align="left" width="152">Идентификатор</th>
<th align="left">Название</th>
<th align="left" width="50">Версия</th>
<th width="23">&nbsp;</th>
<th width="22">&nbsp;</th>
<th width="22">&nbsp;</th>
</tr>
</table>
<div id="modulesbox" class="gridsortbox">
{section name=i loop=$modules}
<table id="module_{$modules[i].id}" class="grid gridsort">
<tr class="{cycle values="row0,row1"}">
<td width="20">{$modules[i].ico}</td>
<td width="150">{$modules[i].name}</td>
<td>{$modules[i].caption}</td>
<td width="50">{$modules[i].version|regex_replace:"/([0-9][0-9])$/":".\\1"}</td>
<td width="20" align="center">{if $modules[i].usehelp}<a href="http://a-cms.ru/manuals/extensions/modules/{$modules[i].name}" target="_blank" title="Справка"><img width="16" height="16" src="/templates/admin/images/help.gif" alt="Справка"></a>{else}&nbsp;{/if}</td>
<td width="20" align="center"><a href="javascript:extexport({$modules[i].id})" title="Экспорт"><img width="16" height="16" src="/templates/admin/images/save.gif" alt="Экспорт"></a></td>
<td width="20" align="center"><a href="javascript:delextension({$modules[i].id})" title="Удалить"><img src="/templates/admin/images/del.gif" width="16" height="16" alt="Удалить"></a></td>
</tr>
</table>
{/section}
</div>
{literal}<script type="text/javascript">
Sortable.create('modulesbox',{tag:'table',onUpdate: setextensionsort});
</script>{/literal}
{/tabpage}

{tabpage id="plugin"}
<div class="warning">
<p style="color:red">Использование плагинов доступно только в полной версии.</p>
</div>
{/tabpage}

{tabpage id="block"}
<table class="grid gridsort">
<tr>
<th width="23">&nbsp;</th>
<th align="left" width="152">Идентификатор</th>
<th align="left">Название</th>
<th align="left" width="50">Версия</th>
<th width="23">&nbsp;</th>
<th width="22">&nbsp;</th>
<th width="22">&nbsp;</th>
</tr>
</table>
<div id="blocksbox" class="gridsortbox">
{section name=i loop=$blocks}
<table id="block_{$blocks[i].id}" class="grid gridsort">
<tr class="{cycle values="row0,row1"}">
<td width="20"><img src="/templates/admin/images/icons/block.gif" width="16" height="16"></td>
<td width="150">{$blocks[i].name}</td>
<td>{$blocks[i].caption}</td>
<td width="50">{$blocks[i].version|regex_replace:"/([0-9][0-9])$/":".\\1"}</td>
<td width="20" align="center">{if $blocks[i].usehelp}<a href="http://a-cms.ru/manuals/extensions/blocks/{$blocks[i].name}" target="_blank" title="Справка"><img width="16" height="16" src="/templates/admin/images/help.gif" alt="Справка"></a>{else}&nbsp;{/if}</td>
<td width="20" align="center"><a href="javascript:extexport({$blocks[i].id})" title="Экспорт"><img width="16" height="16" src="/templates/admin/images/save.gif" alt="Экспорт"></a></td>
<td width="20" align="center"><a href="javascript:delextension({$blocks[i].id})" title="Удалить"><img src="/templates/admin/images/del.gif" width="16" height="16" alt="Удалить"></a></td>
</tr>
</table>
{/section}
</div>
{literal}<script type="text/javascript">
Sortable.create('blocksbox',{tag:'table',onUpdate: setextensionsort});
</script>{/literal}
{/tabpage}

<table class="actiongrid">
<tr>
<td>
<a href="admin.php?mode=system&item=extensions&authcode={$system.authcode}&action=update" title="Искать новые на диске"><img src="/templates/admin/images/browse2.gif" width="16" height="16"></a>
</td>
<td align="right">
{if $usezip}
{button caption="Экспорт" onclick="document.location='admin.php?mode=system&item=extensions&authcode='+AUTHCODE+'&action=exportall&type='+tabs_id;"}
{/if}
{button caption="Импорт" onclick="getuploadform()"}
</td>
</tr>
</table>

{include file="_footer.tpl"}
