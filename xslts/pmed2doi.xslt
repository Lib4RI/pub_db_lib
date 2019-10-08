<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="xml" indent="yes"/>  
  <xsl:template match="/">
  <result>
    <doi><xsl:value-of select="//record/@doi"/></doi>
    </result>
  </xsl:template>
</xsl:stylesheet>
