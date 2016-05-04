<!DOCTYPE html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Панель управления Astra.CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/templates/admin/style{if strpos($smarty.server.HTTP_USER_AGENT,"MSIE")!==false}_ie{elseif strpos($smarty.server.HTTP_USER_AGENT,"Opera")!==false}_opera{else}_firefox{/if}.css?{$options.version}" type="text/css">
<script type="text/javascript" src="/system/jsaculous/prototype.js"></script>
<script type="text/javascript" src="/system/jsaculous/scriptaculous.js?load=effects,controls,dragdrop"></script>
<script type="text/javascript" src="/system/jsaculous/window.js"></script>
{literal}<script type="text/javascript">
function auth_form(form)
{ if(form.login.value.length==0)
  { alert("Пожалуйста, корректно заполните поле Логин."); return false; }
  else if(form.password.value.length==0)
  { alert("Пожалуйста, корректно заполните поле Пароль."); return false; }
  return true;
}
function remember_form(form)
{ if(form.login.value.length==0)
  { alert("Пожалуйста, корректно заполните поле Логин."); return false; }
  return true;
}
</script>{/literal}
</head>
<body bgcolor="#F3FCFF">
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="98%" style="margin-top:10px">
<tr>
<td width="10" rowspan="3"></td>
<td width="5" height="5"><img src="/templates/admin/images/rp_corne.gif" width="5" height="5" border="0" alt=""></td>
<td style="border-top:1px solid #E1EDE9;"><img src="/templates/admin/images/spacer00.gif" width="1" height="1" border="0"></td>
<td width="5" height="5"><img src="/templates/admin/images/rp_cornf.gif" width="5" height="5" border="0" alt=""></td>
<td width="10" rowspan="3"></td>
</tr>
<tr>
<td valign="top" width="5" style="border-left:1px solid #E1EDE9;"><img src="/templates/admin/images/spacer00.gif" width="1" height="1" border="0"></td>
<td align="center" valign="middle">
<img src="/templates/admin/images/acmslogo.gif" width="100" height="100">
{if $smarty.get.message==1}
<p align="center" class="gray">Вам выслано письмо с информацией для подтверждения.</p>
{/if}
{if $smarty.get.message==2}
<p align="center" class="gray">Новый пароль выслан на ваш email.</p>
{/if}
{if $errors.notlogin}
<p align="center" class="gray">Указанный логин не найден.</p>
{/if}
{if $errors.notemail}
<p align="center" class="gray">В учетной записи не указан email.</p>
{/if}
<div style="width:200px">
{if $smarty.get.remember}
<form method="post" onsubmit="return remember_form(this);">
<table border="0" cellspacing="2" cellpadding="0" class="tbl_login">
<tr>
<td><img src="/templates/admin/images/sp.gif" width="1" height="30" alt=""></td>
<td><img src="/templates/admin/images/sp.gif" width="1" height="1" alt=""></td>
</tr>
<tr>
<td class="login_text">логин&nbsp;</td>
<td><input type="text" name="login" maxlength="20" class="input" style="width:90px;height:19px;font-size:10px;"></td>
</tr>
<tr>
<td class="login_text" height="19px">&nbsp;</td><td>&nbsp;</td>
</tr>
<tr>
<td><img src="/templates/admin/images/sp.gif" width="1" height="1" alt=""></td>
<td>
<input type="hidden" name="mode" value="auth">
<input type="hidden" name="action" value="remember">
<input type="submit" class="submit" value="Восстановить" style="width:90">
</td>
</tr>
<tr>
<td colspan="2"><img src="/templates/admin/images/sp.gif" width="1" height="1" alt=""></td>
</tr>
<tr>
<td colspan="2"><a style="margin-left:15px" class="cp_link_headding" href="admin.php">Назад к авторизации</a></td>
</tr>
</table>
</form>
{else}
<form method="post" onsubmit="return auth_form(this);">
<table border="0" cellspacing="2" cellpadding="0" class="tbl_login">
<tr>
<td><img src="/templates/admin/images/sp.gif" width="1" height="30" alt=""></td>
<td><img src="/templates/admin/images/sp.gif" width="1" height="1" alt=""></td>
</tr>
<tr>
<td class="login_text">логин&nbsp;</td>
<td><input type="text" name="login" maxlength="20" class="input" style="width:83px;height:19px;font-size:10px;width:80;"></td>
</tr>
<tr>
<td class="login_text">пароль&nbsp;</td>
<td><input type="password" name="password" maxlength="20" class="input" style="width:83px;height:19px;font-size:10px;width:80;"></td>
</tr>
<tr>
<td><img src="/templates/admin/images/sp.gif" width="1" height="1" alt=""></td>
<td>
<input type="hidden" name="mode" value="auth">
<input type="hidden" name="action" value="login">
<input type="submit" class="submit" value="Войти" style="width:80">
</td>
</tr>
<tr>
<td colspan="2"><img src="/templates/admin/images/sp.gif" width="1" height="1" alt=""></td>
</tr>
<tr>
<td colspan="2"><a style="margin-left:15px" class="cp_link_headding" href="admin.php?remember=1">Забыли пароль?</a></td>
</tr>
</table>
</form>
{/if}
</div>
<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;
</td>
<td valign="top" width="5" style="border-right:1px solid #E1EDE9;"><img src="/templates/admin/images/spacer00.gif" width="1" height="1" border="0"></td>
</tr>
<tr>
<td width="5" height="5"><img src="/templates/admin/images/rp_corng.gif" width="5" height="5" border="0" alt=""></td>
<td style="border-bottom:1px solid #E1EDE9;"><img src="/templates/admin/images/spacer00.gif" width="1" height="1" border="0"></td>
<td width="5" height="5"><img src="/templates/admin/images/rp_cornh.gif" width="5" height="5" border="0" alt=""></td>
</tr>
</table>
</body>
<script type="text/javascript" src="http://a-cms.ru/counter.php?type=free&host={$smarty.server.HTTP_HOST}"></script>
</html>