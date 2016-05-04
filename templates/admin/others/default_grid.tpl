{if $title}<h3>{$title}</h3>{/if}
{if $rows>0}
{section name=row loop=$cells}
{if $smarty.section.row.first}
<table class="gridfix" width="100%">
<tr>
{section name=col loop=$headers}
<th align="left" width="{$width[col]}">{$headers[col]}</th>
{/section}
</tr>
{/if}
{section name=col loop=$cells[row]}
{if $smarty.section.col.first}
<tr class="{if $class[row]}{$class[row]}{else}{cycle values="row0,row1"}{/if}">
{/if}
<td align="{$align[col]}" width="{$width[col]}">{$cells[row][col]}</td>
{if $smarty.section.col.last}
</tr>
{/if}
{/section}
{if $smarty.section.row.last}
</table>
{/if}
{/section}
{/if}