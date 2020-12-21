<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 <xsl:template match="/">
  <span id='ChampSelSpan' style="vertical-align:bottom;">
   <select id='ChampSel' onchange="changeChamp( this );" style="margin-top:11px;"><option/>
    <option value="-1">-new-</option>
    <xsl:for-each select="/stlib/Championship">
	 <xsl:sort select="@ChKey" data-type="number" order="descending"/>
     <option>
	  <xsl:if test="@ChKey = /stlib/default/@default">
	   <xsl:attribute name="selected">1</xsl:attribute>
	  </xsl:if>
	  <xsl:attribute name="value"><xsl:value-of select="@ChKey"/></xsl:attribute>
	  <xsl:value-of select="Grade/name"/> - <xsl:value-of select="Season/name"/>
	 </option>
    </xsl:for-each>
   </select>&#160;&#160;<button id='rollOver' onclick="rollOver( );" disabled="1">Roll over championship</button><p/>
   <span id="ChampFixture">
   </span>
  </span>
 </xsl:template>
</xsl:stylesheet>

