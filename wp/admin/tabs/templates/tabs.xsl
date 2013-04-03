<?xml version="1.0" encoding="UTF-8"?>
<!--
	@author Paul van der Meijs <code@paulvandermeijs.nl>
	@copyright Copyright (c) 2012, Paul van der Meijs
	@version 1.0
 -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	
	<xsl:output 
		method="html" 
		encoding="utf-8" 
		indent="no" 
		omit-xml-declaration="yes"  />

	<!-- Tabs -->

	<xsl:template match="tabs">
		<xsl:variable name="active" select="tab[@active]" />

		<xsl:variable name="icon">
			<xsl:choose>
				<xsl:when test="string($active/icon)">
					<xsl:value-of select="$active/icon" />
				</xsl:when>
				<xsl:when test="string(@default_icon)">
					<xsl:value-of select="@default_icon" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>options-general</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<div class="wrap">
			<div id="icon-{$icon}" class="icon32"><br /></div>
			<h2 class="nav-tab-wrapper">
				<xsl:if test="@title">
					<span class="nav-tab-title"><xsl:value-of select="@title" disable-output-escaping="yes" /></span>
				</xsl:if>
				<xsl:apply-templates select="tab" mode="nav" />
			</h2>
			<xsl:apply-templates select="$active" />
		</div>
	</xsl:template>


	<!-- Tab navigation -->

	<xsl:template match="tab" mode="nav">
		<a href="{@uri}">
			<xsl:attribute name="class">
				<xsl:text>nav-tab</xsl:text>

				<xsl:if test="@active">
					<xsl:text> nav-tab-active</xsl:text>
				</xsl:if>

				<xsl:if test="@hidden">
					<xsl:text> nav-tab-hidden</xsl:text>
				</xsl:if>
			</xsl:attribute>
			<xsl:value-of select="title" />
		</a>
	</xsl:template>


	<!-- Single tab -->

	<xsl:template match="tab">
		<xsl:value-of select="content" disable-output-escaping="yes" />
	</xsl:template>

</xsl:stylesheet>