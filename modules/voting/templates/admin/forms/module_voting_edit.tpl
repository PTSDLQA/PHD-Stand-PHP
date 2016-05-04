<form name="editvariantform" method="post" onsubmit="return variant_form(this)">
<p>Вариант:</p>
<p>{editbox name="name" width="60%" text=$form.name}</p>
{hidden name="id" value=$form.id}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="authcode" value=$system.authcode}
{hidden name="action" value="editvariant"}
<div align="right" style="margin-top:10px">
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>