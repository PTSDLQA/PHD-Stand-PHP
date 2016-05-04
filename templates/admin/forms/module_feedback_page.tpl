<form name="pageform" method="post" onsubmit="return page_form(this)">
{fckeditor name="content" text=$form.content height=500}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="tab" value="page"}
{hidden name="authcode" value=$system.authcode}
{hidden name="action" value="save"}
<div align="right" style="margin-top:10px">
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>
