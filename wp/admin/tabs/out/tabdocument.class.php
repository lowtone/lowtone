<?php
namespace lowtone\wp\admin\tabs\out;
use lowtone\Util,
	lowtone\types\objects\out\ObjectDocument,
	lowtone\wp\admin\tabs\Tab;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\admin\tabs\out
 */
class TabDocument extends ObjectDocument {

	const BUILD_ACTIVE_CONTENT = "build_active_content";

	public function __construct(Tab $tab) {
		parent::__construct($tab);

		$this->updateBuildOptions(array(
				self::PROPERTY_FILTERS => array(
					Tab::PROPERTY_CONTENT => function($output) {
						if (is_callable($output))
							$output = Util::catchOutput($output);

						return $output;
					}
				),
				self::BUILD_ATTRIBUTES => array(
					Tab::PROPERTY_URI,
					Tab::PROPERTY_ACTIVE,
					Tab::PROPERTY_HIDDEN
				),
				self::BUILD_ELEMENTS => array(
					Tab::PROPERTY_NAME,
					Tab::PROPERTY_TITLE,
					Tab::PROPERTY_ICON,
					Tab::PROPERTY_SORT,
				),
				self::BUILD_ACTIVE_CONTENT => true
			));
	}

	public function build(array $options = NULL) {
		$this->updateBuildOptions($options);

		if ($this->itsObject->active && $this->getBuildOption(self::BUILD_ACTIVE_CONTENT) && true !== ($buildElements = $this->getBuildOption(self::BUILD_ELEMENTS))) {
			$buildElements = array_merge((array) $buildElements, array(Tab::PROPERTY_CONTENT));

			$this->updateBuildOptions(array(
					self::BUILD_ELEMENTS => $buildElements
				));
		}

		return parent::build();
	}

	/**
	 * Apply getters on extraction.
	 * @return array
	 */
	protected function extractProperties() {
		for ($properties = array(), $object = (array) $this->itsObject; list($key) = each($object);) 
			$properties[$key] = $this->itsObject[$key];

		return $properties;
	}

}