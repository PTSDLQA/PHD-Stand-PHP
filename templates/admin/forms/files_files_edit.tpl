<form name="fm_editform" method="post" enctype="multipart/form-data">
<p>Оригинальное имя:</p>
<p>{editbox name="name" width="50%" text=$form.name}</p>
<p>Реальное имя:</p>
<p>{editbox name="basename" width="50%" text=$form.basename}</p>
<p>Описание:</p>
<p>{editbox name="caption" width="80%" text=$form.caption}</p>
<p>Размер (байты):</p>
<p>{editbox name="size" width="80" text=$form.size}&nbsp;<input type="checkbox" name="realsize"{if $form.realsize} checked{/if}>&nbsp;Реальный размер</p>
<p>Скачиваний:</p>
<p>{editbox name="dwnl" width="80" text=$form.dwnl}</p>
<p>Заменить:</p>
<p><input type="file" name="uploadfile"></p>
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