{include file="_header.tpl"}

{literal}
<script type="text/javascript">
function goimport(form)
{ if(form.importmode[0].checked && form.importfile.value.length==0)
  { alert('Укажите загружаемый файл!'); return false; }
  if(form.importmode[1].checked && form.importpath.value.length==0)
  { alert('Укажите путь к файлу!'); return false; }
  if(confirm('Вы действительно хотите импортировать конфигурацию?'))
  { showLoading();
    return true;
  }
  else
  return false;
}
</script>
{/literal}

<div class="box" style="width:800px">
<form method="post" enctype="multipart/form-data" onsubmit="return goimport(this)">
<h3>Импорт:</h3>
<p style="margin-top:5px;"><label><input id="im1" type="radio" name="importmode" value="1" checked>&nbsp;Загрузить архив:</label></p>
<p style="margin-top:5px;"><input type="file" name="importfile" onchange="$('im1').checked=true;"></p>
<p style="margin-top:5px;"><label><input id="im2" type="radio" name="importmode" value="2">&nbsp;Путь к файлу на сервере:</label></p>
<p style="margin-top:5px;">{editbox name="importpath" width="40%" onchange="$('im2').checked=true;"}</p>
<input type="hidden" name="mode" value="site">
<input type="hidden" name="item" value="import">
<input type="hidden" name="action" value="import">
<div align="right" style="margin:5px;margin-top:10px;">
{submit caption="Импорт"}
</div>
{hidden name="authcode" value=$system.authcode}
</form>
</div>

<div class="box" style="width:800px;margin-top:15px;">
<form method="post">
<h3>Экспорт:</h3>
<p><label><input type="checkbox" name="bd" checked>&nbsp;База данных</label></p>
<p><label><input type="checkbox" name="theme" checked>&nbsp;Тема дизайна</label></p>
<p><label><input type="checkbox" name="files">&nbsp;Пользовательские файлы</label></p>
<p><label><input type="checkbox" name="extensions">&nbsp;Используемые расширения</label></p>
<p><label><input type="radio" name="type" value="zip"{if $usezip} checked{else} disabled{/if}>&nbsp;zip</label>&nbsp;&nbsp;
<label><input type="radio" name="type" value="gz"{if !$usezip} checked{/if}>&nbsp;gz</label></p>
<input type="hidden" name="mode" value="site">
<input type="hidden" name="item" value="import">
<input type="hidden" name="action" value="export">
<div align="right" style="margin:5px;margin-top:10px;">
{submit caption="Экспорт"}
</div>
{hidden name="authcode" value=$system.authcode}
</form>
</div>

{include file="_footer.tpl"}
