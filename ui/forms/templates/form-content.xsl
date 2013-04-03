<?xml version="1.0" encoding="UTF-8"?>
<!--
	This template removes the form element because widget forms accept only
	fields.
	@author Paul van der Meijs <code@paulvandermeijs.nl>
	@copyright Copyright (c) 2012, Paul van der Meijs
	@version 1.0
 -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="form.xsl" />

	<!-- Overwrite form -->
	
	<xsl:template match="form">
		<xsl:apply-templates select="." mode="content" />
	</xsl:template>
	
</xsl:stylesheet>