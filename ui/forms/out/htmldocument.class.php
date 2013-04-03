<?php
namespace lowtone\ui\forms\out;
use lowtone\ui\forms\base\out\FormElementDocument,
	lowtone\ui\forms\Html;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\ui\forms\out
 */
class HtmlDocument extends FormElementDocument {
	
	/**
	 * @var Input
	 */
	protected $itsHtml;
	
	public function __construct(Html $html) {
		parent::__construct($html);

		$this->updateBuildOptions(array(
				self::BUILD_ELEMENTS => array(
						Html::PROPERTY_CONTENT
					),
				self::BUILD_CHILDREN => false
			));
		
	}
	
}