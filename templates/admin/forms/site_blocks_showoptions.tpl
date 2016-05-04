<p><input type="checkbox" name="check_showall" value="Y"{if $form.showall=='Y'} checked{/if} onclick="showallcheck(this.checked)">&nbsp;Создается всегда</p>
<div id="showoptions2"{if $form.showall=='Y'} style="display:none"{/if}>
{if $form.sections}
<table width="100%" class="grid">
<tr>
<th align="left" width="100" style="font-size:11">Раздел</th>
<th align="left" style="font-size:11">Страницы</th>
</tr>
{section name=i loop=$form.sections}
<tr class="{cycle values="row0,row1"}">
<td width="100" nowrap>{$form.sections[i].caption}</td>
<td style="font-size:10">
{section name=j loop=$form.sections[i].pages}
<input type="checkbox" name="showcheck[]" value="{$form.sections[i].id}_{$form.sections[i].pages[j].page}"{if $form.sections[i].pages[j].checked} checked{/if}>&nbsp;{$form.sections[i].pages[j].caption}
{if !$smarty.section.j.last}, {/if}
{sectionelse}
<input type="checkbox" name="showcheck[]" value="{$form.sections[i].id}"{if $form.sections[i].checked} checked{/if}>
{/section}
</td>
</tr>
{/section}
</table>
{/if}
</div>