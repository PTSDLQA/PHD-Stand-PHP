<form name="uploadform" method="post" enctype="multipart/form-data" onsubmit="showLoading();return true;">
<p>Загрузить архив:</p>
<p><input type="file" name="extensionfile0" style="width:40%"></p>
<p><a href="javascript:moreextensions()" title="Несколько"><img src="/templates/admin/images/add.gif" width="16" height="16" alt="Несколько"></a></p>
<div id="moreextensions" style="display:none">
<p><input type="file" name="extensionfile1" style="width:40%"></p>
<p><input type="file" name="extensionfile2" style="width:40%"></p>
<p><input type="file" name="extensionfile3" style="width:40%"></p>
<p><input type="file" name="extensionfile4" style="width:40%"></p>
<p><input type="file" name="extensionfile5" style="width:40%"></p>
</div>
{hidden name="tab" value=$form.tab}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="action" value="upload"}
<div align="right" style="margin-top:10px">
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
{hidden name="authcode" value=$system.authcode}
</form>