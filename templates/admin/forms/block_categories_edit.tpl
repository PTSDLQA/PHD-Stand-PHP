<p>Раздел:</p>
<p>
<select name="b_idsec" onchange="categories_getownercategories(this.value)">
{html_options options=$form.sections selected=$form.idsec}
</select>
</p>
{if $auth->isExpert()}
<p>Шаблон:</p>
<p>{editbox name="b_template" text=$form.template max=50 width="20%"}</p>
{else}
{hidden name="b_template" value=$form.template}
{/if}
<p><label><input type="checkbox" name="b_curcheck" onclick="categories_curcheck(this.checked)"{if $form.curcheck} checked{/if}>&nbsp;Текущий уровень</label></p>
<div id="catselectbox"{if $form.curcheck} style="display:none"{/if}>
<p>Ссылки на подкатегории из:</p>
<p>
<select name="b_idcat">
<option value="0">Не выбрано</option>
{html_options options=$form.categories selected=$form.idcat}
</select>
</p>
</div>
<p>Количество (0 - все):</p>
<p>{editbox name="b_rows" text=$form.rows max=3 width=50}</p>