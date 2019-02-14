<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="xml" indent="yes"/>  
  <xsl:template match="/">
  <xsl:variable name="wosid"><xsl:value-of select="//KeyUT"/></xsl:variable> 
  <result>
    <WoS_ID><xsl:value-of select="substring-after($wosid,'WOS:')"/></WoS_ID>
    </result>
  </xsl:template>
</xsl:stylesheet>
