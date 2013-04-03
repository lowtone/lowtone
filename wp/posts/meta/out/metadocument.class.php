<?php
namespace lowtone\wp\posts\meta\out;
use lowtone\dom\Element,
	lowtone\types\objects\out\ObjectDocument,
	lowtone\wp\dom\interfaces\Document as WpDocument,
	lowtone\wp\posts\meta\Meta;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\posts\meta\out
 */
class MetaDocument extends ObjectDocument implements WpDocument {

	public function __construct($meta) {
		parent::__construct(new Meta($meta));

		$this->updateBuildOptions(array(
				self::BUILD_ATTRIBUTES => array(
						Meta::PROPERTY_META_ID,
						Meta::PROPERTY_POST_ID
					),
				self::BUILD_ELEMENTS => array(
						Meta::PROPERTY_META_KEY,
						Meta::PROPERTY_META_VALUE
					),
				self::STRIP_PROPERTY_PREFIX => "meta_",
				self::PROPERTY_FILTERS => array(
						Meta::PROPERTY_META_VALUE => function($value) {
							if (is_array($value)) {

								// Rename numeric keys

								$value = \lowtone\types\arrays\XArray::mapKeys(function($value) {
									if (!Element::validateName($value)) 
										$value = Element::normalizeName($value);

									return $value;
								}, $value);

							}

							return $value;
						}
					)
			));
	}
	
	public function build(array $options = NULL) {
		parent::build($options);

		$metaElement = $this->documentElement;
		
		if ($this->itsObject->isPublic())
			$metaElement->setAttribute("public", "1");
		
		return $this;
	}
	
}