<?xml version="1.0" encoding="utf-8"?>

<!--

 *** FlyWeb Components: SMARTSELECT ***

 # This library provides many useful feautures for simple creating and working with COMBO-BOX form field as follows:
 # HTML/CSS styling and/or XSLT design in accordance with your desire, autofill, multiple selection, AJAX-loading, etc.

 # Full documentation, last version download and forum are avaible at http://flyweb.in/fwc.
 # All comments and bugs notifications are welcome.

 # @autor Alexander Zinchuk | Alx | alx.vingrad.ru
 # @version 2.7

 # Have a nice day ;) Jah love.

-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                              xmlns:fwc="http://alx.vingrad.ru/fwc" >

<xsl:output method="html" encoding="utf-8" indent="yes" />

<xsl:template match="fwc:select">
  <xsl:param name="mode">
    <xsl:choose>
      <xsl:when test="boolean(@mode)"><xsl:value-of select="@mode"/></xsl:when>
      <xsl:otherwise>combo</xsl:otherwise>
    </xsl:choose>
  </xsl:param>
  <xsl:param name="skinpath">
    <xsl:choose>
      <xsl:when test="contains(@skin,'ss_')">fwc/skins/<xsl:value-of select="@skin"/>/skin</xsl:when>
      <xsl:when test="boolean(@skin)"><xsl:value-of select="@path"/><xsl:value-of select="@skin"/></xsl:when>
    </xsl:choose>
  </xsl:param>
  <xsl:param name="skin"><xsl:value-of select="@skin"/></xsl:param>
  <xsl:param name="design">
    <xsl:choose>
      <xsl:when test="boolean(@design)"><xsl:value-of select="@path"/><xsl:value-of select="@design"/></xsl:when>
      <xsl:otherwise>fwc/design/design.xsl</xsl:otherwise>
    </xsl:choose>
  </xsl:param>
  <xsl:param name="selectTitle">
    <xsl:choose>
      <xsl:when test="boolean(@title)"><xsl:value-of select="@title"/></xsl:when>
      <xsl:when test="$mode != 'text'">
        <xsl:choose>
          <xsl:when test="count(.//fwc:option[@selected]) = 0"><xsl:value-of select=".//fwc:option"/></xsl:when>
          <xsl:when test="count(.//fwc:option[@selected]) = 1"><xsl:value-of select=".//fwc:option[@selected]"/></xsl:when>
          <xsl:when test="count(.//fwc:option[@selected]) &gt; 1">
            <xsl:for-each select=".//fwc:option[@selected]"><xsl:value-of select="."/><xsl:if test="position() &lt; count(.//fwc:option[@selected])">,</xsl:if></xsl:for-each>
          </xsl:when>
        </xsl:choose>
      </xsl:when>
    </xsl:choose>
  </xsl:param>
  <xsl:param name="onclick">
    <xsl:choose>
      <xsl:when test="@readonly = 'true' or $mode = 'select'"><xsl:value-of select="@id"/>.showHideOptions();return false;</xsl:when>
      <xsl:otherwise>(<xsl:value-of select="@id"/>.mode=='text'&amp;&amp;this.value==<xsl:value-of select="@id"/>.selectTitle)?this.value='':this.select();</xsl:otherwise>
    </xsl:choose>
  </xsl:param>
  <xsl:param name="size">
    <xsl:choose>
      <xsl:when test="boolean(@size)"><xsl:value-of select="@size"/></xsl:when>
      <xsl:when test="$mode != 'text'">
        <xsl:for-each select=".//*[text()]">
          <xsl:sort select="string-length(.)" order="descending" data-type="number"/>
          <xsl:if test="position() = 1">
            <xsl:if test="string-length(.) &gt;= string-length($selectTitle)"><xsl:value-of select="string-length(.)"/></xsl:if>
            <xsl:if test="string-length(.) &lt; string-length($selectTitle)"><xsl:value-of select="string-length($selectTitle)"/></xsl:if>
          </xsl:if>
        </xsl:for-each>
      </xsl:when>
      <xsl:otherwise>18</xsl:otherwise>
    </xsl:choose>
  </xsl:param>
  <span>
  <div class="{$skin}_SelectDiv fwcss" id="{@id}_select" align="left" onkeydown="{@id}.titleKeyDown(event)" style="visibility:hidden;">
    <nobr>
      <xsl:element name="input">
        <xsl:attribute name="id"><xsl:value-of select="@id"/>_title</xsl:attribute>
        <xsl:attribute name="type">text</xsl:attribute>
        <xsl:attribute name="autocomplete">off</xsl:attribute>
        <xsl:attribute name="size"><xsl:value-of select="$size+2"/></xsl:attribute>
        <xsl:attribute name="class"><xsl:value-of select="$skin"/>_Title</xsl:attribute>
        <xsl:attribute name="onclick"><xsl:value-of select="$onclick"/></xsl:attribute>
        <xsl:if test="$mode = 'combo'"><xsl:attribute name="ondblclick"><xsl:value-of select="@id"/>.showHideOptions();return false;</xsl:attribute></xsl:if>
        <xsl:if test="@readonly = 'true' or $mode = 'select'">
          <xsl:attribute name="onmouseover"><xsl:value-of select="@id"/>.titleButtonRollOver();</xsl:attribute>
          <xsl:attribute name="onmouseout"><xsl:value-of select="@id"/>.titleButtonRollOut();</xsl:attribute>
          <xsl:attribute name="onmousedown"><xsl:value-of select="@id"/>.titleRollUpDown('down');</xsl:attribute>
          <xsl:attribute name="onmouseup"><xsl:value-of select="@id"/>.titleRollUpDown('up');</xsl:attribute>
        </xsl:if>
        <xsl:attribute name="onblur"><xsl:value-of select="@id"/>.titleBlur();</xsl:attribute>
        <xsl:attribute name="onkeyup"><xsl:value-of select="@id"/>.ontype(event);</xsl:attribute>
        <xsl:attribute name="value"><xsl:value-of select="$selectTitle"/></xsl:attribute>
        <xsl:if test="@readonly = 'true' or $mode = 'select'"><xsl:attribute name="readonly"/></xsl:if>
      </xsl:element>
      <xsl:if test="$mode != 'text'">
        <xsl:element name="input">
          <xsl:attribute name="id"><xsl:value-of select="@id"/>_tbutton</xsl:attribute>
          <xsl:attribute name="size">1</xsl:attribute>
          <xsl:attribute name="class"><xsl:value-of select="$skin"/>_Button</xsl:attribute>
          <xsl:attribute name="onclick"><xsl:value-of select="@id"/>.showHideOptions();return false;</xsl:attribute>
          <xsl:attribute name="onmouseover"><xsl:value-of select="@id"/>.titleButtonRollOver();</xsl:attribute>
          <xsl:attribute name="onmouseout"><xsl:value-of select="@id"/>.titleButtonRollOut();</xsl:attribute>
          <xsl:attribute name="onmousedown"><xsl:value-of select="@id"/>.titleRollUpDown('down');return false;</xsl:attribute>
          <xsl:attribute name="onmouseup"><xsl:value-of select="@id"/>.titleRollUpDown('up');return false;</xsl:attribute>
          <xsl:attribute name="maxlength">0</xsl:attribute>
          <xsl:attribute name="readonly">true</xsl:attribute>
        </xsl:element>
      </xsl:if>
    </nobr>
    <div class="{$skin}_OptionsDiv" style="display:none;visibility:hidden;overflow:hidden;height:auto;" id="{@id}_options">
      <xsl:apply-templates>
        <xsl:with-param name="skin"><xsl:value-of select="$skin"/></xsl:with-param>
        <xsl:with-param name="id"><xsl:value-of select="@id"/></xsl:with-param>
      </xsl:apply-templates>
    </div>
    <span id="{@id}_inform" style="display:none;">for input:hidden</span>
  </div>
  </span>
  <script language="JavaScript" type="text/javascript" class="{@id}_script">
  if(!FWC.initSSarr['<xsl:value-of select="@id"/>']) FWC.initSSarr['<xsl:value-of select="@id"/>'] = function() {
   <xsl:value-of select="@id"/> = new FWC_sSelect(
         '<xsl:value-of select="@id"/>',
         '<xsl:value-of select="$mode"/>',
         '<xsl:value-of select="$skinpath"/>',
         '<xsl:value-of select="$design"/>',
         '<xsl:value-of select="$skin"/>',
         '<xsl:value-of select="$selectTitle"/>',
         '<xsl:value-of select="@multiple"/>',
         '<xsl:value-of select="@readonly"/>',
         '<xsl:value-of select="@ownvalues"/>',
         <xsl:value-of select="number(@fillstart)"/>,
         (<xsl:value-of select="boolean(@ontype)"/> ? function() {<xsl:value-of select="@ontype"/>} : false),
         '<xsl:value-of select="@fewprefix"/>',
         '<xsl:value-of select="@fewall"/>',
         '<xsl:value-of select="@splitter"/>',
         '<xsl:value-of select="@maxheight"/>',
         '<xsl:value-of select="@usectrlkey"/>',
         (<xsl:value-of select="boolean(@onchange)"/> ? function() {<xsl:value-of select="@onchange"/>} : false),
         (<xsl:value-of select="boolean(@onclose)"/> ? function() {<xsl:value-of select="@onclose"/>} : false),
         (<xsl:value-of select="boolean(@oninit)"/> ? function() {<xsl:value-of select="@oninit"/>} : false),
         '<xsl:value-of select="@showspeed"/>',
         <xsl:value-of select="number(@hidespeed)"/>,
         <xsl:value-of select="count(//fwc:select/*//fwc:option) > 0"/>);
  }
  Event.observe(window,'load',FWC.initSSarr['<xsl:value-of select="@id"/>']);
  </script>
