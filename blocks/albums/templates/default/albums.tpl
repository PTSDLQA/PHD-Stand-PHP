{section name=i loop=$albums}
<h3><a href="{$albums[i].link}">{$albums[i].name}</a></h3>
{image id=$albums[i].idimg width=80 height=80 align="center"}
{/section}