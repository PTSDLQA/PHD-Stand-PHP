<form name="editoptform" method="post" onsubmit="this.bsave.click()">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td>
{if $form.type=="bool"}
<input type="checkbox" name="value"{if $form.value==1} checked{/if}>
{elseif $form.type=="select"}
<select name="value">
{html_options options=$form.selectvars selected=$form.value}
</select>
{elseif $form.type=="date"}
{dateselect name="date" date=$form.value}
{else}
{editbox name="value" text=$form.value}
{/if}
</td>
<td width="150" align="center">
<input type="button" class="submit" name="bsave" value="Сохранить" onclick="saveopt({$form.id},{$form.idgroup},'{$form.type}')" style="width:70px">
<input type="button" class="button" value="Отмена" onclick="cancelopt({$form.id},{$form.idgroup})" style="width:70px">
</td>
</tr>
</table>
{hidden name="authcode" value=$system.authcode}
</form>