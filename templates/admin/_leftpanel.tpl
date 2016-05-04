<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F3FCFF">
<tr>
<td width="5" height="5"><img src="/templates/admin/images/rp_corne.gif" width="5" height="5" border="0" alt=""></td>
<td width="190" style="border-top:1px solid #E1EDE9;"><img src="/templates/admin/images/spacer00.gif" width="1" height="1" border="0"></td>
<td width="5" height="5"><img src="/templates/admin/images/rp_cornf.gif" width="5" height="5" border="0" alt=""></td>
</tr>
<tr>
<td valign="top" width="5" style="border-left:1px solid #E1EDE9;"><img src="/templates/admin/images/spacer00.gif" width="1" height="1" border="0"></td>
<td width="190" valign="top" align="center">
<div align="left" style="margin-top:5;margin-bottom:5;"><font color="1F7F90"><b>УПРАВЛЕНИЕ:</b></font></div>
<center>
<div id="leftmenubox">
{foreach from=$leftmenu item=menuitem}
<table{if $menuitem.id} id="leftmenuitem_{$menuitem.id}"{/if} width="100%" border="0" cellspacing="1" cellpadding="0" bgcolor="#DAEBF0" style="margin-bottom:3">
<tr>
<td align="left" bgcolor="#ffffff" width="20">
<a class="cp_link_headding" href="admin.php?mode={$system.mode}&item={$menuitem.item}">
<img src="{$menuitem.ico}" width="32" height="32" border="0" alt="{$menuitem.name}" style="float:left">
</a>
</td>
<td style="padding:7px" bgcolor="#ffffff" nowrap>
{if $menuitem.close}
<a class="cp_link_headding" style="color:red" title="Доступно только в полной версии">{$menuitem.name|truncate:18:"...":true}</a>
{else}
{if $menuitem.item==$system.item}
<a class="cp_link_headding" href="admin.php?mode={$system.mode}&item={$menuitem.item}" title="{$menuitem.name}"><b>{$menuitem.name|truncate:18:"...":true}</b></a>
{else}
<a class="cp_link_headding" href="admin.php?mode={$system.mode}&item={$menuitem.item}" title="{$menuitem.name}">{$menuitem.name|truncate:18:"...":true}</a>
{/if}
{/if}
</td>
</tr>
</table>
{/foreach}
</div>
{if $system.mode=='sections'}
{literal}<script type="text/javascript">
Sortable.create('leftmenubox',{tag:'table',onUpdate: setsectionssort});
</script>{/literal}
{elseif $system.mode=='structures'}
{literal}<script type="text/javascript">
Sortable.create('leftmenubox',{tag:'table',onUpdate: setstructuressort});
</script>{/literal}
{/if}
</center>
</td>
<td valign="top" width="5" style="border-right:1px solid #E1EDE9;"><img src="/templates/admin/images/spacer00.gif" width="1" height="1" border="0"></td>
</tr>
<tr>
<td width="5" height="5"><img src="/templates/admin/images/rp_corng.gif" width="5" height="5" border="0" alt=""></td>
<td width="190" style="border-bottom:1px solid #E1EDE9;"><img src="/templates/admin/images/spacer00.gif" width="1" height="1" border="0"></td>
<td width="5" height="5"><img src="/templates/admin/images/rp_cornh.gif" width="5" height="5" border="0" alt=""></td>
</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F3FCFF" style="margin-top:5">
<tr>
<td width="5" height="5"><img src="/templates/admin/images/rp_corne.gif" width="5" height="5" border="0" alt=""></td>
<td width="190" style="border-top:1px solid #E1EDE9;"><img src="/templates/admin/images/spacer00.gif" width="1" height="1" border="0"></td>
<td width="5" height="5"><img src="/templates/admin/images/rp_cornf.gif" width="5" height="5" border="0" alt=""></td>
</tr>
<tr>
<td valign="top" width="5" style="border-left:1px solid #E1EDE9;"><img src="/templates/admin/images/spacer00.gif" width="1" height="1" border="0"></td>
<td width="190" valign="top" align="center">
<img src="{$bigimage}" width="150" height="150">
</td>
<td valign="top" width="5" style="border-right:1px solid #E1EDE9;"><img src="/templates/admin/images/spacer00.gif" width="1" height="1" border="0"></td>
</tr>
<tr>
<td width="5" height="5"><img src="/templates/admin/images/rp_corng.gif" width="5" height="5" border="0" alt=""></td>
<td width="190" style="border-bottom:1px solid #E1EDE9;"><img src="/templates/admin/images/spacer00.gif" width="1" height="1" border="0"></td>
<td width="5" height="5"><img src="/templates/admin/images/rp_cornh.gif" width="5" height="5" border="0" alt=""></td>
</tr>
</table>