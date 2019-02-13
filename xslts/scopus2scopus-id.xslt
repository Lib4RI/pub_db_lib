<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xml>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="xml" indent="yes"/>
  
  <xsl:template match="/">
    <Scopus_ID><xsl:value-of select="//eid"/></Scopus_ID>
  </xsl:template>
</xsl:stylesheet>
