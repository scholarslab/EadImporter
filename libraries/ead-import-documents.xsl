<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="1.0"
    xmlns:str="http://exslt.org/strings">
    
    <xsl:output method="text"/>
    
    <xsl:template match="/">
        <!-- the first line contains column names -->
        <xsl:text>Title,Date</xsl:text>
        <!-- now we have to try to pick up all item-level components --> 
        <xsl:for-each select="//*[starts-with(local-name(),'c')][@level='item']">
            <xsl:apply-templates select="did"/>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="did">
        <xsl:value-of select="normalize-space(unittitle)"/>
        <xsl:text>,</xsl:text>
        <xsl:value-of select="normalize-space(unitdate)"/>
    </xsl:template>
    
</xsl:stylesheet>
