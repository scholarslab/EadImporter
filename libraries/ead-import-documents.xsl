<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:xs="http://www.w3.org/2001/XMLSchema" exclude-result-prefixes="xs" version="1.0"
	xmlns:str="http://exslt.org/strings">

	<xsl:output method="text"/>

	<xsl:template match="/">
		<!-- the first line contains column names -->
		<xsl:text>Title,Date</xsl:text>
		<xsl:text>
		</xsl:text>
		<!-- now we have to try to pick up all item-level components -->
		<xsl:for-each select="descendant::node()[@level='item']">
			<xsl:apply-templates select="did"/>
			<!--<xsl:text>,</xsl:text>-->
			<!--<xsl:call-template name="description"/>-->
			<xsl:text>
			</xsl:text>
		</xsl:for-each>
	</xsl:template>

	<xsl:template match="did">
		<xsl:text>"</xsl:text><xsl:value-of select="normalize-space(unittitle)"/><xsl:text>"</xsl:text>
		<xsl:text>,</xsl:text>
		<!--<xsl:value-of select="origination"/>
		<xsl:text>,</xsl:text>-->
		<xsl:text>"</xsl:text><xsl:value-of select="normalize-space(unitdate)"/><xsl:text>"</xsl:text>
	</xsl:template>

	<xsl:template name="description">
		<xsl:for-each select="abstract">
			<xsl:value-of select="."/>
			<xsl:if test="not(position() = last())">
				<xsl:text>|</xsl:text>
			</xsl:if>
		</xsl:for-each>
		<!--<xsl:if test="abstract and bioghist">
			<xsl:text>|</xsl:text>
		</xsl:if>
		<xsl:for-each select="bioghist">
			<xsl:value-of select="."/>
			<xsl:if test="not(position() = last())">
				<xsl:text>|</xsl:text>
			</xsl:if>
		</xsl:for-each>
		<xsl:if test="(abstract or bioghist) and descgrp">
			<xsl:text>|</xsl:text>
		</xsl:if>
		<xsl:for-each select="descgrp">
			<xsl:value-of select="."/>
			<xsl:if test="not(position() = last())">
				<xsl:text>|</xsl:text>
			</xsl:if>
		</xsl:for-each>-->
	</xsl:template>
</xsl:stylesheet>
