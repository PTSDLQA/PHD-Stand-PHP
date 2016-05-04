{hidden name="b_idsec" value=$form.idsec}
<p>Шаблон:</p>
<p>{editbox name="b_template" text="cloud.tpl" max=50 width="20%"}</p>
<div{if count($form.sections)==0} style="display:none"{/if}>
<p>Теги из раздела:</p>
<p>
<select name="b_idsearch">
<option value="0">Все</option>
{html_options options=$form.sections}
</select>
</p>
</div>
<p>Количество тегов:</p>
<p>{editbox name="b_count" text="50" width="80px"}</p>
