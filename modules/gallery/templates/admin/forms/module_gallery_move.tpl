<form name="moveitemsform" method="post">
<p>Куда:</p>
<p>{treeselect name="idto" items=$form.categories emptytxt="Без категории" title="Выбор категории"}</p>
{section name=i loop=$form.items}
{hidden name="checkitem[]" value=$form.items[i]}
{/section}
{hidden name="idcat" value=$form.idcat}
{hidden name="tab" value="albums"}
{hidden name="tab_albums" value=""}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="authcode" value=$system.authcode}
{hidden name="action" value="moveitems"}
<div align="right" style="margin-top:10px">
{submit caption="OK"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</div>
</form>