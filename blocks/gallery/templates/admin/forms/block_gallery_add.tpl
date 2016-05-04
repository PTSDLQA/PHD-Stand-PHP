<div{if count($form.sections)==1} style="display:none"{/if}>
<p>Раздел:</p>
<p>
<select name="b_idsec" onchange="categories_getcategories(this.value)">
{html_options options=$form.sections}
</select>
</p>
</div>
<p>Шаблон:</p>
<p>{editbox name="b_template" text="gallery.tpl" max=50 width="20%"}</p>
<p>Категория:</p>
<p>
<select name="b_idcat" onchange="gallery_getalbums(this.form.b_idsec.value,this.value)">
<option value="0">Все</option>
{html_options options=$form.categories}
</select>
</p>
<p>Альбом:</p>
<p>
<select name="b_idalb">
<option value="0">Все</option>
{html_options options=$form.albums}
</select>
</p>
<p>Сортировка:</p>
<select name="b_sort">
<option value="1">По заданному порядку</option>
<option value="2">Случайно</option>
</select>
<p>Количество фото:</p>
<p>{editbox name="b_rows" text="5" max=3 width=50}</p>
