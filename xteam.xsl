<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 <xsl:template match="/">
  <span>
   <span class='dlgTeamSelSpan'>
    <select onchange="changeTeam( this );" style="width:150px;"><option/>
     <option value="-1">-new-</option>
     <xsl:for-each select="/stteam/team">
	  <xsl:sort select="@key" data-type="number"/>
      <option>
	   <xsl:attribute name="value"><xsl:value-of select="@key"/></xsl:attribute>
	   <xsl:value-of select="teamname"/>
	  </option>
     </xsl:for-each>
    </select>
   </span>
   <span class='dlgTeamDerivSelSpan' style="display:none;">
    <select onchange="changeTeamDeriv( this );" style="width:150px;">
     <option/>
	 <option value='F'>Ladder pos:</option>
	 <option value='W'>Winner match:</option>
	 <option value='L'>Loser match:</option>
    </select>
   </span>
   <span class='dlgTeamDerivSelRank' style="display:none;">
    <select class='LadderPosSel'>
	 <option value='-1'></option>
	 <xsl:if test="/stteam/numfinalists">
	  <xsl:call-template name="PopulateNumFinalists">
	   <xsl:with-param name="curr" select="1"/>
       <xsl:with-param name="end" select="/stteam/numfinalists"/>
	  </xsl:call-template>
	 </xsl:if>
	 <xsl:if test="not(/stteam/numfinalists)">
	  <xsl:call-template name="PopulateNumFinalists">
	   <xsl:with-param name="curr" select="1"/>
       <xsl:with-param name="end" select="count(/stteam/team)"/>
	  </xsl:call-template>
	 </xsl:if>
    </select>
    <select class='WinLose'>
	 <option value='-1'></option>
	 <xsl:for-each select="/stteam/fround/fseries">
	  <option><xsl:attribute name="value"><xsl:value-of select="@sno"/></xsl:attribute><xsl:value-of select="sername"/></option>
	 </xsl:for-each>
    </select>
   </span>
  </span>
 </xsl:template>
 <xsl:template name="PopulateNumFinalists">
  <xsl:param name="curr"/>
  <xsl:param name="end"/>
  <option><xsl:attribute name="value"><xsl:value-of select="$curr"/></xsl:attribute><xsl:value-of select="$curr"/></option>
  <xsl:if test="$curr &lt; $end">
   <xsl:call-template name="PopulateNumFinalists">
    <xsl:with-param name="curr" select="$curr + 1"/>
	<xsl:with-param name="end" select="$end"/>
   </xsl:call-template>
  </xsl:if>
 </xsl:template>
</xsl:stylesheet>

