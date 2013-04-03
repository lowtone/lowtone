<?php
namespace lowtone\types\objects\out;
use lowtone\dom\Document,
	lowtone\types\objects\Object;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\types\objects\out
 */
class ObjectListDocument extends Document {

	/**
	 * The objects.
	 * @var array
	 */
	protected $itsObjects = array();

	const OBJECT_LIST_ELEMENT_NAME = "object_list_element_name",
		OBJECT_DOCUMENT_OPTIONS = "object_document_options",
		TO_OBJECT = "to_object";

	public function __construct(array $objects) {
		parent::__construct();

		$this->itsObjects = $objects;

		$this->updateBuildOptions(array(
				self::OBJECT_LIST_ELEMENT_NAME => $this->createObjectListElementName()
			));
	}

	public function build(array $options = NULL) {
		$this->updateBuildOptions((array) $options);
		
		$objectListElement = $this->createAppendElement($this->getBuildOption(self::OBJECT_LIST_ELEMENT_NAME));

		$toObject = is_callable($toObject = $this->getBuildOption(self::TO_OBJECT)) ? $toObject : false;

		foreach ($this->itsObjects as $object) {
			if ($toObject !== false)
				$object = call_user_func($toObject, $object);

			if (!($object instanceof Object))
				continue;

			$objectDocument = $object->createDocument();

			$objectDocument->build($this->getBuildOption(self::OBJECT_DOCUMENT_OPTIONS));

			if ($objectElement = $this->importDocument($objectDocument))
				$objectListElement->appendChild($objectElement);

		}

		return $this;
	}

	/**
	 * Create a default object list element name based on the first object.
	 * @return string Returns the name for the object list element.
	 */
	protected function createObjectListElementName() {
		$first = reset($this->itsObjects);

		if (!($first instanceof Object))
			return "objects";

		return strtolower(end(explode("\\", $first->__getClass()))) . "s";
	}

}