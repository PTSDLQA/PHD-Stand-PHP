<form name="fm_registerform" method="post" enctype="multipart/form-data" onsubmit="return fm_runregister(this)">
<p>Путь к файлу или каталогу на сервере</p>
<p>{editbox name="path" width="60%"}</p>
<p>Принадлежность разделу:</p>
<p>
<select name="idsec">
<option value="0">Не выбрано</option>
{html_options options=$form.sections selected=$form.idsec}
</select>
</p>
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="action" value="register"}
{hidden name="authcode" value=$system.authcode}
<div align="right" style="margin-top:10px">
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>