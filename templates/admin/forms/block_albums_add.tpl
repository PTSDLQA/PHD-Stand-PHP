<div{if count($form.sections)==1} style="display:none"{/if}>
<p>Раздел:</p>
<p>
<select name="b_idsec" onchange="categories_getcategories(this.value)">
{html_options options=$form.sections}
</select>
</p>
</div>
<p>Шаблон:</p>
<p>{editbox name="b_template" text="albums.tpl" max=50 width="20%"}</p>
<p>Категория:</p>
<p>
<select name="b_idcat">
<option value="0">Все</option>
{html_options options=$form.categories}
</select>
</p>
<p>Сортировка:</p>
<select name="b_sort">
<option value="1" selected>По дате размещения вверх</option>
<option value="2">По дате размещения вниз</option>
<option value="3">По названию</option>
<option value="4">По заданному порядку</option>
<option value="5">Случайно</option>
</select>
{if $auth->isSuperAdmin()}
<p>Фильтр:</p>
<p>{editbox name="b_filter" width="20%"}</p>
{/if}
<p>Количество альбомов:</p>
<p>{editbox name="b_rows" text="5" max=3 width=50}</p>