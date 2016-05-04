<p>Раздел:</p>
<p>
<select name="b_idsec">
{html_options options=$form.sections selected=$form.idsec}
</select>
</p>
{if $auth->isExpert()}
<p>Шаблон:</p>
<p>{editbox name="b_template" text=$form.template max=50 width="20%"}</p>
{else}
{hidden name="b_template" value=$form.template}
{/if}