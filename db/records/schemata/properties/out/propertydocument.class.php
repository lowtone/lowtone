<?php
namespace lowtone\db\records\schemata\properties\out;
use lowtone\types\objects\out\ObjectDocument,
	lowtone\db\records\schemata\properties\Property;

class PropertyDocument extends ObjectDocument {
	
	public function __construct(Property $property) {
		parent::__construct($property);

		$this->updateBuildOptions(array(
				self::BUILD_ATTRIBUTES => array(
						Property::ATTRIBUTE_NAME
					),
				self::BUILD_ELEMENTS => array(
						Property::ATTRIBUTE_TYPE,
						Property::ATTRIBUTE_LENGTH,
						Property::ATTRIBUTE_NULL,
						Property::ATTRIBUTE_AUTO_INCREMENT,
						Property::ATTRIBUTE_DEFAULT_VALUE,
						Property::ATTRIBUTE_ON_UPDATE,
						// Property::ATTRIBUTE_INDEXES,
						// Property::ATTRIBUTE_GET,
						// Property::ATTRIBUTE_SET,
						// Property::ATTRIBUTE_SERIALIZE,
						// Property::ATTRIBUTE_UNSERIALIZE
					)
			));
	}

	public function build(array $options = NULL) {
		parent::build($options);

		// Indexes

		if ($this->itsObject->indexes) {
			$indexesElement = $this->documentElement->createAppendElement("indexes");

			foreach ($this->itsObject->indexes as $index)
				$indexesElement->appendCreateElement("index", $index);

		}

		return $this;
	}
	
}