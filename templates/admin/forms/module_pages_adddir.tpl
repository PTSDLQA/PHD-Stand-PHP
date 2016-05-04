<form name="adddirform" method="post" onsubmit="return adddir_form(this)">
<table class="invisiblegrid" width="100%">
<tr>
<td width="60%">Название:<sup style="color:gray">*</sup></td>
<td width="40%">Идентификатор (URL):</td>
</tr>
<tr>
<td width="60%">{editbox name="name"}</td>
<td width="40%">{editbox name="urlname" max=100}</td>
</tr>
</table>
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="page" value=$system.page}
{hidden name="action" value="adddir"}
{hidden name="authcode" value=$system.authcode}
<div align="right" style="margin-top:10px">
<p style="float:left">
<label><input type="checkbox" name="active" checked>&nbsp;Активно</label>&nbsp;&nbsp;&nbsp;{if $form.usemap}<label><input type="checkbox" name="inmap" checked>&nbsp;В карте сайта</label>{else}{hidden name="inmap" value=1}{/if}
</p>
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>