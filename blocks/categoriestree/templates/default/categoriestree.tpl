{if $categories}
{literal}
<style>
.dtree {font-family: Verdana;font-size: 11px;color: #666;white-space: nowrap;}
.dtree img {border: 0px;vertical-align: middle;}
.dtree a {color: #333;text-decoration: none;}
.dtree a.node, .dtree a.nodeSel {white-space: nowrap;padding: 1px 2px 1px 2px;}
.dtree a.node:hover, .dtree a.nodeSel:hover {color: #333;text-decoration: underline;}
.dtree a.nodeSel {background-color: #c0d2ec;}
.dtree .clip {overflow: hidden;}
</style>
{/literal}
<script type="text/javascript" src="/blocks/categoriestree/dtree.js"></script>
<div style="margin:5px;">
<script type="text/javascript">
tree = new dTree('tree');
tree.config.useCookies=false;
tree.add(0,-1,'');
{section name=i loop=$categories}
tree.add({$categories[i].id},{$categories[i].idker},'{$categories[i].name}','{$categories[i].link}','','','','',{if $categories[i].selected}true{else}false{/if});
{/section}
document.write(tree);
</script>
</div>
{/if}