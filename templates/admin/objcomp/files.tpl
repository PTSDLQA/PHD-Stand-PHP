<div id="files_gridbox"></div>
<div id="files_mainbox" align="right">
{button caption="Выбрать" onclick="files_getregister()"}
{button caption="Загрузить" onclick="files_getupload()"}
</div>
<div id="files_uploadbox" class="box" style="display:none">
<table class="invisiblegrid" width="100%">
<tr>
<td>Файл:</td>
<td width="80%">Описание:</td>
</tr>
<tr>
<td><div id="files_filebox"></div></td>
<td width="80%"><input type="text" id="files_caption1" name="files_caption1" style="width:100%"></td>
</tr>
</table>
<div align="right" style="margin-top:10px">
{button caption="Загрузить" onclick="files_upload()"}
{button caption="Отмена" onclick="files_cancel()"}
</div>
</div>
<div id="files_registerbox" class="box" style="display:none">
<table class="invisiblegrid" width="100%">
<tr>
<td width="20%"><span {help text="Файлы загруженные в каталог /ifiles/"}>Файл:</span></td>
<td width="80%">Описание:</td>
</tr>
<tr>
<td width="20%"><select id="files_path" name="files_path" style="width:100%">{html_options options=$files}</select></td>
<td width="80%"><input type="text" id="files_caption2" name="files_caption2" style="width:100%"></td>
</tr>
</table>
<div align="right" style="margin-top:10px">
{button caption="Выбрать" onclick="files_register()"}
{button caption="Отмена" onclick="files_cancel()"}
</div>
</div>
<div id="files_editbox" class="box" style="display:none">
<table class="invisiblegrid" width="100%">
<tr>
<td>Заменить:</td>
<td width="80%">Описание:</td>
</tr>
<tr>
<td><div id="files_filebox2"></div></td>
<td width="80%"><input type="text" id="files_caption3" name="files_caption3" style="width:100%"></td>
</tr>
</table>
<div align="right" style="margin-top:10px">
<input type="hidden" id="files_id" name="files_id" value="0">
{button caption="Сохранить" onclick="files_save()"}
{button caption="Отмена" onclick="files_cancel()"}
</div>
</div>
<div id="files_messagebox" class="box" style="display:none" align="center"></div>
{if $mainframe}
<script type="text/javascript">files_refresh();</script>
{/if}

