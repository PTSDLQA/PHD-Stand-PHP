<div{if count($form.sections)==1} style="display:none"{/if}>
<p>Раздел:</p>
<p>
<select name="b_idsec" onchange="pages_getdirs(this.value)">
{html_options options=$form.sections}
</select>
</p>
</div>
<p>Шаблон:</p>
<p>{editbox name="b_template" text="pages.tpl" max=50 width="20%"}</p>
<p><label><input type="checkbox" name="b_curcheck" onclick="pages_curcheck(this.checked)">&nbsp;Текущий уровень</label></p>
<div{if count($form.dirs)==0} style="display:none"{/if}>
<div id="catselectbox">
<p>Ссылки на страницы подраздела:</p>
<p>
<select name="b_idcat">
<option value="0">Корневой</option>
{html_options options=$form.dirs}
</select>
</p>
</div>
</div>
