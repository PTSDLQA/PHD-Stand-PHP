<form name="addimageform" method="post" enctype="multipart/form-data">
<table class="invisiblegrid">
<tr>
<td>Файл фото:</td>
<td width="80%">Описание:</td>
</tr>
<tr>
<td><input type="file" name="image1"></td>
<td width="80%">{editbox name="name1"}</td>
</tr>
<tr>
<td><input type="file" name="image2"></td>
<td width="80%">{editbox name="name2"}</td>
</tr>
<tr>
<td><input type="file" name="image3"></td>
<td width="80%">{editbox name="name3"}</td>
</tr>
<tr>
<td><input type="file" name="image4"></td>
<td width="80%">{editbox name="name4"}</td>
</tr>
<tr>
<td><input type="file" name="image5"></td>
<td width="80%">{editbox name="name5"}</td>
</tr>
<tr>
<td><input type="file" name="image6"></td>
<td width="80%">{editbox name="name6"}</td>
</tr>
</table>
<p>Файл архива (zip)</p>
<p><input type="file" name="archzip"></p>
{if $auth->isSuperAdmin()}
<p>Путь к архиву или каталогу на сервере:</p>
<p>{editbox name="path" width="60%"}</p>
{/if}
{hidden name="idalb" value=$form.idalb}
{hidden name="tab" value="albums"}
{hidden name="tab_albums" value="images"}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="authcode" value=$system.authcode}
{hidden name="action" value="addimage"}
<div align="right" style="margin-top:10px">
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>