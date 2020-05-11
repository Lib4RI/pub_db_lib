<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="xml" indent="yes"/>  
  <xsl:template match="/">
   
  	<result>
  		<xsl:for-each select="/response/citation/item">
  			<xsl:if test="id = 'primary'">
	  			<item>
			    	<title><xsl:value-of select="title"/></title>
			    	<doi><xsl:value-of select="pdbx_database_id_doi"/></doi>
			    	<pmid><xsl:value-of select="pdbx_database_id_pub_med"/></pmid>
		    	</item>
	    	</xsl:if>
    	</xsl:for-each>
    </result>
  </xsl:template>
</xsl:stylesheet>