<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="xml" indent="yes"/>  
  <xsl:template match="/">
  <result>
    <Pubmed_ID><xsl:value-of select="//record/@pmid"/></Pubmed_ID>
    </result>
  </xsl:template>
</xsl:stylesheet>
