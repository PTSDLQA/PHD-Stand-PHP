{section name=i loop=$items}
<h3><a href="{$items[i].link}">{$items[i].name}</a></h3>
<p>
{image id=$items[i].idimg width=80 height=80 align="left"}
{$items[i].description}
</p>
<div class="clear"></div>
{/section}
