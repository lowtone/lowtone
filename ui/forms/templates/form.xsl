<?xml version="1.0" encoding="UTF-8"?>
<!--
	@author Paul van der Meijs <code@paulvandermeijs.nl>
	@copyright Copyright (c) 2012, Paul van der Meijs
	@version 1.0
 -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" encoding="utf-8" indent="no" />
		
	<!-- Form -->
	
	<xsl:template match="form">
		<form>
			<xsl:call-template name="attributes" />
			<xsl:if test="@uniqid">
				<xsl:attribute name="id">
					<xsl:value-of select="@uniqid" />
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@action">
				<xsl:attribute name="action">
					<xsl:value-of select="@action" />
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@method">
				<xsl:attribute name="method">
					<xsl:value-of select="@method" />
				</xsl:attribute>
			</xsl:if>
			<xsl:attribute name="enctype">
				<xsl:choose>
					<xsl:when test="@enctype">
						<xsl:value-of select="@enctype" />
					</xsl:when>
					<xsl:when test="//input[@type='file']">
						<xsl:text>multipart/form-data</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>application/x-www-form-urlencoded</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<xsl:call-template name="class" />
			<xsl:apply-templates select="." mode="content" />
		</form>
	</xsl:template>


	<!-- Form content -->
	
	<xsl:template match="form" mode="content">
		<xsl:apply-templates select="fieldset|input|html" />
	</xsl:template>
	
	
	<!-- Fieldset -->
	
	<xsl:template match="fieldset">
		<fieldset id="{@uniqid}">
			<xsl:call-template name="attributes" />
			<xsl:call-template name="class" />
			<xsl:if test="boolean(string(legend))">
				<xsl:apply-templates select="legend" />
			</xsl:if>
			<xsl:apply-templates select="fieldset|input|html" />
		</fieldset>
	</xsl:template>
	
	
	<!-- Legend -->
	
	<xsl:template match="legend">
		<legend class="lowtone"><span><xsl:value-of select="." /></span></legend>
	</xsl:template>
	
	
	<!-- Input -->
	
	<xsl:template match="input">
		<input id="{@uniqid}" type="{@type}" name="{name}" value="{value}">
			<xsl:call-template name="attributes" />
			<xsl:if test="@disabled">
				<xsl:attribute name="disabled">1</xsl:attribute>
			</xsl:if>
			<xsl:call-template name="class" />
		</input>
	</xsl:template>
	
	
	<!-- Text -->
	
	<xsl:template match="input[@type='text']">
		<xsl:variable name="id" select="@uniqid" />
		
		<xsl:apply-templates select="label">
			<xsl:with-param name="id" select="$id" />
		</xsl:apply-templates>

		<xsl:choose>
			<xsl:when test="@multiple">
				<textarea id="{$id}" name="{name}" placeholder="{placeholder}">
					<xsl:call-template name="attributes" />
					<xsl:if test="@disabled">
						<xsl:attribute name="disabled">1</xsl:attribute>
					</xsl:if>
					<xsl:call-template name="class" />
					<xsl:value-of select="value" />
				</textarea>
			</xsl:when>
			<xsl:otherwise>
				<input id="{$id}" type="{@type}" name="{name}" value="{value}" placeholder="{placeholder}">
					<xsl:call-template name="attributes" />
					<xsl:if test="@disabled">
						<xsl:attribute name="disabled">1</xsl:attribute>
					</xsl:if>
					<xsl:call-template name="class" />
				</input>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:apply-templates select="comment" />
		<div class="clear" />
	</xsl:template>

			
	<!-- Select -->
	
	<xsl:template match="input[@type='select']">
		<xsl:variable name="id" select="@uniqid" />
		
		<xsl:apply-templates select="label">
			<xsl:with-param name="id" select="$id" />
		</xsl:apply-templates>
		<select id="{$id}">
			<xsl:call-template name="attributes" />
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
		<div class="clear" />
	</xsl:template>
	
	
	<!-- Option group -->
	
	<xsl:template match="optgroup">
		<xsl:element name="optgroup">
			<xsl:attribute name="label"><xsl:value-of select="@label" /></xsl:attribute>
			<xsl:apply-templates select="option" />
		</xsl:element>
	</xsl:template>
	
	
	<!-- Option -->
	
	<xsl:template match="option">
		<xsl:element name="option">
			<xsl:attribute name="value"><xsl:value-of select="value" /></xsl:attribute>
			<xsl:if test="@selected">
				<xsl:attribute name="selected">selected</xsl:attribute>
			</xsl:if>
			<xsl:if test="@disabled">
				<xsl:attribute name="disabled">1</xsl:attribute>
			</xsl:if>
			<xsl:value-of select="label" disable-output-escaping="yes" />
		</xsl:element>
	</xsl:template>
	
	
	<!-- Checkbox or radio -->
	
	<xsl:template match="input[@type='checkbox' or @type='radio']">
		<xsl:variable name="id" select="@uniqid" />
		
		<input id="{$id}" type="{@type}" name="{name}" value="{value}" class="lowtone {@type}">
			<xsl:call-template name="attributes" />
			<xsl:if test="@selected">
				<xsl:attribute name="checked">checked</xsl:attribute>
			</xsl:if>
			<xsl:if test="@disabled">
				<xsl:attribute name="disabled">1</xsl:attribute>
			</xsl:if>
		</input>
		<xsl:apply-templates select="label">
			<xsl:with-param name="id" select="$id" />
		</xsl:apply-templates>
		
		<xsl:apply-templates select="comment" />
		<div class="clear" />
	</xsl:template>
	
	
	<!-- Buttons -->
	
	<xsl:template match="input[@type='button' or @type='submit']">
		<xsl:variable name="id" select="@uniqid" />
		
		<input id="{$id}" type="{@type}" name="{name}" value="{value}">
			<xsl:call-template name="attributes" />
			<xsl:if test="@disabled">
				<xsl:attribute name="disabled">1</xsl:attribute>
			</xsl:if>
			<xsl:call-template name="class" />
		</input>
	</xsl:template>
	
	
	<!-- Label -->
	
	<xsl:template match="label">
		<xsl:param name="id" />
		
		<xsl:if test="string(.)">
			<label for="{$id}" class="lowtone"><xsl:value-of select="." disable-output-escaping="yes" /></label>
		</xsl:if>
	</xsl:template>
	
	
	<!-- Comment -->
	
	<xsl:template match="comment">
		<xsl:if test="string(.)">
			<p class="comment lowtone"><xsl:value-of select="." disable-output-escaping="yes" /></p>
		</xsl:if>
	</xsl:template>


	<!-- Html -->

	<xsl:template match="html">
		<div>
			<xsl:call-template name="attributes" />
			<xsl:call-template name="class" />
			<xsl:value-of select="content" disable-output-escaping="yes" />
		</div>
	</xsl:template>


	<!-- Class -->

	<xsl:template name="class">
		<xsl:attribute name="class">
			<xsl:for-each select="class">
				<xsl:if test="position()>1">
					<xsl:text> </xsl:text>
				</xsl:if>
				<xsl:value-of select="." />
			</xsl:for-each>
		</xsl:attribute>
	</xsl:template>


	<!-- Attributes -->

	<xsl:template name="attributes">
		<xsl:for-each select="attribute">
			<xsl:attribute name="{@name}"><xsl:value-of select="." /></xsl:attribute>
		</xsl:for-each>
	</xsl:template>
	
</xsl:stylesheet>