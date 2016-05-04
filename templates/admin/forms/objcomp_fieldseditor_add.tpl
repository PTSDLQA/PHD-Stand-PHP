<form name="addfieldform" method="post" onsubmit="return field_form(this)">
<p>Идентификатор:<sup style="color:gray">*</sup></p>
<p>{editbox name="field" max=20 width=100}</p>
<p>Название:<sup style="color:gray">*</sup></p>
{section name=i loop=$form.languages}
<p>{editbox name=$form.languages[i].name max=100 width="60%"}</p>
{/section}
<p>Тип:<sup style="color:gray">*</sup></p>
<p>
<select name="type" onchange="fieldseltype(this.value)">
<option value="string">Строка</option>
<option value="int">Целое число</option>
<option value="float">Дробное число</option>
<option value="bool">Логический (Да/Нет)</option>
<option value="date">Дата</option>
<option value="text">Текст</option>
<option value="format">Форматированный текст</option>
<option value="select" style="color:red" disabled title="Доступно только в полной версии">Значение из списка</option>
<option value="mselect" style="color:red" disabled title="Доступно только в полной версии">Множество значений из списка</option>
<option value="image">Изображение</option>
<option value="file">Файл</option>
</select>
</p>
<div id="field_typestringbox">
<p>Длина:<sup style="color:gray">*</sup></p>
<p>{editbox name="length" width="50" max=3 text="50"}</p>
</div>
<div id="field_typeboolbox" style="display:none">
<p>По умолчанию:&nbsp;<label><input type="radio" name="booldef" value="1">&nbsp;Да</label>&nbsp;&nbsp;<label><input type="radio" name="booldef" value="0" checked>&nbsp;Нет</label></p>
</div>
<div id="field_typetextbox" style="display:none">
<p>Высота редактора (строки):<sup style="color:gray">*</sup></p>
<p>{editbox name="rows" width="50" max=3 text="5"}</p>
</div>
<div id="field_typeformatbox" style="display:none">
<p>Высота редактора (пиксели):<sup style="color:gray">*</sup></p>
<p>{editbox name="height" width="50" max=3 text="200"}</p>
</div>
<div{if $form.usesearch || $form.usenofront || $form.usefill} class="box" style="margin-top:10px;"{/if}>
{if $form.usesearch}
<p><label><input type="checkbox" name="search">&nbsp;Фильтр по полю для администратора.</label></p>
{/if}
{if $form.usenofront}
<p><label><input type="checkbox" name="nofront">&nbsp;Не использовать во внешних формах.</label></p>
{/if}
{if $form.usefill}
<p><label><input type="checkbox" name="fill">&nbsp;Обязательно для заполнения во внешних формах.</label></p>
{/if}
</div>
{hidden name="tab" value=$form.tab}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="obj_action" value="fld_add"}
{hidden name="authcode" value=$system.authcode}
<div align="right" style="margin-top:10px">
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>