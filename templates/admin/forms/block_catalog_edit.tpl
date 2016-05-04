<div{if count($form.sections)==1} style="display:none"{/if}>
<p>Раздел:</p>
<p>
<select name="b_idsec" onchange="categories_getcategories(this.value)">
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
<p>Категория:</p>
<p>
<select name="b_idcat">
<option value="0">Все</option>
{html_options options=$form.categories selected=$form.idcat}
</select>
</p>
<p>Сортировка:</p>
<select name="b_sort">
<option value="1"{if $form.sort==1} selected{/if}>По дате размещения вверх</option>
<option value="2"{if $form.sort==2} selected{/if}>По дате размещения вниз</option>
<option value="3"{if $form.sort==3} selected{/if}>По названию</option>
<option value="4"{if $form.sort==4} selected{/if}>По заданному порядку</option>
<option value="5"{if $form.sort==5} selected{/if}>Случайно</option>
</select>
{if $auth->isSuperAdmin()}
<p>Фильтр:</p>
<p>{editbox name="b_filter" width="20%" text=$form.filter}</p>
{/if}
<p>Количество материалов:</p>
<p>{editbox name="b_rows" text=$form.rows max=3 width=50}</p>
<p><label><input type="checkbox" name="b_nodouble"{if $form.nodouble} checked{/if} value="1">&nbsp;Исключать уже показанные на странице записи</label></p>