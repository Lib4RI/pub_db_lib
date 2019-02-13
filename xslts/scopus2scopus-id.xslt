<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:atom="http://www.w3.org/2005/Atom" >
  
  <xsl:output method="xml" indent="yes"/>
  
  <xsl:template match="//atom:search-results">
  <result>
    <Scopus_ID><xsl:value-of select="//atom:eid"/></Scopus_ID>
    </result>
  </xsl:template>
</xsl:stylesheet>
