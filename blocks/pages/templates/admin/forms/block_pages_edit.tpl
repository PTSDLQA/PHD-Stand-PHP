<div{if count($form.sections)==1} style="display:none"{/if}>
<p>Раздел:</p>
<p>
<select name="b_idsec" onchange="pages_getdirs(this.value)">
{html_options options=$form.sections selected=$form.idsec}
</select>
</p>
</div>
{if $auth->isExpert()}
<p>Шаблон:</p>
<p>{editbox name="b_template" text=$form.template max=50 width="20%"}</p>
{else}
{hidden name="b_template" value=$form.template}
{/if}
<p><label><input type="checkbox" name="b_curcheck" onclick="categories_curcheck(this.checked)"{if $form.curcheck} checked{/if}>&nbsp;Текущий уровень</label></p>
<div{if count($form.dirs)==0} style="display:none"{/if}>
<div id="catselectbox"{if $form.curcheck} style="display:none"{/if}>
<p>Ссылки на страницы подраздела:</p>
<p>
<select name="b_idcat">
<option value="0">Корневой</option>
{html_options options=$form.dirs selected=$form.idcat}
</select>
</p>
</div>
</div>