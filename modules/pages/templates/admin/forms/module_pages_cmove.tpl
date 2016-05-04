<form name="moveform" method="post" onsubmit="return moveitem(this)">
<p>Куда:</p>
{if $form.idker>0}
<p>{treeselect name="idto" selected=-1 items=$form.dirs emptytxt="Корень" title="Выбор подраздела"}</p>
{else}
<p>{treeselect name="idto" selected=-1 items=$form.dirs emptytxt="" title="Выбор подраздела"}</p>
{/if}
{section name=i loop=$form.pages}
{hidden name="checkpages[]" value=$form.pages[i]}
{/section}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="page" value=$system.page}
{hidden name="authcode" value=$system.authcode}
{hidden name="action" value="cmove"}
<div align="right" style="margin-top:10px">
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>
