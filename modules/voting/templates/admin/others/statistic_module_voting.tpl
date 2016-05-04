{if $datebegin && $dateend}
<p>Период: <b>{$datebegin|date_format:"%d.%m.%Y"}</b>&nbsp;-&nbsp;<b>{$dateend|date_format:"%d.%m.%Y"}</b></p>
{else}
<p>Период: <b>Не указан</b></p>
{/if}
<p>Статус:&nbsp;
{if $status==1}
<font color="#ff0000"><b>Завершен</b></font>
{elseif $status==2}
<font color="#ff9933"><b>Неактивен</b></font>
{else}
<font color="#008000"><b>В процессе</b></font>
{/if}
</p>
{if $status!=2}
<p>Всего ответило: <b>{if $vote_count}{$vote_count}{else}0{/if}</b></p>
{/if}