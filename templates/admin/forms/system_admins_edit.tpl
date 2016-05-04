<form method="post" onsubmit="return editadmin_form(this)">
<p>Имя:<sup style="color:gray">*</sup></p>
<p>{editbox name="name" width="40%" text=$form.name}{if $form.id!=$auth->id}&nbsp;&nbsp;<label><input type="checkbox" name="active" value="Y"{if $form.active=='Y'} checked{/if}>&nbsp;Активен</label>{else}&nbsp;&nbsp;<input type="checkbox" name="active" checked disabled>&nbsp;Активен<input type="hidden" name="active" value="Y">{/if}</p>
<p>Логин:<sup style="color:gray">*</sup></p>
<p>{editbox name="login" max=20 width="20%" text=$form.login}</p>
<p>Новый пароль (если хотите сменить):</p>
<p>{passbox name="password"}</p>
<p>Email:</p>
<p>{editbox name="email" max=50 width="20%" text=$form.email}</p>
<h3>Доступ:</h3>
<div class="box">
<p title="Распределение прав доступно только в полной версии"><label><input type="checkbox" name="check_accessall" checked disabled>&nbsp;Полный доступ</label></p>
</div>
{hidden name="id" value=$form.id}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="action" value="edit"}
<div align="right" style="margin-top:10px">
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
{hidden name="authcode" value=$system.authcode}
</form>