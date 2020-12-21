<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 <xsl:template match="/">
  <select onchange="changeVenue( this );" style="width:150px;"><option/>
   <option value="-1">-new-</option>
   <xsl:for-each select="/stteam/venue">
	<xsl:sort select="@sort" data-type="number"/>
    <option>
	 <xsl:attribute name="value"><xsl:value-of select="@key"/></xsl:attribute>
	 <xsl:value-of select="name"/>
	</option>
   </xsl:for-each>
  </select>
 </xsl:template>
</xsl:stylesheet>

