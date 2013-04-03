<?php
namespace lowtone\ui\forms\out;
use lowtone\ui\forms\base\out\FormElementDocument,
	lowtone\ui\forms\FieldSet;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\ui\forms\out
 */
class FieldSetDocument extends FormElementDocument {
	
	public function __construct(FieldSet $fieldSet) {
		parent::__construct($fieldSet);

		$this->updateBuildOptions(array(
				self::BUILD_ELEMENTS => array(
						FieldSet::PROPERTY_LEGEND
					)
			));
		
	}
	
}