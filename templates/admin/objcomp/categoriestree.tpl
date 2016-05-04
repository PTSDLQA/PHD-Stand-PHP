{if $treebox->object->items}
{object obj=$treebox}
{else}
<div class="box">Нет категорий</div>
{/if}
<table class="actiongrid">
<tr>
<td align="right">
{button caption="Добавить" onclick="getaddcatform(-1,-1,-1)"}
</td>
</tr>
</table>
<script type="text/javascript">tc_sortable();</script>
