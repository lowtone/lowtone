<?php
namespace lowtone\ui\spinner;
use lowtone\dom\xhtml\XhtmlDocument;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\ui\spinner
 */
class Spinner extends XhtmlDocument {
	
	public function build(array $options = NULL) {
		$src = get_admin_url(NULL, "images/wpspin_light.gif");
		
		$this
			->createAppendElement("img")
			->setAttributes(array(
				"src" => $src,
				"id" => "ajax-loading",
				"class" => "lowtone ajax-loading",
				"alt" => ""
			));
		
		return $this;
	}
	
}