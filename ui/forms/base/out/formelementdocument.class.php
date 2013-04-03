<?php
namespace lowtone\ui\forms\base\out;
use lowtone\types\objects\out\ObjectDocument,
	lowtone\ui\forms\base\FormElement;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\ui\forms\base\out
 */
class FormElementDocument extends ObjectDocument {
	
	/**
	 * @var FormElement
	 */
	protected $itsFormElement;

	const BUILD_CHILDREN = "build_children",
		BUILD_CLASS = "build_class";
	
	public function __construct(FormElement $element) {
		parent::__construct($element);
		
		$this->itsFormElement = $this->itsObject;

		$this->updateBuildOptions(array(
				self::BUILD_ATTRIBUTES => array(
						FormElement::PROPERTY_UNIQUE_ID,
						FormElement::PROPERTY_DISABLED
					),
				self::BUILD_ELEMENTS => false,
				self::BUILD_CHILDREN => true,
				self::BUILD_CLASS => true
			));
		
		$this->setTemplate(realpath(dirname(dirname(__DIR__)) . "/templates/form.xsl"));
		
	}
	
	public function build(array $options = NULL) {
		parent::build($options);

		// Build children

		if ($this->getBuildOption(self::BUILD_CHILDREN)) {

			$children = array_filter((array) $this->itsFormElement->getChildren(), function($child) {
				return $child instanceof FormElement;
			});

			$byOrder = array();

			foreach ($children as $child) 
				$byOrder[(int) $child[FormElement::PROPERTY_ORDER]][] = $child;

			ksort($byOrder, SORT_NUMERIC);

			$children = call_user_func_array("array_merge", $byOrder);

			foreach ($children as $child) {
				$childDocument = $child->createDocument();

				$childDocument->build();

				if ($childElement = $this->importDocument($childDocument))
					$this->documentElement->appendChild($childElement);
			}

		}

		// Build class

		if ($this->getBuildOption(self::BUILD_CLASS)) {

			foreach ((array) $this->itsFormElement->getClass() as $class) 
				$this->documentElement->appendCreateElement(FormElement::PROPERTY_CLASS, $class);

		}
		
		return $this;
	}
	
}