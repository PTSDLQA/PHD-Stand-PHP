<form name="fm_uploadform" method="post" enctype="multipart/form-data" onsubmit="return fm_runupload(this)">
<p>Файл:</p>
<p><input type="file" name="uploadfile"></p>
<p>Описание:</p>
<p>{editbox name="caption" width="80%"}</p>
<p>Принадлежность разделу:</p>
<p>
<select name="idsec">
<option value="0">Не выбрано</option>
{html_options options=$form.sections selected=$form.idsec}
</select>
</p>
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="action" value="upload"}
{hidden name="authcode" value=$system.authcode}
<div align="right" style="margin-top:10px">
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>