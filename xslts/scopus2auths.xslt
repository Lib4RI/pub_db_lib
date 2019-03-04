<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:dc="http://purl.org/dc/elements/1.1/" 
  xmlns:dcterms="http://purl.org/dc/terms/" >
  
  <xsl:output method="xml" indent="yes"/>
  
  <xsl:template match="/">
    <result>
    	<xsl:for-each select="//dc:creator">
    		<author><xsl:value-of select="current()" /></author>
		</xsl:for-each>  
    </result>
  </xsl:template>
</xsl:stylesheet>


