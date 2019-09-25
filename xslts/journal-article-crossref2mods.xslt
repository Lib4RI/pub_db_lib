<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
  xmlns="http://www.loc.gov/mods/v3" 
  xmlns:mods="http://www.loc.gov/mods/v3" 
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
  xmlns:xlink="http://www.w3.org/1999/xlink"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:output method="xml" indent="yes"/>
	<xsl:template match="/">
		<mods>
		<titleInfo>
			<title><xsl:value-of select="/doi_records/doi_record/crossref/journal/journal_article/titles/title"/></title>
		</titleInfo>
		<originInfo>
    		<dateIssued encoding="w3cdtf" keyDate="yes"><xsl:value-of select="/doi_records/doi_record/crossref/journal/journal_issue/publication_date/year"/></dateIssued>
  		</originInfo>
		<identifier type="doi"><xsl:value-of select="/doi_records/doi_record/crossref/journal/journal_issue/doi_data/doi"/></identifier>
  		<xsl:for-each select="/doi_records/doi_record/crossref/journal/journal_article/contributors/person_name">
  			<xsl:choose>
  				<xsl:when test="@sequence='first'">
  					<name type="personal" usage="primary">
  					<namePart type="family"><xsl:value-of select="surname"/></namePart>
  					<namePart type="given"><xsl:value-of select="given_name"/></namePart> <!--To be checked--> 
  					<role>
  						<roleTerm authority="marcrelator" type="text">author</roleTerm>
  					</role>
  					<xsl:choose>
		  				<xsl:when test="affiliation='Paul-Scherrer Institut Villigen-PSI Switzerland'">
		  					<fourri>true</fourri>
		  				</xsl:when>
		  			</xsl:choose>  					  					
  					</name>
  				 </xsl:when>
  				 <xsl:otherwise>
    				<name type="personal">
  					<namePart type="family"><xsl:value-of select="surname"/></namePart>
  					<namePart type="given"><xsl:value-of select="given_name"/></namePart>
  					<role>
  						<roleTerm authority="marcrelator" type="text">author</roleTerm>
  					</role>
  					<xsl:choose>
		  				<xsl:when test="affiliation='Paul-Scherrer Institut Villigen-PSI Switzerland'">
		  					<fourri>true</fourri>
		  				</xsl:when>
		  			</xsl:choose>  					  					
  					</name>    				
  				 </xsl:otherwise>
  			</xsl:choose>
		</xsl:for-each>  
  		<relatedItem type="host">
  			<titleInfo>
  				<title><xsl:value-of select="/doi_records/doi_record/crossref/journal/journal_metadata/full_title"/></title>
  			</titleInfo>
  			<titleInfo type="abbreviated">
  				<title><xsl:value-of select="/doi_records/doi_record/crossref/journal/journal_metadata/abbrev_title"/></title>
  				</titleInfo>
  			<originInfo>
  				<publisher></publisher>
  			</originInfo>
			<identifier type="journal id"></identifier>
			<identifier type="issn"><xsl:value-of select="/doi_records/doi_record/crossref/journal/journal_metadata/issn[@media_type='print']"/></identifier>
			<identifier type="e-issn"><xsl:value-of select="/doi_records/doi_record/crossref/journal/journal_metadata/issn[@media_type='electronic']"/></identifier>
			<part>
				<detail type="volume">
					<number><xsl:value-of select="/doi_records/doi_record/crossref/journal/journal_issue/journal_volume/volume"/></number>
				</detail>
				<detail type="issue">
					<number><xsl:value-of select="/doi_records/doi_record/crossref/journal/journal_issue/issue"/></number>
				</detail>
				<extent unit="page">
					<start><xsl:value-of select="/doi_records/doi_record/crossref/journal/journal_article/pages/first_page"/></start>
					<end><xsl:value-of select="/doi_records/doi_record/crossref/journal/journal_article/pages/last_page"/></end>
				</extent>
			</part>
  		</relatedItem>
				
		</mods>
	</xsl:template>
</xsl:stylesheet>
