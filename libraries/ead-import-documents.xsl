<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:xs="http://www.w3.org/2001/XMLSchema" exclude-result-prefixes="xs" version="1.0"
	xmlns:str="http://exslt.org/strings">

	<xsl:output method="text"/>
	<xsl:param name="query"/>

	<xsl:template match="/">
		<!-- the first line contains column names -->
		<xsl:text>Title,Date,Creator,Publisher,Format,Identifier,Coverage,Description,Language,Type,Subject,Rights</xsl:text>
		<xsl:text>
		</xsl:text>
		<!-- now we have to try to pick up all item-level components -->
		<xsl:for-each
			select="descendant::c | descendant::c01 | descendant::c02 | descendant::c03 | descendant::c04 | descendant::c05 | descendant::c06 | descendant::c07 | descendant::c08 | descendant::c09 | descendant::c10 | descendant::c11">

			<!-- assume that any component without children is an item, possibly unsafe but level is not explicitly required in EAD -->
			<xsl:if
				test="not(child::c) and not(child::c02) and not(child::c03) and not(child::c04) and not(child::c05) and not(child::c06) and not(child::c07) and not(child::c08) and not(child::c09) and not(child::c10) and not(child::c11) and not(child::c12)">
				<xsl:apply-templates select="." mode="item"/>
			</xsl:if>

			<!-- uncomment below and comment conditional above if levels are explicit in guides -->
			<!--<xsl:if
				test="@level = 'item'">
				<xsl:apply-templates select="." mode="item"/>
			</xsl:if>-->
		</xsl:for-each>
	</xsl:template>

	<xsl:template match="node()" mode="item">
		<xsl:choose>
			<xsl:when test="string(normalize-space($query))">
				<xsl:variable name="text">
					<xsl:for-each select="descendant-or-self::text()">
						<xsl:value-of select="translate(., 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')"/>
						<xsl:text> </xsl:text>
					</xsl:for-each>
				</xsl:variable>
				<xsl:if test="contains($text, $query)">
					<xsl:call-template name="get_record"/>
				</xsl:if>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="get_record"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="did">
		<!-- title -->
		<xsl:text>"</xsl:text>
		<xsl:call-template name="normalize-quotes">
			<xsl:with-param name="text">
				<xsl:choose>
					<xsl:when test="not(unittitle/child::node())">
						<xsl:value-of select="normalize-space(unittitle)"/>
					</xsl:when>
					<xsl:when test="unittitle/title">
						<xsl:value-of select="normalize-space(unittitle/title)"/>
					</xsl:when>
					<xsl:when test="unittitle/unitdate">
						<xsl:value-of select="normalize-space(unittitle/text())"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="normalize-space(unittitle)"/>
					</xsl:otherwise>
				</xsl:choose>

			</xsl:with-param>
		</xsl:call-template>
		<xsl:text>",</xsl:text>

		<!-- date -->
		<xsl:text>"</xsl:text>
		<xsl:choose>
			<xsl:when test="unitdate">
				<xsl:value-of select="normalize-space(unitdate)"/>
			</xsl:when>
			<xsl:when test="unittitle/unitdate">
				<xsl:value-of select="normalize-space(unittitle/unitdate)"/>
			</xsl:when>
		</xsl:choose>
		<xsl:text>",</xsl:text>

		<!-- creator -->
		<xsl:text>"</xsl:text>
		<xsl:value-of select="normalize-space(origination)"/>
		<xsl:text>",</xsl:text>

		<!-- publisher -->
		<xsl:text>"</xsl:text>
		<xsl:value-of select="normalize-space(repository)"/>
		<xsl:text>",</xsl:text>

		<!-- format -->
		<xsl:text>"</xsl:text>
		<xsl:value-of select="normalize-space(physdesc/genreform)"/>
		<xsl:text>",</xsl:text>

		<!-- identifier -->
		<xsl:text>"</xsl:text>
		<xsl:value-of select="normalize-space(unitid)"/>
		<xsl:text>",</xsl:text>
	</xsl:template>

	<xsl:template name="normalize-quotes">
		<xsl:param name="text"/>
		<xsl:variable name="singlequote">
			<xsl:text>&#x0027;</xsl:text>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="contains($text, '&#x0022;')">
				<xsl:variable name="updated_text">
					<!--<xsl:value-of
						select="concat(substring-before($text, '&#x0022;'), $singlequote, substring-before(substring-after($text, '&#x0022;'), '&#x0022;'), $singlequote, substring-after(substring-after($text, '&#x0022;'), '&#x0022;'))"
						/>-->
					<xsl:value-of select="translate($text, '&#x0022;', $singlequote)"/>
				</xsl:variable>
				<xsl:value-of select="$updated_text"/>
				<!--<xsl:choose>
					<xsl:when test="contains($updated_text, '&#x0022;')">
						<xsl:call-template name="normalize-quotes">
							<xsl:with-param name="text" select="$updated_text"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="$updated_text"/>
					</xsl:otherwise>
				</xsl:choose>-->
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$text"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="abstract | bioghist | descgrp | scopecontent">
		<xsl:variable name="singlequote">
			<xsl:text>&#x0027;</xsl:text>
		</xsl:variable>

		<!--<xsl:value-of select="normalize-space(translate(., '&#x0022;', $singlequote))"/>-->
		<xsl:for-each select="descendant-or-self::text()">
			<xsl:value-of select="translate(normalize-space(.), '&#x0022;', $singlequote)"/>
			<xsl:text> </xsl:text>
		</xsl:for-each>
	</xsl:template>

	<xsl:template match="accessrestrict | userestrict">
		<xsl:variable name="singlequote">
			<xsl:text>&#x0027;</xsl:text>
		</xsl:variable>
		<xsl:for-each select="descendant-or-self::text()">
			<xsl:value-of select="translate(normalize-space(.), '&#x0022;', $singlequote)"/>
			<xsl:text> </xsl:text>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="get_record">
		<xsl:apply-templates select="did"/>
		<!-- coverage -->
		<xsl:text>"</xsl:text>
		<xsl:value-of select="normalize-space(descendant::unitdate)"/>
		<xsl:if test="descendant::unitdate and descendant::geogname">
			<xsl:text> </xsl:text>
		</xsl:if>
		<xsl:for-each select="descendant::geogname">
			<xsl:value-of select="normalize-space(.)"/>
			<xsl:if test="not(position() = last())">
				<xsl:text> </xsl:text>
			</xsl:if>
		</xsl:for-each>
		<xsl:text>",</xsl:text>
		
		<!-- description -->
		<xsl:text>"</xsl:text>
		<xsl:apply-templates select="abstract | bioghist | descgrp | scopecontent"/>
		<xsl:text>",</xsl:text>
		
		<!-- language -->
		<xsl:text>"</xsl:text>
		<xsl:choose>
			<xsl:when test="langmaterial/language">
				<xsl:for-each select="langmaterial/language">
					<xsl:value-of select="normalize-space(.)"/>
					<xsl:if test="not(position() = last())">
						<xsl:text> </xsl:text>
					</xsl:if>
				</xsl:for-each>
			</xsl:when>
			<xsl:otherwise>
				<xsl:for-each select="/ead/archdesc/did/langmaterial/language">
					<xsl:value-of select="normalize-space(.)"/>
					<xsl:if test="not(position() = last())">
						<xsl:text> </xsl:text>
					</xsl:if>
				</xsl:for-each>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:text>",</xsl:text>
		
		<!-- type -->
		<xsl:text>"</xsl:text>
		<xsl:value-of select="@level"/>
		<xsl:text>",</xsl:text>
		
		<!-- subject -->
		<xsl:text>"</xsl:text>
		<xsl:for-each select="descendant::subject">
			<xsl:value-of select="normalize-space(.)"/>
			<xsl:if test="not(position() = last())">
				<xsl:text>;</xsl:text>
			</xsl:if>
		</xsl:for-each>
		<xsl:text>",</xsl:text>
		
		<!-- rights -->
		<xsl:text>"</xsl:text>
		<xsl:choose>
			<xsl:when test="accessrestrict">
				<xsl:apply-templates select="accessrestrict"/>
			</xsl:when>
			<xsl:when test="/ead/archdesc/accessrestrict">
				<xsl:apply-templates select="/ead/archdesc/accessrestrict"/>
			</xsl:when>
			<xsl:when test="/ead/archdesc/descgrp/accessrestrict">
				<xsl:apply-templates select="/ead/archdesc/descgrp/accessrestrict"/>
			</xsl:when>
		</xsl:choose>
		<!--<xsl:if
			test="accessrestrict or /ead/archdesc/accessrestrict or /ead/archdesc/descgrp/accessrestrict">
			<xsl:text> </xsl:text>
			</xsl:if>-->
		<xsl:choose>
			<xsl:when test="userestrict">
				<xsl:apply-templates select="userestrict"/>
			</xsl:when>
			<xsl:when test="/ead/archdesc/userestrict">
				<xsl:apply-templates select="/ead/archdesc/userestrict"/>
			</xsl:when>
			<xsl:when test="/ead/archdesc/descgrp/userestrict">
				<xsl:apply-templates select="/ead/archdesc/descgrp/userestrict"/>
			</xsl:when>
		</xsl:choose>
		<xsl:text>"</xsl:text>
		<xsl:text>
		</xsl:text>
	</xsl:template>

</xsl:stylesheet>
