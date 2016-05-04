{$site_name} - {$section_name}.
{foreach from=$fields item=field}
{if $field.type=="bool"}
<b>{$field.name}</b>:{if $field.value=="Y"}Да{else}Нет{/if}<br>
{elseif $field.type=="text"}
<b>{$field.name}</b>:<br>
{$field.value|escape|nl2br}<br>
{elseif $field.type!="file"}
<b>{$field.name}</b>: {$field.value|escape}<br>
{/if}
{/foreach}