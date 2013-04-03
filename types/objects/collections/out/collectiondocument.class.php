<?php
namespace lowtone\types\objects\collections\out;
use lowtone\dom\Document,
	lowtone\types\objects\Object,
	lowtone\types\objects\collections\Collection;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\types\objects\out
 */
class CollectionDocument extends Document {

	/**
	 * The objects.
	 * @var Collection
	 */
	protected $itsCollection = array();

	const COLLECTION_ELEMENT_NAME = "collection_element_name",
		OBJECT_DOCUMENT_OPTIONS = "object_document_options",
		FILTER_OBJECT_DOCUMENT_OPTIONS = "filter_object_document_options",
		APPLY_WP_FILTERS = "apply_wp_filters",
		OBJECT_DOCUMENT_OPTIONS_FILTERS = "object_document_options_filters",
		TO_OBJECT = "to_object";

	public function __construct(Collection $collection) {
		parent::__construct();

		$this->itsCollection = $collection;

		$this->updateBuildOptions(array(
				self::COLLECTION_ELEMENT_NAME => $this->createCollectionElementName()
			));
	}

	public function build(array $options = NULL) {
		$this->updateBuildOptions((array) $options);
		
		$this->createAppendElement($collectionElementName = $this->getBuildOption(self::COLLECTION_ELEMENT_NAME));

		$document = $this;

		$toObject = is_callable($toObject = $this->getBuildOption(self::TO_OBJECT)) ? $toObject : false;

		$this->itsCollection->each(function($object) use ($document, $collectionElementName, $toObject) {
			if (!($object instanceof Object || is_callable($toObject) && ($object = call_user_func($toObject, $object)) instanceof Object))
				return;

			$objectDocument = $object->createDocument();

			$objectDocumentOptions = $document->getBuildOption(CollectionDocument::OBJECT_DOCUMENT_OPTIONS);

			if ($document->getBuildOption(CollectionDocument::FILTER_OBJECT_DOCUMENT_OPTIONS)) {
				$objectDocumentOptions = Object::applyFilters($objectDocumentOptions, $document->getBuildOption(CollectionDocument::OBJECT_DOCUMENT_OPTIONS_FILTERS), array($object, $document));

				if ($document->getBuildOption(CollectionDocument::APPLY_WP_FILTERS)) {

					foreach (array("collection_document_object_document_options", $collectionElementName . "_collection_document_object_document_options") as $filter) 
						$objectDocumentOptions = apply_filters($filter, $objectDocumentOptions, $object, $document);

				}

			}

			$objectDocument->build($objectDocumentOptions);

			if (!($objectElement = $document->importDocument($objectDocument)))
				return;

			$document->documentElement->appendChild($objectElement);
		});
		
		return $this;
	}

	/**
	 * Create a default object list element name based on the first object.
	 * @return string Returns the name for the object list element.
	 */
	protected function createCollectionElementName() {
		foreach ($this->itsCollection->getObjects() as $object) {
			if (!($object instanceof Object))
				continue;

			return strtolower(end(explode("\\", $object->__getClass()))) . "s";
		}

		return "objects";
	}

	public function getCollection() {
		return $this->itsCollection;
	}

}