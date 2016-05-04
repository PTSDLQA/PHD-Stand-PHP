<form name="addcommentform" method="post" onsubmit="return comment_form(this)">
<p>Имя:<sup style="color:gray">*</sup></p>
<p>{editbox name="name" width="40%" tabindex="1"}</p>
<p>&nbsp;</p>
<div style="float:right">
<a href="javascript:addSmile(';)')"><img width="20" height="20" src="/templates/admin/images/smiles/wink.gif" alt="wink" style="vertical-align:middle;"></a>
<a href="javascript:addSmile(':)')"><img width="20" height="20" src="/templates/admin/images/smiles/smile.gif" alt="smile" style="vertical-align:middle;"></a>
<a href="javascript:addSmile(':P')"><img width="20" height="20" src="/templates/admin/images/smiles/tongue.gif" alt="tongue" style="vertical-align:middle;"></a>
<a href="javascript:addSmile(':D')"><img width="20" height="20" src="/templates/admin/images/smiles/biggrin.gif" alt="biggrin" style="vertical-align:middle;"></a>
<a href="javascript:addSmile(':lol:')"><img width="20" height="20" src="/templates/admin/images/smiles/lol.gif" alt="lol" style="vertical-align:middle;"></a>
<a href="javascript:addSmile('-_-')"><img width="20" height="20" src="/templates/admin/images/smiles/closedeyes.gif" alt="closedeyes" style="vertical-align:middle;"></a>
<a href="javascript:addSmile('(_(')"><img width="20" height="20" src="/templates/admin/images/smiles/glare.gif" alt="glare" style="vertical-align:middle;"></a>
<a href="javascript:addSmile(':huh:')"><img width="20" height="20" src="/templates/admin/images/smiles/huh.gif" alt="huh" style="vertical-align:middle;"></a>
<a href="javascript:addSmile(':(')"><img width="20" height="20" src="/templates/admin/images/smiles/sad.gif" alt="sad" style="vertical-align:middle;"></a>
<a href="javascript:addSmile(':angry:')"><img width="20" height="20" src="/templates/admin/images/smiles/angry.gif" alt="angry" style="vertical-align:middle;"></a>
<a href="javascript:addSmile('B)')"><img width="20" height="20" src="/templates/admin/images/smiles/cool.gif" alt="cool" style="vertical-align:middle;"></a>
<a href="javascript:addSmile(':unsure:')"><img width="20" height="20" src="/templates/admin/images/smiles/unsure.gif" alt="unsure" style="vertical-align:middle;"></a>
<a href="javascript:addSmile(':o')"><img width="20" height="20" src="/templates/admin/images/smiles/ohmy.gif" alt="ohmy" style="vertical-align:middle;"></a>
<a href="javascript:addSmile(':blink:')"><img width="20" height="20" src="/templates/admin/images/smiles/blink.gif" alt="blink" style="vertical-align:middle;"></a>
<a href="javascript:addSmile(':shok:')"><img width="20" height="20" src="/templates/admin/images/smiles/shok.gif" alt="shok" style="vertical-align:middle;"></a>
</div>
<input type="button" value=" B " onclick="addTag('b')" style="width:20px;height:20px;">&nbsp;&nbsp;
<input type="button" value=" I " onclick="addTag('i')" style="width:20px;height:20px;">&nbsp;&nbsp;
<input type="button" value=" U " onclick="addTag('u')" style="width:20px;height:20px;">&nbsp;&nbsp;
<div><textarea id="message" name="bbcode" rows="6" tabindex="2" style="width:100%;margin-top:5px;">{$form.bbcode|escape}</textarea></div>
{hidden name="iditem" value=$form.iditemcomm}
{hidden name="mode" value=$system.mode}
{hidden name="item" value=$system.item}
{hidden name="obj_action" value="comm_add"}
{hidden name="authcode" value=$system.authcode}
<div align="right" style="margin-top:10px">
<p style="float:left"><label><input type="checkbox" name="active" checked>&nbsp;Размещен</label></p>
{submit caption="OK" tabindex="3"}
{button caption="Отмена" onclick="Windows.closeAll()" tabindex="4"}
</div>
</form>