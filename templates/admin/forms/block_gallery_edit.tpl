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
<select name="b_idcat" onchange="gallery_getalbums(this.form.b_idsec.value,this.value)">
<option value="0">Все</option>
{html_options options=$form.categories selected=$form.idcat}
</select>
</p>
<p>Альбом:</p>
<p>
<select name="b_idalb">
<option value="0">Все</option>
{html_options options=$form.albums selected=$form.idalb}
</select>
</p>
<p>Сортировка:</p>
<select name="b_sort">
<option value="1"{if $form.sort==1} selected{/if}>По заданному порядку</option>
<option value="2"{if $form.sort==2} selected{/if}>Случайно</option>
</select>
<p>Количество фото:</p>
<p>{editbox name="b_rows" text=$form.rows max=3 width=50}</p>