</xsl:template>

<xsl:template match="//fwc:option">
  <xsl:param name="skin"/>
  <xsl:param name="id"/>
  <xsl:param name="selected">
    <xsl:choose>
      <xsl:when test="@selected = 'true'">Sel</xsl:when>
      <xsl:otherwise>Free</xsl:otherwise>
    </xsl:choose>
  </xsl:param>
  <xsl:param name="value">
    <xsl:choose>
      <xsl:when test="boolean(@value)"><xsl:value-of select="@value"/></xsl:when>
      <xsl:otherwise><xsl:apply-templates/></xsl:otherwise>
    </xsl:choose>
  </xsl:param>
  <xsl:element name="span">
    <xsl:attribute name="val"><xsl:value-of select="$value"/></xsl:attribute>
    <xsl:attribute name="class"><xsl:value-of select="$skin"/>_Option_<xsl:value-of select="$selected"/>_Out <xsl:value-of select="@class"/></xsl:attribute>
    <xsl:attribute name="onclick"><xsl:value-of select="$id"/>.selectOption(this,false,event);</xsl:attribute>
    <xsl:attribute name="onmouseover"><xsl:value-of select="$id"/>.highlightOption(this);</xsl:attribute>
    <xsl:for-each select="@*[name(.) != 'value' and name(.) != 'class']">
      <xsl:attribute name="{name(.)}"><xsl:value-of select="."/></xsl:attribute>
    </xsl:for-each>
    <nobr>
    <xsl:apply-templates>
      <xsl:with-param name="skin"><xsl:value-of select="$skin"/></xsl:with-param>
      <xsl:with-param name="id"><xsl:value-of select="$id"/></xsl:with-param>
    </xsl:apply-templates>
    </nobr>
  </xsl:element>
</xsl:template>

<xsl:template match="*">
  <xsl:param name="skin"/>
  <xsl:param name="id"/>
  <xsl:element name="{name(.)}">
    <xsl:for-each select="@*">
      <xsl:attribute name="{name(.)}"><xsl:value-of select="."/></xsl:attribute>
    </xsl:for-each>
    <xsl:apply-templates>
      <xsl:with-param name="skin"><xsl:value-of select="$skin"/></xsl:with-param>
      <xsl:with-param name="id"><xsl:value-of select="$id"/></xsl:with-param>
    </xsl:apply-templates>
  </xsl:element>
</xsl:template>

</xsl:stylesheet>
