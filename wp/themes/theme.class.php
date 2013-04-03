<?php
namespace lowtone\wp\themes;
use WP_Theme,
	lowtone\types\objects\Object;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\themes
 */
class Theme extends Object {
	
	const PROPERTY_THEME_ROOT = "theme_root",
		PROPERTY_HEADERS = "headers",
		PROPERTY_HEADERS_SANITIZED = "headers_sanitized",
		PROPERTY_NAME_TRANSLATED = "name_translated",
		PROPERTY_ERRORS = "errors",
		PROPERTY_STYLESHEET = "stylesheet",
		PROPERTY_TEMPLATE = "template",
		PROPERTY_PARENT = "parent",
		PROPERTY_THEME_ROOT_URI = "theme_root_uri",
		PROPERTY_TEXTDOMAIN_LOADED = "textdomain_loaded",
		PROPERTY_CACHE_HASH = "cache_hash";
	
	const HEADER_NAME = "Name",
		HEADER_THEME_URI = "ThemeURI",
		HEADER_DESCRIPTION = "Description",
		HEADER_AUTHOR = "Author",
		HEADER_AUTHOR_URI = "AuthorURI",
		HEADER_VERSION = "Version",
		HEADER_TEMPLATE = "Template",
		HEADER_STATUS = "Status",
		HEADER_TAGS = "Tags",
		HEADER_TEXT_DOMAIN = "TextDomain",
		HEADER_DOMAIN_PATH = "DomainPath";
	
	public function __construct($theme = NULL) {
		
		if (is_array($theme)) {
			
			if (!array_key_exists(self::PROPERTY_HEADERS, $theme))
				$theme = array(self::PROPERTY_HEADERS => $theme);
			
		} else if ($theme instanceof WP_Theme) {
			
			$theme = $this->extractThemeProperties($theme);
			
		}
		
		parent::__construct($theme);
		
	}

	// Getters
	
	public function getHeaders() {return $this->__get(self::PROPERTY_HEADERS);}
	
	// Static
	
	/**
	 * Extract the headers from the given file.
	 * @param string $file The style.css for the required theme (for WordPress 
	 * 3.4 and later the theme directory path will do).
	 * @return array Returns an array with the theme's headers.
	 */
	public static function getData($file) {
		$version = get_bloginfo("version");
		
		// Before WordPress 3.4
		
		if (version_compare($version, "3.4", '<')) 
			return get_theme_data($file);
		
		// WordPress 3.4 and later
		
		if (preg_match("/.css/i", $file))
			$file = dirname($file);
		
		$theme = new Theme(wp_get_theme(basename($file), dirname($file)));
		
		return $theme->getHeaders();
	}
	
	/**
	 * Extract the properties from a WP_Theme object.
	 * @param WP_Theme $theme The subject theme.
	 * @return array Returns an array with the theme properties.
	 */
	public static function extractThemeProperties(WP_Theme $theme) {
		return array(
			self::PROPERTY_THEME_ROOT => $theme->get_theme_root(),
			self::PROPERTY_HEADERS => array(
				self::HEADER_NAME => $theme->get(self::HEADER_NAME),
				self::HEADER_THEME_URI => $theme->get(self::HEADER_THEME_URI),
				self::HEADER_DESCRIPTION => $theme->get(self::HEADER_DESCRIPTION),
				self::HEADER_AUTHOR => $theme->get(self::HEADER_AUTHOR),
				self::HEADER_AUTHOR_URI => $theme->get(self::HEADER_AUTHOR_URI),
				self::HEADER_VERSION => $theme->get(self::HEADER_VERSION),
				self::HEADER_TEMPLATE => $theme->get(self::HEADER_TEMPLATE),
				self::HEADER_STATUS => $theme->get(self::HEADER_STATUS),
				self::HEADER_TAGS => $theme->get(self::HEADER_TAGS),
				self::HEADER_TEXT_DOMAIN => $theme->get(self::HEADER_TEXT_DOMAIN),
				self::HEADER_DOMAIN_PATH => $theme->get(self::HEADER_DOMAIN_PATH)
			),
			self::PROPERTY_STYLESHEET => $theme->get_stylesheet(),
			self::PROPERTY_TEMPLATE => $theme->get_template(),
			self::PROPERTY_THEME_ROOT_URI => $theme->get_theme_root_uri()
		);
	}
	
}