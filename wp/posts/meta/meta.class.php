<?php
namespace lowtone\wp\posts\meta;
use lowtone\db\records\schemata\Schema,
	lowtone\db\records\schemata\properties\Property,
	lowtone\wp\meta\Meta as Base;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\posts\meta
 */
class Meta extends Base {
		
	const PROPERTY_POST_ID = "post_id";

	// Getters

	public function getPostId() {return $this->__get(self::PROPERTY_POST_ID);}

	// Setters

	public function setPostId($postId) {return $this->__set(self::PROPERTY_POST_ID, $postId);}

	// Static

	public static function __createSchema($defaults = NULL) {
		return parent::__createSchema(Schema::mergeSchemata(array(
				self::PROPERTY_POST_ID => array(
						Property::ATTRIBUTE_TYPE => Property::TYPE_INT,
						Property::ATTRIBUTE_LENGTH => 20
					),
			), $defaults));
	}

	public static function __getTableBase() {
		return "postmeta";
	}

	public static function __getDocumentClass() {
		return "lowtone\\wp\\posts\\meta\\out\\MetaDocument";
	}

	public static function __getObjectIdProperty() {
		return self::PROPERTY_POST_ID;
	}
	
}