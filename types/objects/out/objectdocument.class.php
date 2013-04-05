<?php
namespace lowtone\types\objects\out;
use lowtone\dom\Document,
	lowtone\types\objects\Object;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\types\objects\out
 */
class ObjectDocument extends Document {

	/**
	 * @var Object
	 */
	protected $itsObject;

	const OBJECT_ELEMENT_NAME = "object_element_name",
		FILTER_PROPERTIES = "filter_properties",
		APPLY_WP_FILTERS = "apply_wp_filters",
		PROPERTY_FILTERS = "property_filters",
		BUILD_ATTRIBUTES = "build_attributes",
		BUILD_ELEMENTS = "build_elements",
		STRIP_PROPERTY_PREFIX = "strip_property_prefix";
	
	public function __construct(Object $object) {
		parent::__construct();
		
		$this->itsObject = $object;
		
		$this->updateBuildOptions(array(
			self::OBJECT_ELEMENT_NAME => strtolower(end(explode("\\", $this->itsObject->__getClass()))),
			self::BUILD_ELEMENTS => true
		));

	}
	
	public function build(array $options = NULL) {
		$this->updateBuildOptions((array) $options);

		$this->createObjectElement();
		
		$properties = $this->applyPropertyFilters($this->filterProperties($this->extractProperties()));

		$this
			->buildAttributes($properties)
			->buildElements($properties);

		do_action("build_object_document", $this);

		return $this;
	}

	/**
	 * Create the Object element.
	 * @return Element Returns the newly created Object element.
	 */
	protected function createObjectElement() {
		return $this->createAppendElement($this->getBuildOption(self::OBJECT_ELEMENT_NAME));
	}

	/**
	 * Extract the properties from the Object.
	 * @return array Returns an array of all properties for the Object.
	 */
	protected function extractProperties() {
		return (array) $this->itsObject;
	}

	/**
	 * Filter the properties.
	 * @param array $properties The subject properties.
	 * @return array Returns the filtered array.
	 */
	protected function filterProperties($properties) {
		if ($filters = $this->getBuildOption(self::FILTER_PROPERTIES))
			$properties = Object::applyFilters($properties, $filters);

		if ($this->getBuildOption(self::APPLY_WP_FILTERS))
			$properties = apply_filters("object_document_properties", $properties);

		return $properties;
	}

	/**
	 * Apply filters to the property values.
	 * @param array $properties The subject properties.
	 * @return array Returns the filtered properties.
	 */
	protected function applyPropertyFilters($properties) {
		return $this->itsObject->filterProperties((array) $this->getBuildOption(self::PROPERTY_FILTERS), NULL, $properties);
	}

	/**
	 * Build the attributes.
	 * @param array $properties The properties to build the attributes from.
	 * @return ObjectDocument Returns the ObjectDocument for chaining.
	 */
	protected function buildAttributes($properties) {
		$objectElement = $this->documentElement;

		if ($buildAttributes = $this->getBuildOption(self::BUILD_ATTRIBUTES)) {
			$attributes = $properties;

			if (is_array($buildAttributes))
				$attributes = array_intersect_key($attributes, array_flip($buildAttributes));

			$attributes = $this->stripPrefixes($attributes);

			$objectElement->setAttributes($attributes);

		}

		return $this;
	}

	/**
	 * Build the elements.
	 * @param array $properties The properties to build the attributes from.
	 * @return ObjectDocument Returns the ObjectDocument for chaining.
	 */
	protected function buildElements($properties) {
		$objectElement = $this->documentElement;

		if ($buildElements = $this->getBuildOption(self::BUILD_ELEMENTS)) {
			$elements = $properties;

			if (is_array($buildElements))
				$elements = array_intersect_key($elements, array_flip($buildElements));

			$elements = $this->stripPrefixes($elements);
			
			$objectElement->createAppendElements($elements);

		}

		return $this;
	}

	/**
	 * Strip prefixes from the property names.
	 * @param array The subject properties.
	 * @return array Returns the property with modified names.
	 */
	private function stripPrefixes($properties) {
		if ($stripPrefix = $this->getBuildOption(self::STRIP_PROPERTY_PREFIX))
			$properties = Object::stripPropertyPrefix($stripPrefix, $properties);

		return $properties; 
	}

}