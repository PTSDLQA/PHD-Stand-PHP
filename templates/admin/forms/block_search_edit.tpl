{hidden name="b_idsec" value=$form.idsec}
{if $auth->isExpert()}
<p>Шаблон:</p>
<p>{editbox name="b_template" text=$form.template max=50 width="20%"}</p>
{else}
{hidden name="b_template" value=$form.template}
{/if}
