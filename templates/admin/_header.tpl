<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>{$caption} - Панель управления Astra.CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/templates/admin/style{if strpos($smarty.server.HTTP_USER_AGENT,"MSIE")!==false}_ie{elseif strpos($smarty.server.HTTP_USER_AGENT,"Opera")!==false}_opera{else}_firefox{/if}.css?{$options.version}" type="text/css">
<link rel="stylesheet" href="/templates/admin/windows/default.css" type="text/css">
<link rel="stylesheet" href="/templates/admin/windows/alphacube.css" type="text/css">
<link rel="stylesheet" href="/templates/admin/windows/alert.css" type="text/css">
<script type="text/javascript" src="/system/jsaculous/prototype.js"></script>
<script type="text/javascript" src="/system/jsaculous/scriptaculous.js?load=effects,controls,dragdrop"></script>
<script type="text/javascript" src="/system/jsaculous/window.js"></script>
<script type="text/javascript" src="/system/jsaculous/dateselect.js"></script>
<script type="text/javascript" src="/system/jsaculous/tree.js"></script>
<script type="text/javascript" src="/system/jsaculous/sselect.js"></script>
<script type="text/javascript" src="/system/jsoverlib/overlib.js"></script>
<script type="text/javascript" src="/system/jsoverlib/overlib_hideform.js"></script>
<script type="text/javascript" src="/system/jscodemirror/codemirror.js"></script>
<script type="text/javascript" src="/system/jshttprequest/jshttprequest.js"></script>
{$jscripts}<script type="text/javascript">runLoading();</script>
</head>
<body bgcolor="#F3FCFF">
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td height="15" align="left" width="100%" class="hline">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td width="100" align="center"><a class="cp_link_headding" href="http://a-cms.ru" target="_blank"><b>Astra.CMS Free</b></a></td>
<td valign="middle"><div class="gray"><a class="cp_link_headding" href="http://{$domain}" target="_blank">{$domain}</a>{if $site_name} - {$site_name}{/if}</div></td>
<td align="right" valign="middle">v.{$options.version|regex_replace:"/([0-9][0-9])$/":".\\1"}&nbsp;</td>
</tr>
</table>
</td>
</tr>
<tr>
<td width="100%" height="26" background="/templates/admin/images/tp_but_g.gif" align="left">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
{assign var="menux" value=0}
{if $menu.system}
{if $system.mode=="system"}
<td width="100" height="26" class="tp_link_button" bgcolor="#F3FCFF" valign="bottom" align="left">
<img width="16" height="16" src="/templates/admin/images/icons/main_system.gif" style="float:left;margin-left:5px;">
&nbsp;Система
</td>
{else}
{capture name=system_menu}
<table width="100%">
{section name=i loop=$menu.system}
<tr>
<td width="16"><img src="{$menu.system[i].ico}" width="16" height="16"></td>
{if $menu.system[i].close}
<td><a class="cp_link_headding" style="color:red" title="Доступно только в полной версии">{$menu.system[i].name}</a></td>
{else}
<td><a class="cp_link_headding" href="admin.php?mode=system&item={$menu.system[i].item}">{$menu.system[i].name}</a></td>
{/if}
</tr>
{/section}
</table>
{/capture}
<td width="100" height="26" valign="bottom" class="tp_link_button0a" align="left"
{popup sticky=true text=$smarty.capture.system_menu fgcolor="#F3FCFF" bgcolor="86BECD" noclose=true fixx=$menux fixy=44}>
<img width="16" height="16" src="/templates/admin/images/icons/main_system2.gif" style="float:left;margin-left:5px;">
&nbsp;<a href="admin.php?mode=system" class="tp_link_button0">Система</a>
</td>
{if $system.mode!="site"}
<td width="10" height="26" valign="bottom" class="tp_link_button1">|</td>
{assign var="menux" value=$menux+5}
{/if}
{/if}
{assign var="menux" value=$menux+100}
{/if}
{if $menu.site}
{if $system.mode=="site"}
<td width="100" height="26" class="tp_link_button" bgcolor="#F3FCFF" valign="bottom" align="left">
<img width="16" height="16" src="/templates/admin/images/icons/main_site.gif" style="float:left;margin-left:5px;">
&nbsp;Сайт
</td>
{assign var="menux" value=$menux+105}
{else}
{capture name=site_menu}
<table width="100%">
{section name=i loop=$menu.site}
<tr>
<td width="16"><img src="{$menu.site[i].ico}" width="16" height="16"></td>
{if $menu.site[i].close}
<td><a class="cp_link_headding" style="color:red" title="Доступно только в полной версии">{$menu.site[i].name}</a></td>
{else}
<td><a class="cp_link_headding" href="admin.php?mode=site&item={$menu.site[i].item}">{$menu.site[i].name}</a></td>
{/if}
</tr>
{/section}
</table>
{/capture}
<td width="90" height="26" valign="bottom" class="tp_link_button0a" align="left"
{popup sticky=true text=$smarty.capture.site_menu fgcolor="#F3FCFF" bgcolor="86BECD" noclose=true fixx=$menux fixy=44}>
<img width="16" height="16" src="/templates/admin/images/icons/main_site2.gif" style="float:left;margin-left:5px;">
&nbsp;<a href="admin.php?mode=site" class="tp_link_button0">Сайт</a>
</td>
{assign var="menux" value=$menux+90}
{if $system.mode!="files"}
<td width="10" height="26" valign="bottom" class="tp_link_button1">|</td>
{assign var="menux" value=$menux+10}
{/if}
{/if}

