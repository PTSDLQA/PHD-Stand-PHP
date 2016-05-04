<p>Раздел:</p>
<p>
<select name="b_idsec" onchange="categories_getownercategories(this.value)">
{html_options options=$form.sections}
</select>
</p>
<p>Шаблон:</p>
<p>{editbox name="b_template" text="categories.tpl" max=50 width="20%"}</p>
<p><label><input type="checkbox" name="b_curcheck" onclick="categories_curcheck(this.checked)">&nbsp;Текущий уровень</label></p>
<div id="catselectbox">
<p>Ссылки на подкатегории из:</p>
<p>
<select name="b_idcat">
<option value="0">Не выбрано</option>
{html_options options=$form.categories}
</select>
</p>
</div>
<p>Количество (0 - все):</p>
<p>{editbox name="b_rows" text="0" max=3 width=50}</p>

