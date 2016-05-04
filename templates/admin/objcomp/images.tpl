<div id="images_gridbox"></div>
<div id="images_mainbox" align="right">
{button caption="Загрузить" onclick="images_getupload()"}
</div>
<div id="images_uploadbox" class="box" style="display:none">
<table class="invisiblegrid" width="100%">
<tr>
<td>Файл изображения:</td>
<td width="80%">Описание:</td>
</tr>
<tr>
<td><div id="images_filebox"></div></td>
<td width="80%"><input type="text" id="images_caption1" name="images_caption1" style="width:100%"></td>
</tr>
</table>
<div align="right" style="margin-top:10px">
{button caption="Загрузить" onclick="images_upload()"}
{button caption="Отмена" onclick="images_cancel()"}
</div>
</div>
<div id="images_editbox" class="box" style="display:none">
<table class="invisiblegrid" width="100%">
<tr>
<td>Заменить:</td>
<td width="80%">Описание:</td>
</tr>
<tr>
<td><div id="images_filebox2"></div></td>
<td width="80%"><input type="text" id="images_caption2" name="images_caption2" style="width:100%"></td>
</tr>
</table>
<div align="right" style="margin-top:10px">
<input type="hidden" id="images_id" name="images_id" value="0">
{button caption="Сохранить" onclick="images_save()"}
{button caption="Отмена" onclick="images_cancel()"}
</div>
</div>
<div id="images_messagebox" class="box" style="display:none" align="center"></div>
{if $mainframe}
<script type="text/javascript">images_refresh();</script>
{/if}
