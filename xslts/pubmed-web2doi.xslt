<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:xhtml="http://www.w3.org/1999/xhtml">
  <xsl:output method="xml" indent="yes"/> 
  <xsl:template match="/">
  	<results>
  		<doi><xsl:value-of select="//xhtml:a[@ref='aid_type=doi']" /></doi>
  	</results>
  </xsl:template>
 </xsl:stylesheet> 
 

