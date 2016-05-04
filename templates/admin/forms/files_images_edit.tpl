<form name="im_editform" method="post">
<p>{image id=$form.id width=80 height=80 noimgheight=true popup=true align="left" style="margin-right:5px"}</p>
<p>Имя файла:</p>
<p>{editbox name="basename" width="50%" text=$form.basename}</p>
<table class="invisiblegrid">
<tr>
<td>Ширина:</td>
<td></td>
<td>Высота:</td>
</tr>
<tr>
<td>{editbox name="width" width="70" text=$form.width}</td>
<td>X</td>
<td>{editbox name="height" width="70" text=$form.height}</td>
</tr>
</table>
<p>Описание:</p>
<p>{editbox name="caption" width="80%" text=$form.caption}</p>
<p>Принадлежность разделу:</p>
<p>
<select name="idsec">
<option value="0">Не выбрано</option>
{html_options options=$form.sections selected=$form.idsec}
</select>
</p>
{hidden name="id" value=$form.id}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="action" value="edit"}
{hidden name="authcode" value=$system.authcode}
<div align="right" style="margin-top:10px">
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>