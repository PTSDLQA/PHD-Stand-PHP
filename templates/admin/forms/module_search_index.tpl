<form name="indexform" method="post">
{section name=i loop=$form.sections}
<p><label><input type="checkbox" name="sections[]" value="{$form.sections[i].id}" checked>&nbsp;{$form.sections[i].caption}</label></p>
{/section}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="authcode" value=$system.authcode}
{hidden name="action" value="indexall"}
<div align="right" style="margin-top:10px">
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>