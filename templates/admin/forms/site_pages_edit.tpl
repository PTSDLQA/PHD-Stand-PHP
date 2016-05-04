<form name="pageeditform" method="post">
<p>Шаблон:<sup style="color:gray">*</sup></p>
<p>{editbox name="template" width="40%" text=$form.template}</p>
{hidden name="id" value=$form.id}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="action" value="editpage"}
{hidden name="authcode" value=$system.authcode}
<div align="right" style="margin-top:10px">
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>