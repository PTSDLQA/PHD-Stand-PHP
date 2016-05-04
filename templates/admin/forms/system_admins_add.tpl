<form method="post" onsubmit="return addadmin_form(this)">
<p>Имя:<sup style="color:gray">*</sup></p>
<p>{editbox name="name" width="40%"}&nbsp;&nbsp;<label><input type="checkbox" name="active" value="Y" checked>&nbsp;Активен</label></p>
<p>Логин:<sup style="color:gray">*</sup></p>
<p>{editbox name="login" max=20 width="20%"}</p>
<p>Пароль:<sup style="color:gray">*</sup></p>
<p>{passbox name="password"}</p>
<p>Email:</p>
<p>{editbox name="email" max=50 width="20%"}</p>
<h3>Доступ:</h3>
<div class="box">
<p title="Распределение прав доступно только в полной версии"><label><input type="checkbox" name="check_accessall" checked disabled>&nbsp;Полный доступ</label></p>
</div>
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="action" value="add"}
<div align="right" style="margin-top:10px">
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
{hidden name="authcode" value=$system.authcode}
</form>