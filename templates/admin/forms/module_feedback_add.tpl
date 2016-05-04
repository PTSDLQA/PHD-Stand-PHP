<form name="addfieldform" method="post" onsubmit="return field_form(this)">
<p>Идентификатор:<sup style="color:gray">*</sup></p>
<p>{editbox name="field" max=20 width=100}</p>
<p>Название:<sup style="color:gray">*</sup></p>
<p>{editbox name="name" max=100 width="60%"}</p>
<p>Тип:<sup style="color:gray">*</sup></p>
<p>
<select name="type" onchange="fieldseltype(this.value)">
<option value="string">Строка</option>
<option value="int">Целое число</option>
<option value="float">Дробное число</option>
<option value="bool">Логический (Да/Нет)</option>
<option value="date">Дата</option>
<option value="text">Текст</option>
{if $form.full}
<option value="select">Значение из списка</option>
<option value="mselect">Множество значений из списка</option>
{else}
<option value="select" style="color:red" disabled title="Доступно только в полной версии">Значение из списка</option>
<option value="mselect" style="color:red" disabled title="Доступно только в полной версии">Множество значений из списка</option>
{/if}
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
<div id="field_typeselectbox" style="display:none">
<p>
<select name="idvar">
{html_options options=$form.vars}
<option value="">+Создать список+</option>
</select>
</p>
</div>
{hidden name="tab" value="page"}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="authcode" value=$system.authcode}
{hidden name="action" value="fld_add"}
<div align="right" style="margin-top:10px">
<p style="float:left"><label><input type="checkbox" name="fill">&nbsp;Обязательно для заполнения.</label></p>
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>