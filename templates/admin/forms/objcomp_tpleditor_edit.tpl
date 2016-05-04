<form name="editfileform" method="post">
<div class="box">
<textarea id="codearea" name="text" style="width:100%;height:{if $form.warning}580{else}650{/if}px">
{$form.text|escape:"html"}
</textarea>
</div>
<div align="right" style="margin-top:10px">
<p style="float:left">
<a href="javascript:applytpl(document.forms.editfileform)" title="Сохранить не закрывая"><img src="/templates/admin/images/save.gif" width="16" height="16" style="vertical-align:middle"></a>
{if $form.tpls}
&nbsp;&nbsp;Сопутствующие шаблоны:&nbsp;
<select onchange="if(this.value) gotpl(this.value);">
<option value="">-</option>
{html_options options=$form.tpls}
</select>
{/if}
</p>
<p style="float:right">
{button class="submit" caption="OK" onclick="savetpl(this.form)"}
{button caption="Отмена" onclick="Windows.closeAll()"}
</p>
</div>
{hidden name="path" value=$form.path}
{hidden name="authcode" value=$system.authcode}
</form>