{/if}
{if $menu.files}
{if $system.mode=="files"}
<td width="100" height="26" class="tp_link_button" bgcolor="#F3FCFF" valign="bottom" align="left">
<img width="16" height="16" src="/templates/admin/images/icons/main_files.gif" style="float:left;margin-left:5px;">
&nbsp;Файлы
</td>
{assign var="menux" value=$menux+105}
{else}
{capture name=site_files}
<table width="100%">
{section name=i loop=$menu.files}
<tr>
<td width="16"><img src="{$menu.files[i].ico}" width="16" height="16"></td>
<td><a class="cp_link_headding" href="admin.php?mode=files&item={$menu.files[i].item}">{$menu.files[i].name}</a></td>
</tr>
{/section}
</table>
{/capture}
<td width="90" height="26" valign="bottom" class="tp_link_button0a" align="left"
{popup sticky=true text=$smarty.capture.site_files fgcolor="#F3FCFF" bgcolor="86BECD" noclose=true fixx=$menux fixy=44}>
<img width="16" height="16" src="/templates/admin/images/icons/main_files2.gif" style="float:left;margin-left:5px;">
&nbsp;<a {if $auth->isAdmin()}href="admin.php?mode=files"{/if} class="tp_link_button0">Файлы</a>
</td>
{assign var="menux" value=$menux+90}
{if $system.mode!="sections"}
<td width="10" height="26" valign="bottom" class="tp_link_button1">|</td>
{assign var="menux" value=$menux+10}
{/if}
{/if}
{/if}
{if $menu.sections}
{if $system.mode=="sections"}
<td width="100" height="26" class="tp_link_button" bgcolor="#F3FCFF" valign="bottom" align="left">
<img width="16" height="16" src="/templates/admin/images/icons/main_sections.gif" style="float:left;margin-left:5px;">
&nbsp;Разделы
</td>
{assign var="menux" value=$menux+5}
{else}
{capture name=site_sections}
<table width="100%">
{section name=i loop=$menu.sections}
<tr>
<td width="16"><img src="{$menu.sections[i].ico}" width="16" height="16"></td>
<td><a class="cp_link_headding" href="admin.php?mode=sections&item={$menu.sections[i].item}">{$menu.sections[i].name|truncate:25:"...":true}</a></td>
</tr>
{/section}
</table>
{/capture}
<td width="100" height="26" valign="bottom" class="tp_link_button0a" align="left"
{popup sticky=true text=$smarty.capture.site_sections fgcolor="#F3FCFF" bgcolor="86BECD" noclose=true fixx=$menux fixy=44}>
<img width="16" height="16" src="/templates/admin/images/icons/main_sections.gif" style="float:left;margin-left:5px;">
&nbsp;<a href="admin.php?mode=sections" class="tp_link_button0">Разделы</a>
</td>
{if $system.mode!="structures"}
<td width="10" height="26" valign="bottom" class="tp_link_button1">|</td>
{assign var="menux" value=$menux+10}
{/if}
{/if}
{assign var="menux" value=$menux+100}
{/if}
{if $menu.structures}
{if $system.mode=="structures"}
<td width="110" height="26" class="tp_link_button" bgcolor="#F3FCFF" valign="bottom" align="left">
<img width="16" height="16" src="/templates/admin/images/icons/main_structures.gif" style="float:left;margin-left:5px;">
&nbsp;Дополнения
</td>
{assign var="menux" value=$menux+5}
{else}
{capture name=site_structures}
<table width="100%">
{section name=i loop=$menu.structures}
<tr>
<td width="16"><img src="{$menu.structures[i].ico}" width="16" height="16"></td>
<td><a class="cp_link_headding" href="admin.php?mode=structures&item={$menu.structures[i].item}">{$menu.structures[i].name|truncate:25:"...":true}</a></td>
</tr>
{/section}
</table>
{/capture}
<td width="110" height="26" valign="bottom" class="tp_link_button0a" align="left"
{popup sticky=true text=$smarty.capture.site_structures fgcolor="#F3FCFF" bgcolor="86BECD" noclose=true fixx=$menux fixy=44}>
<img width="16" height="16" src="/templates/admin/images/icons/main_structures.gif" style="float:left;margin-left:5px;">
&nbsp;<a href="admin.php?mode=structures" class="tp_link_button0">Дополнения</a>
</td>
<td width="10" height="26" valign="bottom" class="tp_link_button1">|</td>
{/if}
{/if}
<td>&nbsp;</td>
<td width="180" nowrap><img width="16" height="16" src="/templates/admin/images/admin.gif" style="float:left">&nbsp;<span class="tp_link_button2">{$auth->data.name}</span>&nbsp;</td>
<td width="10" height="26" valign="bottom" class="tp_link_button1">|</td>
<td width="60" height="26" valign="bottom" class="tp_link_button0a" align="center"><a href="admin.php?mode=auth&authcode={$system.authcode}&action=logout" class="tp_link_button0">Выход</a></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table width="100%" height="100%">
<tr>
<td width="175" valign="top">
{include file="_leftpanel.tpl"}
</td>
<td valign="top" class="mainbody">
<table width="100%">
<tr>
<td>
{if $caption}
<h1>{$caption}</h1>
{/if}
</td>
<td width="30" align="right"><a href="admin.php" title="Обзор панели управления"><img src="/templates/admin/images/icons/home.gif" width="24" height="24" alt="Обзор панели управления"></a></td>
{if $topageslink}
<td width="30" align="right"><a href="{$topageslink}" title="Шаблоны раздела"><img width="24" height="24" src="/templates/admin/images/pages.gif" alt="Страницы раздела"></a></td>
{/if}
{if $itemstatistic}
<td width="30" align="right"><img width="24" height="24" src="/templates/admin/images/icons/statistic.gif" alt="Статистика" {popup text=$itemstatistic fgcolor="#F3FCFF" caption="Статистика" noclose=true sticky=false bgcolor="86BECD" width=300}></td>
{/if}
{if $system.mode=='sections'}
<td width="30" align="right"><a href="{$system.sectionlink}" target="_blank" title="Просмотр на сайте"><img width="24" height="24" src="/templates/admin/images/icons/browse.gif" alt="Просмотр на сайте"></a></td>
{else}
<td width="30" align="right"><a href="http://{$domain}" target="_blank" title="На главную сайта"><img width="24" height="24" src="/templates/admin/images/icons/browse.gif" alt="На главную сайта"></a></td>
{/if}
<td width="30" align="right">
<a href="http://wiki.a-cms.ru{if $system.module}/modules/{$system.module}{/if}" title="Руководство" target="_blank"><img width="24" height="24" src="/templates/admin/images/icons/help.gif" alt="Руководство"></a>
</td>
</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="95%" bgcolor="#FFFFFF">
<tr>
<td width="5" height="5"><img src="/templates/admin/images/cp_leftc.gif" width="5" height="5" border="0" alt=""></td>
<td style="border-top:1px solid #E1EDE9;"><img src="/templates/admin/images/spacer00.gif" width="1" height="1" border="0"></td>
<td width="5" height="5"><img src="/templates/admin/images/cp_right.gif" width="5" height="5" border="0" alt=""></td>
</tr>
<tr>
<td valign="top" width="1" style="border-left:1px solid #E1EDE9;"><img src="/templates/admin/images/spacer00.gif" width="1" height="1" border="0"></td>
<td valign="top" height="100%" width="100%">
{if $iconeditors}
<div class="box">
{section name=i loop=$iconeditors}
<img src="{$iconeditors[i].ico}" width="16" height="16" border="0" alt="{$iconeditors[i].caption}" style="vertical-align:middle">&nbsp;<a class="cp_link_headding" href="{$iconeditors[i].link}">{$iconeditors[i].caption}</a>{if !$smarty.section.i.last}&nbsp;&nbsp; &nbsp;&nbsp;{/if}
{/section}
</div>
{/if}