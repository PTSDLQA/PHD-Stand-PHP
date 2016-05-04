{include file="_header.tpl"}

<h1>{$section_name}</h1>

{$content}

{if $errors.captcha}
<p style="color:red">Неверно введены контрольные цифры, попробуйте еще раз.</p>
{/if}

{literal}
<script type="text/javascript">
function valid_form(form)
{ {/literal}{foreach from=$fields item=field}
  {if $field.fill=="Y" && $field.type!="bool" && $field.type!="select"}
  if(form.{$field.field}.value.replace(/\s+/, '').length==0)
  {literal}{{/literal} alert("Пожалуйста, заполните поле '{$field.name}'"); return false;{literal}}{/literal}
  {/if}
  {/foreach}
  {literal}
  if(form.captcha.value.replace(/\s+/, '').length<4)
  { alert('Пожалуйста, укажите цифры на картинке.'); return false; }{/literal}{literal}
  return true;
}
</script>
{/literal}

<h3>Отправить сообщение:</h3>

<form method="post" onsubmit="return valid_form(this)" enctype="multipart/form-data">
{foreach from=$fields item=field}

{if $field.type=="string"}
<p>{$field.name}:{if $field.fill=="Y"}<b>*</b>{/if}</p>
<p>{editbox name=$field.field max=$field.length text=$field.value width="40%"}</p>

{elseif $field.type=="int" || $field.type=="float"}
<p>{$field.name}:{if $field.fill=="Y"}<b>*</b>{/if}</p>
<p>{editbox name=$field.field max=10 width=60 text=$field.value}</p>

{elseif $field.type=="bool"}
<p><input type="checkbox" name="{$field.field}"{if $field.value=="Y"} checked{/if}>&nbsp;{$field.name}</p>

{elseif $field.type=="text"}
<p>{$field.name}{if $field.fill=="Y"}<b>*</b>{/if}:</p>
<p>{textarea name=$field.field rows=$field.property text=$field.value}</p>

{elseif $field.type=="select"}
<p>{$field.name}:{if $field.fill=="Y"}<b>*</b>{/if}</p>
<p>
<select name="{$field.field}">
{if $field.fill=='N'}<option value="0">Не выбрано</option>{/if}
{html_options options=$field.options selected=$field.value}
</select>
</p>

{elseif $field.type=="mselect"}
<p>{$field.name}:{if $field.fill=="Y"}<b>*</b>{/if}</p>
<p>{html_checkboxes name=$field.field options=$field.options checked=$field.value separator=", "}</p>

{elseif $field.type=="date"}
<p>{$field.name}:{if $field.fill=="Y"}<b>*</b>{/if}</p>
<p>{html_select_date prefix=$field.field time=$field.value field_order="DMY" start_year=$field.startyear end_year="+3"}</p>

{elseif $field.type=="file"}
<p>{$field.name}:</p>
<p><input type="file" name="{$field.field}"></p>
{/if}
{/foreach}

<p>
{captcha style="float:right"}
Введите цифры на рисунке:<b>*</b>
{editbox name="captcha" max=4 width="40px"}
</p>
<div class="clear"></div>

{submit caption="Отправить"}
{hidden name="action" value="send"}
</form>

{include file="_footer.tpl"}