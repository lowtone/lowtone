<?php
namespace lowtone\wp\shortcodes;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\wp\shortcodes
 */
class Shortcode {
	
	public static function extractShortcodes($content) {
		if (!preg_match_all('/'. get_shortcode_regex() .'/s', $content, $matches))
			return NULL;

		return implode($matches[0]);
	}

}