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

	<xsl:template match="postboxes">
		<div class="metabox-holder">
			<xsl:apply-templates select="postbox" />
		</div>
	</xsl:template>


	<!-- Single tab -->

	<xsl:template match="postbox">
		<div class="postbox">
			<h3 class="hndle"><span><xsl:value-of select="title" /></span></h3>
			<div class="inside">
				<xsl:value-of select="content" disable-output-escaping="yes" />
			</div>
		</div>
	</xsl:template>

</xsl:stylesheet>