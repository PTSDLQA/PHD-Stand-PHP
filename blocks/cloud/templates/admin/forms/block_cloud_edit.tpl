{hidden name="b_idsec" value=$form.idsec}
<div{if count($form.sections)==0} style="display:none"{/if}>
<p>Теги из раздела:</p>
<p>
<select name="b_idsearch">
<option value="0">Все</option>
{html_options options=$form.sections selected=$form.idsearch}
</select>
</p>
</div>
{if $auth->isExpert()}
<p>Шаблон:</p>
<p>{editbox name="b_template" text=$form.template max=50 width="20%"}</p>
{else}
{hidden name="b_template" value=$form.template}
{/if}
<p>Количество тегов:</p>
<p>{editbox name="b_count" width="80px" text=$form.count}</p>