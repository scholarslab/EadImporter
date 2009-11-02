<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
      xmlns:xs="http://www.w3.org/2001/XMLSchema"
      exclude-result-prefixes="xs"
      version="1.0"
      xmlns:str="http://exslt.org/strings">
    
    <xsl:output method="text"/>
    
    <xsl:template match="/">
        <xsl:text>People:</xsl:text>
<xsl:text>
</xsl:text>
        <xsl:for-each select="//persname">
            <xsl:apply-templates select="."/>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="persname">
        <xsl:for-each select="str:tokenize(.,',')">
            <xsl:for-each select="str:tokenize(.,'--')">
                <xsl:value-of select="normalize-space(.)"/>
                <xsl:text>,</xsl:text>
            </xsl:for-each>
        </xsl:for-each>
<xsl:text>            
</xsl:text>
    </xsl:template>
    
</xsl:stylesheet>
