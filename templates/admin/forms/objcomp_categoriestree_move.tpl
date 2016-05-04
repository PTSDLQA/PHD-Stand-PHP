<form name="movecatform" method="post" onsubmit="return movecat(this)">
<p>Куда:</p>
{if $form.idker>0}
<p>{treeselect name="idto" selected=-1 items=$form.categories emptytxt="Корень" title="Выбор категории"}</p>
{else}
<p>{treeselect name="idto" selected=-1 items=$form.categories emptytxt="" title="Выбор категории"}</p>
{/if}
<input type="hidden" name="id" value="{$form.id}">
<input type="hidden" name="tab" value="cat">
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="obj_action" value="ct_move"}
{hidden name="authcode" value=$system.authcode}
<div align="right" style="margin-top:10px">
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>