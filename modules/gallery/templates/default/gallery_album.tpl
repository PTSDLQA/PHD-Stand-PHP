{include file="_header.tpl"}
{lightbox_init}

<h1>{$album.name}</h1>

<p>{$album.description}</p>

{section name=i loop=$images}
{image data=$images[i] width=150 height=150 style="float:left" lightbox=true}
{/section}

<div class="clear"></div>

{if $options.usevote}
<form method="post">
Оценка: {$album.vote}, Голосов: {$album.cvote},
{if !$isvote}
<select name="vote">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5" selected>5</option>
</select>
{submit caption="Оценить"}
{hidden name="action" value="addvote"}
{else}
Вы уже проголосовали.
{/if}
</form>
{/if}

{if $options.usecomments}
{if $comments}
<h3>Комментарии:</h3>
{/if}
{section name=i loop=$comments}
<p><b>{$comments[i].date|date_format:"%D %T"} {$comments[i].name}:</b></p>
<p>{$comments[i].message}</p>
{/section}

{if $errors.captcha}
<p style="color:red">Неверно введены контрольные цифры, попробуйте еще раз.</p>
{/if}

{literal}
<script type="text/javascript">
function valid_form(form)
{ if(form.name.value.replace(/\s+/,'').length==0)
  { alert("Пожалуйста, заполните имя."); return false; }
  if(form.message.value.replace(/\s+/,'').length<5)
  { alert("Пожалуйста, заполните сообщение."); return false; }
  return true;
}
</script>
{/literal}

<h4>Оставить комментарий:</h4>
<form name="addcommentform" method="post" onsubmit="return valid_form(this)">
Ваше имя:<br>
{editbox name="name" width="40%" text=$form.name}<br>
<p>
<input type="button" value=" B " onclick="addTag('b')">&nbsp;&nbsp;
<input type="button" value=" I " onclick="addTag('i')">&nbsp;&nbsp;
<input type="button" value=" U " onclick="addTag('u')">&nbsp;&nbsp;
</p>
{textarea id="message" name="message" rows=6 text=$form.message}
<br>
{captcha style="float:right"}
Введите цифры на картинке: {editbox name="captcha" max=4 width="40px"}
<div class="clear"></div>
{submit caption="Добавить"}
{hidden name="action" value="addcomment"}
</form>
{/if}

{include file="_footer.tpl"}