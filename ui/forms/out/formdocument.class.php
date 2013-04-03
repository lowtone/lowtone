<?php
namespace lowtone\ui\forms\out;
use lowtone\ui\forms\base\out\FormElementDocument,
	lowtone\ui\forms\Form;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\ui\forms\out
 */
class FormDocument extends FormElementDocument {
	
	public function __construct(Form $form) {
		parent::__construct($form);

		$this->updateBuildOptions(array(
				self::OBJECT_ELEMENT_NAME => "form",
				self::BUILD_ATTRIBUTES => array(
						Form::PROPERTY_UNIQUE_ID,
						Form::PROPERTY_ACTION,
						Form::PROPERTY_METHOD
					)
			));
		
	}
	
}