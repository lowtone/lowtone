<?xml version="1.0" encoding="UTF-8"?>
<!--
	This form template is intended for use on the profile page where inputs are
	displayed in tables.
	@author Paul van der Meijs <code@paulvandermeijs.nl>
	@copyright Copyright (c) 2012, Paul van der Meijs
	@version 1.0
 -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="form-content.xsl" />
	
	
	<!-- Override fieldset -->
	
	<xsl:template match="fieldset">
		<xsl:apply-templates select="legend" />
		<table class="form-table">
			<xsl:apply-templates select="input|html" />
		</table>
	</xsl:template>
	
	
	<!-- Override legend -->
	
	<xsl:template match="legend">
		<h3><xsl:value-of select="." /></h3>
	</xsl:template>
	
	
	<!-- Override input -->
	
	<xsl:template match="input">
		<xsl:variable name="id" select="@uniqid" />

		<tr>
			<xsl:call-template name="class" />
			<th>
				<xsl:apply-templates select="label">
					<xsl:with-param name="id" select="$id" />
				</xsl:apply-templates>
			</th>
			<td>
				<xsl:choose>
					<xsl:when test="@multiple">
						<textarea id="{$id}" name="{name}">
							<xsl:call-template name="class" />
							<xsl:value-of select="value" />
						</textarea>
					</xsl:when>
					<xsl:otherwise>
						<input id="{$id}" type="{@type}" name="{name}" value="{value}">
							<xsl:call-template name="class" />
						</input>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:apply-templates select="comment" />
			</td>
		</tr>
	</xsl:template>


	<xsl:template match="input[@type='hidden']">
		<input type="{@type}" name="{name}" value="{value}" />
	</xsl:template>

			
	<!-- Override select -->
	
	<xsl:template match="input[@type='select']">
		<xsl:variable name="id" select="@uniqid" />

		<tr>
			<xsl:call-template name="class" />
			<th>
				<xsl:apply-templates select="label">
					<xsl:with-param name="id" select="$id" />
				</xsl:apply-templates>
			</th>
			<td>
				<select id="{$id}">
					<xsl:attribute name="name">
						<xsl:value-of select="name" />
						<xsl:if test="@multiple">
							<xsl:text>[]</xsl:text>
						</xsl:if>
					</xsl:attribute>
					<xsl:if test="@multiple">
						<xsl:attribute name="multiple">multiple</xsl:attribute>
					</xsl:if>
					<xsl:call-template name="class" />
					<xsl:if test="@disabled">
						<xsl:attribute name="disabled">1</xsl:attribute>
					</xsl:if>
					<xsl:apply-templates select="option" />
					<xsl:apply-templates select="optgroup" />
				</select>
				<xsl:apply-templates select="comment" />
			</td>
		</tr>
	</xsl:template>
	
	
	<!-- Override comment -->
	
	<xsl:template match="comment">
		<span class="description"><xsl:value-of select="." disable-output-escaping="yes" /></span>
	</xsl:template>


	<!-- Html -->

	<xsl:template match="html">
		<tr>
			<xsl:call-template name="class" />
			<td colspan="2">
				<xsl:value-of select="content" disable-output-escaping="yes" />
			</td>
		</tr>
	</xsl:template>
	
</xsl:stylesheet>