<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
  xmlns="http://www.loc.gov/mods/v3" 
  xmlns:mods="http://www.loc.gov/mods/v3" 
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
  xmlns:xlink="http://www.w3.org/1999/xlink"
  xmlns:dtd="http://www.elsevier.com/xml/svapi/abstract/dtd"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:dn="http://www.elsevier.com/xml/svapi/abstract/dtd" 
  xmlns:ait="http://www.elsevier.com/xml/ani/ait" 
  xmlns:ce="http://www.elsevier.com/xml/ani/common" 
  xmlns:cto="http://www.elsevier.com/xml/cto/dtd" 
  xmlns:dc="http://purl.org/dc/elements/1.1/" 
  xmlns:prism="http://prismstandard.org/namespaces/basic/2.0/" 
  xmlns:xocs="http://www.elsevier.com/xml/xocs/dtd" 
  exclude-result-prefixes="dtd xsl prism dn ait ce cto dc prism xocs">
  
  <xsl:output method="xml" indent="yes"/>
	<xsl:template match="/">
	<mods>
		<titleInfo>
			<title><xsl:value-of select="/dtd:abstracts-retrieval-response/dtd:coredata/dc:title"/></title>
		</titleInfo>
		<originInfo>
    		<dateIssued encoding="w3cdtf" keyDate="yes"><xsl:value-of select="/dtd:abstracts-retrieval-response/item/ait:process-info/ait:date-sort/@year"/></dateIssued>
  		</originInfo>
  		<identifier type="doi"><xsl:value-of select="/dtd:abstracts-retrieval-response/item/bibrecord/item-info/itemidlist/ce:doi"/></identifier>
  		<xsl:for-each select="/dtd:abstracts-retrieval-response/dtd:authors/dtd:author">
  			<xsl:choose>
  				<xsl:when test="@seq=1">
  					<name type="personal" usage="primary">
  					<namePart type="family"><xsl:value-of select="ce:surname"/></namePart>
  					<namePart type="given"><xsl:value-of select="ce:given-name"/></namePart>
  					<role>
  						<roleTerm authority="marcrelator" type="text">author</roleTerm>
  					</role>
  					</name>
  				 </xsl:when>
  				 <xsl:otherwise>
    				<name type="personal">
  					<namePart type="family"><xsl:value-of select="ce:surname"/></namePart>
  					<namePart type="given"><xsl:value-of select="ce:given-name"/></namePart>
  					<role>
  						<roleTerm authority="marcrelator" type="text">author</roleTerm>
  					</role>
  					</name>    				
  				 </xsl:otherwise>
  			</xsl:choose>
		</xsl:for-each>  
  		<abstract><xsl:value-of select=""/></abstract>
	</mods>
	</xsl:template>
</xsl:stylesheet>
