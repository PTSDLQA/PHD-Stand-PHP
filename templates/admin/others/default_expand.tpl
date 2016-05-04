<div id="treeitem_{$idcat}"{if strpos($smarty.server.HTTP_USER_AGENT,"MSIE")!==false} style="position:fixed"{/if}>
<table class="expand" cellpadding=0 cellspacing=0>
<tr{if !$active} class="close"{/if}>
<td width="16">{if $bexpand}<img id="{$idpic}" hspace="2" src="/templates/admin/images/collapse.gif" width="9" height="9" onclick="{$jfun}" style="cursor:pointer">{else}&nbsp;{/if}</td>
{if $idimg}<td width="20">{capture name=cimage}{image id=$idimg width=150}{/capture}<img src="/templates/admin/images/image.gif" width="16" height="16" {popup text=$smarty.capture.cimage fgcolor="#F3FCFF" width=150 bgcolor="#86BECD" left=true}></td>{/if}
{if $selected}<td><b>{$title}{if $count}  ({$count}){/if}</b></td>{else}<td>{$title}{if $count}  ({$count}){/if}</td>{/if}
{if $bedit}
<td width="25" align="center"><a href="javascript:getaddcatform({$idcat},{$idker},{$level})" title="Добавить подкатегорию"><img src="/templates/admin/images/add.gif" width="16" height="16" alt="Добавить подкатегорию"></a></td>
<td width="25" align="center">{$bedit}</td>
<td width="25" align="center">{if $blink}<a href="{$blink}" target="_blank" title="Просмотр на сайте"><img src="/templates/admin/images/browse.gif" width="16" height="16" alt="Просмотр на сайте"></a>{else}&nbsp;{/if}</td>
{if $seo}<td width="25" align="center"><a href="javascript:geturlseoform('{$blink}')" title="SEO оптимизация"><img src="/templates/admin/images/addpage.gif" width="16" height="16"></a></td>{/if}
<td width="25" align="center"><a href="javascript:getmovecatform({$idcat})" title="Переместить"><img src="/templates/admin/images/move.gif" width="16" height="16" alt="Переместить"></a></td>
{/if}
{if $bdel}<td width="20" align="center">{$bdel}</td>{/if}
</tr>
</table>
<input type="hidden" id="treeiteml_{$idcat}" value="{$level}">
<div id="{$id}" style="display:none" class="expand_content">
{$content}
</div>
</div>