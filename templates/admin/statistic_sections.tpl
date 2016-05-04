{include file="_header.tpl"}

{section name=i loop=$statistics}
<h3>{$statistics[i].name}:</h3>
<div class="box">
{$statistics[i].block->getContent()}
</div>
{/section}

{include file="_footer.tpl"}