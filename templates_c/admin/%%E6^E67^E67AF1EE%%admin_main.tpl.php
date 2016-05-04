<?php /* Smarty version 2.6.26, created on 2013-08-14 16:53:06
         compiled from admin_main.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'regex_replace', 'admin_main.tpl', 29, false),array('modifier', 'truncate', 'admin_main.tpl', 146, false),array('function', 'popup', 'admin_main.tpl', 61, false),)), $this); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Панель управления Astra.CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/templates/admin/style<?php if (strpos ( $_SERVER['HTTP_USER_AGENT'] , 'MSIE' ) !== false): ?>_ie<?php elseif (strpos ( $_SERVER['HTTP_USER_AGENT'] , 'Opera' ) !== false): ?>_opera<?php else: ?>_firefox<?php endif; ?>.css?<?php echo $this->_tpl_vars['options']['version']; ?>
" type="text/css">
<link rel="stylesheet" href="/templates/admin/windows/default.css" type="text/css">
<link rel="stylesheet" href="/templates/admin/windows/alphacube.css" type="text/css">
<link rel="stylesheet" href="/templates/admin/windows/alert.css" type="text/css">
<script type="text/javascript" src="/system/jsaculous/prototype.js"></script>
<script type="text/javascript" src="/system/jsaculous/scriptaculous.js?load=effects,controls,dragdrop"></script>
<script type="text/javascript" src="/system/jsaculous/window.js"></script>
<script type="text/javascript" src="/system/jsaculous/dateselect.js"></script>
<script type="text/javascript" src="/system/jsaculous/sselect.js"></script>
<script type="text/javascript" src="/system/jsoverlib/overlib.js"></script>
<script type="text/javascript" src="/system/jsoverlib/overlib_hideform.js"></script>
<script type="text/javascript" src="/system/jscodemirror/codemirror.js"></script>
<script type="text/javascript" src="/system/jshttprequest/jshttprequest.js"></script>
<?php echo $this->_tpl_vars['jscripts']; ?>
<script type="text/javascript">runLoading();</script>
</head>
<body bgcolor="#F3FCFF">
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td height="15" align="left" width="100%" class="hline">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td width="100" align="center"><a class="cp_link_headding" href="http://a-cms.ru" target="_blank"><b>Astra.CMS Free</b></a></td>
<td valign="middle"><div class="gray"><a class="cp_link_headding" href="http://<?php echo $this->_tpl_vars['domain']; ?>
" target="_blank"><?php echo $this->_tpl_vars['domain']; ?>
</a><?php if ($this->_tpl_vars['site_name']): ?> - <?php echo $this->_tpl_vars['site_name']; ?>
<?php endif; ?></div></td>
<td align="right" valign="middle">v.<?php echo ((is_array($_tmp=$this->_tpl_vars['options']['version'])) ? $this->_run_mod_handler('regex_replace', true, $_tmp, "/([0-9][0-9])$/", ".\\1") : smarty_modifier_regex_replace($_tmp, "/([0-9][0-9])$/", ".\\1")); ?>
&nbsp;</td>
</tr>
</table>
</td>
</tr>
<tr>
<td width="100%" height="26" background="/templates/admin/images/tp_but_g.gif" align="left">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<?php $this->assign('menux', 0); ?>
<?php if ($this->_tpl_vars['menu']['system']): ?>
<?php if ($this->_tpl_vars['system']['mode'] == 'system'): ?>
<td width="100" height="26" class="tp_link_button" bgcolor="#ffffff" valign="bottom" align="left">
<img width="16" height="16" src="/templates/admin/images/icons/main_system.gif" style="float:left;margin-left:5px;">
&nbsp;Система
</td>
<?php else: ?>
<?php ob_start(); ?>
<table width="100%">
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['menu']['system']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
<tr>
<td width="16"><img src="<?php echo $this->_tpl_vars['menu']['system'][$this->_sections['i']['index']]['ico']; ?>
" width="16" height="16"></td>
<?php if ($this->_tpl_vars['menu']['system'][$this->_sections['i']['index']]['close']): ?>
<td><a class="cp_link_headding" style="color:red" title="Доступно только в полной версии"><?php echo $this->_tpl_vars['menu']['system'][$this->_sections['i']['index']]['name']; ?>
</a></td>
<?php else: ?>
<td><a class="cp_link_headding" href="admin.php?mode=system&item=<?php echo $this->_tpl_vars['menu']['system'][$this->_sections['i']['index']]['item']; ?>
"><?php echo $this->_tpl_vars['menu']['system'][$this->_sections['i']['index']]['name']; ?>
</a></td>
<?php endif; ?>
</tr>
<?php endfor; endif; ?>
</table>
<?php $this->_smarty_vars['capture']['system_menu'] = ob_get_contents(); ob_end_clean(); ?>
<td width="100" height="26" valign="bottom" class="tp_link_button0a" align="left"
<?php echo smarty_function_popup(array('sticky' => true,'text' => $this->_smarty_vars['capture']['system_menu'],'fgcolor' => "#F3FCFF",'bgcolor' => '86BECD','noclose' => true,'fixx' => $this->_tpl_vars['menux'],'fixy' => 44), $this);?>
>
<img width="16" height="16" src="/templates/admin/images/icons/main_system2.gif" style="float:left;margin-left:5px;">
&nbsp;<a href="admin.php?mode=system" class="tp_link_button0">Система</a>
</td>
<?php if ($this->_tpl_vars['system']['mode'] != 'site'): ?>
<td width="10" height="26" valign="bottom" class="tp_link_button1">|</td>
<?php $this->assign('menux', $this->_tpl_vars['menux']+5); ?>
<?php endif; ?>
<?php endif; ?>
<?php $this->assign('menux', $this->_tpl_vars['menux']+100); ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['menu']['site']): ?>
<?php if ($this->_tpl_vars['system']['mode'] == 'site'): ?>
<td width="100" height="26" class="tp_link_button" bgcolor="#ffffff" valign="bottom" align="left">
<img width="16" height="16" src="/templates/admin/images/icons/main_site.gif" style="float:left;margin-left:5px;">
&nbsp;Сайт
</td>
<?php else: ?>
<?php ob_start(); ?>
<table width="100%">
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['menu']['site']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
<tr>
<td width="16"><img src="<?php echo $this->_tpl_vars['menu']['site'][$this->_sections['i']['index']]['ico']; ?>
" width="16" height="16"></td>
<?php if ($this->_tpl_vars['menu']['site'][$this->_sections['i']['index']]['close']): ?>
<td><a class="cp_link_headding" style="color:red" title="Доступно только в полной версии"><?php echo $this->_tpl_vars['menu']['site'][$this->_sections['i']['index']]['name']; ?>
</a></td>
<?php else: ?>
<td><a class="cp_link_headding" href="admin.php?mode=site&item=<?php echo $this->_tpl_vars['menu']['site'][$this->_sections['i']['index']]['item']; ?>
"><?php echo $this->_tpl_vars['menu']['site'][$this->_sections['i']['index']]['name']; ?>
</a></td>
<?php endif; ?>
</tr>
<?php endfor; endif; ?>
</table>
<?php $this->_smarty_vars['capture']['site_menu'] = ob_get_contents(); ob_end_clean(); ?>
<td width="100" height="26" valign="bottom" class="tp_link_button0a" align="left"
<?php echo smarty_function_popup(array('sticky' => true,'text' => $this->_smarty_vars['capture']['site_menu'],'fgcolor' => "#F3FCFF",'bgcolor' => '86BECD','noclose' => true,'fixx' => $this->_tpl_vars['menux'],'fixy' => 44), $this);?>
>
<img width="16" height="16" src="/templates/admin/images/icons/main_site2.gif" style="float:left;margin-left:5px;">
&nbsp;<a href="admin.php?mode=site" class="tp_link_button0">Сайт</a>
</td>
<?php if ($this->_tpl_vars['system']['mode'] != 'files'): ?>
<td width="10" height="26" valign="bottom" class="tp_link_button1">|</td>
<?php $this->assign('menux', $this->_tpl_vars['menux']+10); ?>
<?php endif; ?>
<?php endif; ?>
<?php $this->assign('menux', $this->_tpl_vars['menux']+100); ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['menu']['files']): ?>
<?php if ($this->_tpl_vars['system']['mode'] == 'files'): ?>
<td width="100" height="26" class="tp_link_button" bgcolor="#ffffff" valign="bottom" align="left">
<img width="16" height="16" src="/templates/admin/images/icons/main_files.gif" style="float:left;margin-left:5px;">
&nbsp;Файлы
</td>
<?php else: ?>
<?php ob_start(); ?>
<table width="100%">
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['menu']['files']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
<tr>
<td width="16"><img src="<?php echo $this->_tpl_vars['menu']['files'][$this->_sections['i']['index']]['ico']; ?>
" width="16" height="16"></td>
<td><a class="cp_link_headding" href="admin.php?mode=files&item=<?php echo $this->_tpl_vars['menu']['files'][$this->_sections['i']['index']]['item']; ?>
"><?php echo $this->_tpl_vars['menu']['files'][$this->_sections['i']['index']]['name']; ?>
</a></td>
</tr>
<?php endfor; endif; ?>
</table>
<?php $this->_smarty_vars['capture']['site_files'] = ob_get_contents(); ob_end_clean(); ?>
<td width="90" height="26" valign="bottom" class="tp_link_button0a" align="left"
<?php echo smarty_function_popup(array('sticky' => true,'text' => $this->_smarty_vars['capture']['site_files'],'fgcolor' => "#F3FCFF",'bgcolor' => '86BECD','noclose' => true,'fixx' => $this->_tpl_vars['menux'],'fixy' => 44), $this);?>
>
<img width="16" height="16" src="/templates/admin/images/icons/main_files2.gif" style="float:left;margin-left:5px;">
&nbsp;<a <?php if ($this->_tpl_vars['auth']->isAdmin()): ?>href="admin.php?mode=files"<?php endif; ?> class="tp_link_button0">Файлы</a>
</td>
<?php if ($this->_tpl_vars['system']['mode'] != 'sections'): ?>
<td width="10" height="26" valign="bottom" class="tp_link_button1">|</td>
<?php $this->assign('menux', $this->_tpl_vars['menux']+10); ?>
<?php endif; ?>
<?php endif; ?>
<?php $this->assign('menux', $this->_tpl_vars['menux']+95); ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['menu']['sections']): ?>
<?php if ($this->_tpl_vars['system']['mode'] == 'sections'): ?>
<td width="100" height="26" class="tp_link_button" bgcolor="#ffffff" valign="bottom" align="left">
<img width="16" height="16" src="/templates/admin/images/icons/main_sections.gif" style="float:left;margin-left:5px;">
&nbsp;<a href="admin.php?mode=sections" class="cp_link_headding">Разделы</a>
</td>
<?php else: ?>
<?php ob_start(); ?>
<table width="100%">
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['menu']['sections']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
<tr>
<td width="16"><img src="<?php echo $this->_tpl_vars['menu']['sections'][$this->_sections['i']['index']]['ico']; ?>
" width="16" height="16"></td>
<td><a class="cp_link_headding" href="admin.php?mode=sections&item=<?php echo $this->_tpl_vars['menu']['sections'][$this->_sections['i']['index']]['item']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['menu']['sections'][$this->_sections['i']['index']]['name'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 25, "...", true) : smarty_modifier_truncate($_tmp, 25, "...", true)); ?>
</a></td>
</tr>
<?php endfor; endif; ?>
</table>
<?php $this->_smarty_vars['capture']['site_sections'] = ob_get_contents(); ob_end_clean(); ?>
<td width="100" height="26" valign="bottom" class="tp_link_button0a" align="left"
<?php echo smarty_function_popup(array('sticky' => true,'text' => $this->_smarty_vars['capture']['site_sections'],'fgcolor' => "#F3FCFF",'bgcolor' => '86BECD','noclose' => true,'fixx' => $this->_tpl_vars['menux'],'fixy' => 44), $this);?>
>
<img width="16" height="16" src="/templates/admin/images/icons/main_sections.gif" style="float:left;margin-left:5px;">
&nbsp;<a href="admin.php?mode=sections" class="tp_link_button0">Разделы</a>
</td>
<?php if ($this->_tpl_vars['system']['mode'] != 'plugins'): ?>
<td width="10" height="26" valign="bottom" class="tp_link_button1">|</td>
<?php $this->assign('menux', $this->_tpl_vars['menux']+10); ?>
<?php endif; ?>
<?php endif; ?>
<?php $this->assign('menux', $this->_tpl_vars['menux']+100); ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['menu']['structures']): ?>
<?php if ($this->_tpl_vars['system']['mode'] == 'structures'): ?>
<td width="110" height="26" class="tp_link_button" bgcolor="#ffffff" valign="bottom" align="left">
<img width="16" height="16" src="/templates/admin/images/icons/main_structures.gif" style="float:left;margin-left:5px;">
&nbsp;<a href="admin.php?mode=structures" class="cp_link_headding">Дополнения</a>
</td>
<?php else: ?>
<?php ob_start(); ?>
<table width="100%">
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['menu']['structures']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
<tr>
<td width="16"><img src="<?php echo $this->_tpl_vars['menu']['structures'][$this->_sections['i']['index']]['ico']; ?>
" width="16" height="16"></td>
<td><a class="cp_link_headding" href="admin.php?mode=structures&item=<?php echo $this->_tpl_vars['menu']['structures'][$this->_sections['i']['index']]['item']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['menu']['structures'][$this->_sections['i']['index']]['name'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 25, "...", true) : smarty_modifier_truncate($_tmp, 25, "...", true)); ?>
</a></td>
</tr>
<?php endfor; endif; ?>
</table>
<?php $this->_smarty_vars['capture']['site_structures'] = ob_get_contents(); ob_end_clean(); ?>
<td width="110" height="26" valign="bottom" class="tp_link_button0a" align="left"
<?php echo smarty_function_popup(array('sticky' => true,'text' => $this->_smarty_vars['capture']['site_structures'],'fgcolor' => "#F3FCFF",'bgcolor' => '86BECD','noclose' => true,'fixx' => $this->_tpl_vars['menux'],'fixy' => 44), $this);?>
>
<img width="16" height="16" src="/templates/admin/images/icons/main_structures.gif" style="float:left;margin-left:5px;">
&nbsp;<a href="admin.php?mode=structures" class="tp_link_button0">Дополнения</a>
</td>
<td width="10" height="26" valign="bottom" class="tp_link_button1">|</td>
<?php endif; ?>
<?php endif; ?>
<td>&nbsp;</td>
<td width="180" nowrap><img width="16" height="16" src="/templates/admin/images/admin.gif" style="float:left">&nbsp;<span class="tp_link_button2"><?php echo $this->_tpl_vars['auth']->data['name']; ?>
</span>&nbsp;</td>
<td width="10" height="26" valign="bottom" class="tp_link_button1">|</td>
<td width="60" height="26" valign="bottom" class="tp_link_button0a" align="center"><a href="admin.php?mode=auth&authcode=<?php echo $this->_tpl_vars['system']['authcode']; ?>
&action=logout" class="tp_link_button0">Выход</a></td>
</tr>
</table>
</td>
</tr>
<tr>
<td style="padding:10px;">
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="5" height="5"><img src="/templates/admin/images/rp_corne.gif" width="5" height="5" border="0" alt=""></td>
<td style="border-top:1px solid #E1EDE9;"><img src="/templates/admin/images/spacer00.gif" width="1" height="1" border="0"></td>
<td width="5" height="5"><img src="/templates/admin/images/rp_cornf.gif" width="5" height="5" border="0" alt=""></td>
</tr>
<tr>
<td valign="top" width="5" style="border-left:1px solid #E1EDE9;"><img src="/templates/admin/images/spacer00.gif" width="1" height="1" border="0"></td>
<td valign="top" align="left" style="padding:15px">
<div class="mainpanel">
<a href="http://wiki.a-cms.ru" title="Руководство" target="_blank" style="float:right;margin-right:5px;"><img width="24" height="24" src="/templates/admin/images/icons/help.gif" alt="Руководство"></a>
<a href="http://<?php echo $this->_tpl_vars['domain']; ?>
" style="float:right;margin-right:5px;" target="_blank" title="Просмотр сайта"><img width="24" height="24" src="/templates/admin/images/icons/browse.gif" alt="Просмотр сайта"></a>
<ul class="tabs">
<li id="overview" class="overview tab-current"><a>Управление сайтом</a></li>
</ul>
<div id="overview_tab" class="overview_tab">
<table width="100%" height="100%">
<tr>
<td valign="top">
<?php if ($this->_tpl_vars['main']['sections']): ?>
<div style="clear:both;"></div>
<h2 style="margin-top:50px;margin-bottom:10px;"></h2>
<div id="main_sections">
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['main']['sections']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
<table id="section_<?php echo $this->_tpl_vars['main']['sections'][$this->_sections['i']['index']]['id']; ?>
" border="0" cellspacing="1" cellpadding="0" style="margin-bottom:5px;float:left;">
<tr>
<td width="100" height="80" align="center" style="padding:5" valign="top">
<a class="cp_link_headding" href="admin.php?mode=sections&item=<?php echo $this->_tpl_vars['main']['sections'][$this->_sections['i']['index']]['item']; ?>
">
<img src="<?php echo $this->_tpl_vars['main']['sections'][$this->_sections['i']['index']]['ico']; ?>
" width=32 height=32 border="0"
<?php if ($this->_tpl_vars['main']['sections'][$this->_sections['i']['index']]['stat']): ?><?php echo smarty_function_popup(array('text' => $this->_tpl_vars['main']['sections'][$this->_sections['i']['index']]['stat'],'fgcolor' => "#F3FCFF",'caption' => $this->_tpl_vars['main']['sections'][$this->_sections['i']['index']]['name'],'bgcolor' => '86BECD','width' => 300), $this);?>
<?php endif; ?>><br>
<?php echo $this->_tpl_vars['main']['sections'][$this->_sections['i']['index']]['name']; ?>
</a>
</td>
</tr>
</table>
<?php endfor; endif; ?>
</div>
<?php echo '<script type="text/javascript">
Sortable.create(\'main_sections\',{tag:\'table\',constraint:\'horizontal\',onUpdate: setsectionssort});
</script>'; ?>

<?php endif; ?>
<?php if ($this->_tpl_vars['blocks']): ?>
<div style="clear:both;"></div>
<h2 style="margin-top:40px;margin-bottom:10px;"></h2>
<div id="main_blocks">
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['blocks']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
<table id="block_<?php echo $this->_tpl_vars['blocks'][$this->_sections['i']['index']]['id']; ?>
" border="0" cellspacing="1" celzlpadding="0" style="margin-bottom:5px;float:left;">
<tr>
<td width="100" height="80" align="center" style="padding:5" valign="top">
<a class="cp_link_headding" href="<?php echo $this->_tpl_vars['blocks'][$this->_sections['i']['index']]['link']; ?>
">
<img src="<?php echo $this->_tpl_vars['blocks'][$this->_sections['i']['index']]['ico']; ?>
" width=32 height=32 border="0"><br>
<?php echo $this->_tpl_vars['blocks'][$this->_sections['i']['index']]['caption']; ?>
</a>
</td>
</tr>
</table>
<?php endfor; endif; ?>
</div>
<?php echo '<script type="text/javascript">
Sortable.create(\'main_blocks\',{tag:\'table\',constraint:\'horizontal\',onUpdate: setblockssort});
</script>'; ?>

<?php endif; ?>
</td>
</tr>
</table>
</div>
</div>
</td>
</tr>
</td>
<td valign="top" width="5" style="border-right:1px solid #E1EDE9;"><img src="/templates/admin/images/spacer00.gif" width="1" height="1" border="0"></td>
</tr>
<tr>
<td width="5" height="5"><img src="/templates/admin/images/rp_corng.gif" width="5" height="5" border="0" alt=""></td>
<td style="border-bottom:1px solid #E1EDE9;"><img src="/templates/admin/images/spacer00.gif" width="1" height="1" border="0"></td>
<td width="5" height="5"><img src="/templates/admin/images/rp_cornh.gif" width="5" height="5" border="0" alt=""></td>
</tr>
</table>
</td>
</tr>
</table>
<div id="debugbox"></div>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<script type="text/javascript">
addEvent(window,'load',endLoading, false);
window.name='mainadmin';
</script>
</body>
</html>