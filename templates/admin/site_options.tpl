{include file="_header.tpl"}

<div class="box" style="width:800px">
<form name="siteform" method="post">
<table width="100%" cellspacing="5">
<tr>
<td width="250">Название сайта:</td>
<td>{editbox name="sitename" text=$sitename}</td>
</tr>
<tr>
<td>Заголовок сайта (title):</td>
<td>{editbox name="sitetitle" text=$sitetitle}</td>
</tr>
<tr>
<td>Обратный адрес для отправляемых писем:</td>
<td>{editbox name="mailsfrom" text=$options.mailsfrom}</td>
</tr>
<tr>
<td title="Доступно только в полной версии">Переадресация на хост:</td>
<td>{editbox name="gohost" text=$options.gohost disabled=true}</td>
</tr>
<tr>
<td><label for="404gomain">На главную если страница не найдена:</label></td>
<td><input id="404gomain" type="checkbox" name="404gomain"{if $options.404gomain} checked{/if}></td>
</tr>
<tr>
<td><label for="wizard" title="Доступно только в полной версии">Доступный репозиторий:</label></td>
<td><input id="wizard" type="checkbox" name="wizard" disabled></td>
</tr>
<tr>
<td><label for="transurl">Транслитерация идентификаторов URL:</label></td>
<td><input id="transurl" type="checkbox" name="transurl"{if $options.transurl} checked{/if}></td>
</tr>
<tr>
<td><label for="siteclose">Закрытый режим:</label></td>
<td><input id="siteclose" type="checkbox" name="siteclose"{if $options.siteclose} checked{/if}></td>
</tr>
<tr>
<td>Текст для закрытого режима:</td>
<td>{textarea name="siteclosetext" rows=2 text=$options.siteclosetext}</td>
</tr>
<tr>
<td>Код счетчиков:</td>
<td>{textarea name="codecounters" rows=3 text=$options.codecounters}</td>
</tr>
<tr>
<td>Код своих мета тегов:</td>
<td>{textarea name="codemeta" rows=3 text=$options.codemeta}</td>
</tr>
<tr>
<td><label for="resetcache" title="Доступно только в полной версии">Сбросить кэш:</label></td>
<td><input id="resetcache" type="checkbox" name="resetcache" disabled></td>
</tr>
<tr>
<td><label for="cleartpl">Перекомпилировать шаблоны:</label></td>
<td><input id="cleartpl" type="checkbox" name="cleartpl"></td>
</tr>
<tr>
<td align="right" colspan="2">
{submit caption="Сохранить"}
</td>
</tr>
</table>
{hidden name="action" value="save"}
{hidden name="authcode" value=$system.authcode}
</form>
</div>

{include file="_footer.tpl"}