<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:dc="http://purl.org/dc/elements/1.1/" 
  xmlns:dcterms="http://purl.org/dc/terms/" 
  xmlns:opensearch="http://a9.com/-/spec/opensearch/1.1/" 
  xmlns:prism="http://prismstandard.org/namespaces/basic/2.0/" 
  xmlns:atom="http://www.w3.org/2005/Atom" 
  xmlns:cto="http://www.elsevier.com/xml/cto/dtd">
    
  <xsl:output method="xml" indent="yes"/>
  
  <xsl:template match="/">
    <results>
    	<xsl:for-each select="/atom:search-results/atom:entry">
    		<item>
    			<doi><xsl:value-of select="prism:doi" /></doi>
    			<eid><xsl:value-of select="atom:eid" /></eid>
    		</item>
		</xsl:for-each>  
    </results>
  </xsl:template>
</xsl:stylesheet>


