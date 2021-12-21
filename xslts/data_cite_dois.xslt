<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="xml" indent="yes"/>  
  <xsl:template match="/">
  <xsl:variable name="wosid"><xsl:value-of select="//KeyUT"/></xsl:variable> 
    <results>
    	<xsl:for-each select="/response/data/item">
    		<item>
     			<doi><xsl:value-of select="attributes/doi"/></doi>
     			<url><xsl:value-of select="attributes/url"/></url>
			</item>
     	</xsl:for-each>
    </results>
  </xsl:template>
</xsl:stylesheet>