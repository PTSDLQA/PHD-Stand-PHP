<form name="fa_uploadform" method="post" enctype="multipart/form-data" onsubmit="return false">
<p>Файл:</p>
<p><input type="file" name="fa_file0" style="width:40%"></p>
<p><a href="javascript:morefiles()" title="Несколько"><img src="/templates/admin/images/add.gif" width="16" height="16" alt="Несколько"></a></p>
<div id="morefiles" style="display:none">
<p><input type="file" name="fa_file1" style="width:40%"></p>
<p><input type="file" name="fa_file2" style="width:40%"></p>
<p><input type="file" name="fa_file3" style="width:40%"></p>
<p><input type="file" name="fa_file4" style="width:40%"></p>
<p><input type="file" name="fa_file5" style="width:40%"></p>
</div>
<div align="right" style="margin-top:10px">
<p style="float:left"><label><input type="checkbox" name="replace" checked>&nbsp;C заменой существующих</label></p>
{button class="submit" caption="OK" onclick="runupload(this.form)"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
{hidden name="authcode" value=$system.authcode}
</form>