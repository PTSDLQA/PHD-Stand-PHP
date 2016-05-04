{include file="_header.tpl"}

<div class="box" style="width:800px">
<form method="post">
<p title="Доступно только в полной версии"><label><input type="checkbox" name="autoupdate" disabled>&nbsp;Проверять наличие обновлений</label></p>
<p><label><input type="checkbox" name="debugmode"{if $options.debugmode} checked{/if}>&nbsp;Режим отладки</label></p>
<p title="Доступно только в полной версии"><label><input type="checkbox" name="smartysecurity" disabled>&nbsp;Защищенный режим Smarty</label></p>
<p title="Доступно только в полной версии"><label><input type="checkbox" name="smtp_use" disabled>&nbsp;SMTP для отправки писем</label></p>
<p><label><input type="radio" name="caching" value="0" checked>&nbsp;Без кэширования</label></p>
<p title="Доступно только в полной версии"><label><input type="radio" name="caching" value="1" disabled>&nbsp;Кэширование в файлах</label></p>
<p title="Доступно только в полной версии"><label><input type="radio" name="caching" value="2" disabled>&nbsp;Кэширование в memcached</label></p>
<input type="hidden" name="mode" value="system">
<input type="hidden" name="item" value="options">
<input type="hidden" name="action" value="save">
<div align="right" style="margin:5px;margin-top:10px;">
{submit caption="Сохранить"}
</div>
{hidden name="authcode" value=$system.authcode}
</form>
</div>

{include file="_footer.tpl"}