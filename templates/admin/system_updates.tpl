{include file="_header.tpl"}

<div class="box" style="width:800px">
<form name="uploadform" method="post" enctype="multipart/form-data" onsubmit="showLoading();return true;">
<h3>Загрузить файл:</h3>
<br>
<p><input type="file" name="updatefile"></p>
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="action" value="update"}
<div align="right" style="margin:5px;margin-top:10px">
{submit caption="Загрузить"}
</div>
{hidden name="authcode" value=$system.authcode}
</form>
</div>

{if $smarty.get.update=="ok"}
{assign var='debugdata' value='<script type="text/javascript">alert("Обновление установлено!")</script>'}
{elseif $smarty.get.update=="err_unpack"}
{assign var='debugdata' value='<script type="text/javascript">alert("Не удалось обновить файлы!")</script>'}
{elseif $smarty.get.update=="err_version"}
{assign var='debugdata' value='<script type="text/javascript">alert("Обновление не требуется!")</script>'}
{elseif $smarty.get.update=="err_file"}
{assign var='debugdata' value='<script type="text/javascript">alert("Неверный формат файла!")</script>'}
{/if}

{include file="_footer.tpl"}
