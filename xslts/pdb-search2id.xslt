<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="xml" indent="yes"/>  
  <xsl:template match="/">
   
    <result>
    	<total_count><xsl:value-of select="/response/total_count"/></total_count>
    	<result_set>
    		<xsl:for-each select="/response/result_set/item">
    			<item_id><xsl:value-of select="identifier"/></item_id>
    		</xsl:for-each>
    	</result_set>
    </result>
  </xsl:template>
</xsl:stylesheet>