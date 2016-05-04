{include file="_header.tpl"} 

<table class="form">
<tr><th class="title">Настройка блока</th></tr>
<tr>
<td class="place">
<form name="editblockform" method="post" enctype="multipart/form-data">
<div id="optionsbox" class="blockopt"></div>
{hidden name="id" value=$block.id}
{hidden name="block" value=$block.block}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="action" value="editblock"}
<div align="right" style="margin-top:10px">
{submit caption="Сохранить"}
<input type="button" class="button" value="Отмена" onclick="document.location='{$bprevurl}'" style="width:120px">
</div>
{hidden name="authcode" value=$system.authcode}
</form>
</td>
</tr>
</table>
<script type="text/javascript">onchangetype_edit({$block.id},'{$block.block}','{$block.block}')</script>

{include file="_footer.tpl"} 
