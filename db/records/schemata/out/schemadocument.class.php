<?php
namespace lowtone\db\records\schemata\out;
use lowtone\types\objects\out\ObjectDocument,
	lowtone\db\records\schemata\properties\Property;

class SchemaDocument extends ObjectDocument {
	
	public function build(array $options = NULL) {
		parent::build($options);

		if (($recordClass = $this->itsObject->getRecordClass()) && class_exists($recordClass)) {

			$this->documentElement->setAttributes(array(
					"name" => strtolower(end(explode("\\", $recordClass))),
					"table" => $recordClass::__getTable(),
					"record_class" => $recordClass
				));

		}

		return $this;
	}

	protected function extractProperties() {
		$properties = parent::extractProperties();

		return array_map(function($property, $key) {
			if ($property instanceof Property)
				return $property;

			$property = new Property($property);

			$property->name($key);

			return $property;
		}, array_values($properties), array_keys($properties));
	}

}