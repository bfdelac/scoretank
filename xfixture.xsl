<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 <xsl:template match="/">
  <table id="matchtable" border="1" style="font-size:small;">
   <tr>
	<xsl:if test="/stteam/editmatch">
     <th>Edit</th>
	</xsl:if>
    <th>Round</th>
    <th>Match</th>
    <th>Home</th>
	<th></th>
    <th>Away</th>
    <th>Venue</th>
    <th>Sched</th>
   </tr>
   <xsl:for-each select="/stteam/match">
	<xsl:sort select="@r" data-type="number"/>
	<xsl:sort select="@m" data-type="number"/>
    <tr class="displayrow">
	 <xsl:if test="/stteam/editmatch">
      <td><button>
	   <xsl:attribute name="onclick">EditMatch( <xsl:value-of select="@r"/>, <xsl:value-of select="@m"/>, <xsl:value-of select="hteam/@key"/>, <xsl:value-of select="ateam/@key"/>, <xsl:value-of select="venue/@key"/>, '<xsl:value-of select="sched"/>' );</xsl:attribute>
	   Edit</button></td>
	 </xsl:if>
	 <td class="roundnum" align="right"><xsl:value-of select="@r"/></td>
	 <td class="matchnum" align="right"><xsl:value-of select="@m"/></td>
	 <td><xsl:value-of select="hteam"/></td>
	 <td>vs</td>
	 <td><xsl:value-of select="ateam"/></td>
	 <td><xsl:value-of select="venue"/></td>
	 <td>
	  <xsl:if test="ateam/@key &gt; 0">
	   <span class="textdate"><xsl:value-of select="textdate"/></span>&#160;<span class="texttime"><xsl:value-of select="texttime"/></span>
	  </xsl:if>
	 </td>
	</tr>
   </xsl:for-each>
   <tr>
	<xsl:if test="/stteam/editmatch">
	 <td>&#160;</td>
	</xsl:if>
    <td style="vertical-align: text-top;">
	 <input type="text" id="roundnum" size="3" maxlength="2" readonly="1" width="30px" style="text-align:right;width:30px;"></input>
	 <br/>
	 <button onclick="newRound( );">New</button>
	</td>
	<td style="vertical-align: text-top;"><input type="text" id="matchnum" size="3" maxlength="2" readonly="1" width="30px" style="text-align:right;width:30px;"></input></td>
	<td style="vertical-align: text-top;"><span id="hteam"></span></td>
	<td/>
	<td style="vertical-align: text-top;" class="ateamtd"><span id="ateam"></span></td>
	<td style="vertical-align: text-top;" class="venuetd"><span id="venue"></span></td>
	<td style="vertical-align: text-top;">
	 <input type="text" id="matchdate" style="width:150px;"></input><br/>
	 <input type="text" id="matchtime" value="19:30" style="width:150px;"></input><br/>
	 <button onclick="SaveMatch( );">Save Match</button></td>
   </tr>
   <xsl:if test="/stteam/fround">
    <tr>
	 <xsl:if test="/stteam/editmatch">
	  <th colspan="8">FINALS</th>
	 </xsl:if>
	 <xsl:if test="not(/stteam/editmatch)">
	  <th colspan="7">Finals</th>
	 </xsl:if>
    </tr>
	<xsl:if test="/stteam/editmatch">
	 <xsl:for-each select="/stteam/fround">
	  <xsl:sort select="@r" data-type="number"/>
      <tr>
	   <th colspan="8">Finals Round <xsl:value-of select="@r"/>
	    <xsl:if test="frname">: <xsl:value-of select="frname"/>
		</xsl:if>
	   </th>
	  </tr>
	  <xsl:for-each select="fseries">
	   <xsl:sort select="@sno"/>
	   <tr class="displayrow">
	    <xsl:if test="/stteam/editmatch">
         <td><button>
	      <xsl:attribute name="onclick">EditMatch( <xsl:value-of select="fmatch/@r"/>,
		  		<xsl:value-of select="@sno"/>,
				<xsl:value-of select="hteam/@key"/><xsl:if test="not(hteam)">-1</xsl:if>,
				<xsl:value-of select="ateam/@key"/><xsl:if test="not(ateam)">-1</xsl:if>,
				<xsl:value-of select="fmatch/venue/@key"/>,
				'<xsl:value-of select="fmatch/sched"/>',
				'<xsl:value-of select="@hd"/>',
				<xsl:value-of select="@hdr"/>,
				'<xsl:value-of select="@ad"/>',
				<xsl:value-of select="@adr"/> );</xsl:attribute>
	      Edit</button></td>
	     </xsl:if>
	    <td><xsl:value-of select="../@r"/></td>
	    <td><xsl:value-of select="sername"/></td>
	    <td><xsl:if test="@hd='F'">Ladder pos: <xsl:value-of select="@hdr"/></xsl:if>
		    <xsl:if test="@hd='W'">Winner: <xsl:call-template name="DerivFinal"><xsl:with-param name="sno" select="@hdr"/></xsl:call-template></xsl:if>
			<xsl:if test="@hd='L'">Loser: <xsl:call-template name="DerivFinal"><xsl:with-param name="sno" select="@hdr"/></xsl:call-template></xsl:if>
			<xsl:if test="hteam"><br/><xsl:value-of select="hteam"/></xsl:if></td>
	    <td>vs</td>
	    <td><xsl:if test="@ad='F'">Ladder pos: <xsl:value-of select="@adr"/></xsl:if>
		    <xsl:if test="@ad='W'">Winner: <xsl:call-template name="DerivFinal"><xsl:with-param name="sno" select="@adr"/></xsl:call-template></xsl:if>
			<xsl:if test="@ad='L'">Loser: <xsl:call-template name="DerivFinal"><xsl:with-param name="sno" select="@adr"/></xsl:call-template></xsl:if>
			<xsl:if test="ateam"><br/><xsl:value-of select="ateam"/></xsl:if></td>
	    <td><xsl:value-of select="fmatch/venue"/></td>
	    <td>
	     <span class="textdate"><xsl:value-of select="fmatch/textdate"/></span>&#160;<span class="texttime"><xsl:value-of select="fmatch/texttime"/></span>
	    </td>
	   </tr>
	  </xsl:for-each>
	 </xsl:for-each>
	</xsl:if>
   </xsl:if>
  </table>
 </xsl:template>

 <xsl:template name="DerivFinal">
  <xsl:param name="sno"/>
  <xsl:for-each select="/stteam/fround/fseries[@sno=$sno]">
   <xsl:value-of select="sername"/>
  </xsl:for-each>
 </xsl:template>
</xsl:stylesheet>

