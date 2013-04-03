<?php
namespace lowtone\wp\admin\postbox\handlers\out;
use lowtone\util\properties\out\PropertiesListDocument,
	lowtone\util\properties\out\PropertiesDocument,
	lowtone\wp\admin\postbox\PostBox;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\admin\postbox\handlers\out
 */
class PostBoxesDocument extends PropertiesListDocument {

	public function __construct(array $postBoxes) {
		parent::__construct($postBoxes);

		$this
			->updateBuildOptions(array(
				self::PROPERTIES_LIST_ELEMENT_NAME => "postboxes",
				self::PROPERTIES_DOCUMENT_OPTIONS => array(
						PropertiesDocument::PROPERTIES_ELEMENT_NAME => "postbox", // Ensure use of postbox element name for extended PostBox classes
						PropertiesDocument::PROPERTY_FILTERS => array(
								PostBox::PROPERTY_CONTENT => function($output) {
									if (is_callable($output))
										$output = Util::catchOutput($output);

									return $output;
								}
							),
						PropertiesDocument::BUILD_ELEMENTS => array(
								//PostBox::PROPERTY_NAME,
								PostBox::PROPERTY_TITLE,
								PostBox::PROPERTY_CONTENT
							)
					)
			));
	}

}