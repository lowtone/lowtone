<?php
namespace lowtone\wp\posts\out;
use lowtone\types\objects\out\ObjectListDocument,
	lowtone\wp\posts\Post;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\wp\posts\out
 */
class PostListDocument extends ObjectListDocument {
	
	const POST_LIST_ELEMENT_NAME = "object_list_element_name",
		POST_DOCUMENT_OPTIONS = "object_document_options";

	public function __construct(array $posts) {
		parent::__construct($posts);

		$this->updateBuildOptions(array(
				self::TO_OBJECT => function($object) {
					if ($object instanceof Post)
						return $object;

					return Post::create($object);
				}
			));
	}
	
	public function updateBuildOptions(array $options) {
		parent::updateBuildOptions($options);
		
		return $this->transferBuildOptions(self::POST_DOCUMENT_OPTIONS, array(
			PostDocument::BUILD_LOCALES => $this->getBuildOption(self::BUILD_LOCALES)
		));
	}
	
	public function build(array $options = NULL) {
		parent::build($options);

		$postListElement = $this->documentElement;
		
		// Locales
		
		if ($this->getBuildOption(self::BUILD_LOCALES)) {
			
			$postListElement
				->appendCreateElement("locales", array(
					"title" => __("Posts"),
					"no_posts" => __("No posts.")
				));
			
		}
		
		return $this;
	}
	
}