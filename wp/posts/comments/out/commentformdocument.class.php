<?php
namespace lowtone\wp\posts\comments\out;
use lowtone\dom\Document,
	lowtone\wp\dom\interfaces\Document as WpDocument,
	lowtone\wp\posts\Post,
	lowtone\wp\posts\comments\forms\CommentForm,
	lowtone\types\objects\out\ObjectDocument;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\posts\comments\out
 */
class CommentFormDocument extends Document implements WpDocument {

	/**
	 * @var Post
	 */
	protected $itsPost;

	public function __construct(Post $post) {
		parent::__construct();

		$this->itsPost = $post;

		$this->updateBuildOptions(array(
				self::BUILD_LOCALES => true
			));

	}

	public function build(array $options = NULL) {
		$this->updateBuildOptions((array) $options);

		$commentFormElement = $this->createAppendElement("comment_form");

		$formDocument = CommentForm::create()
			->build()
			->createDocument()
			->build(array(
					ObjectDocument::OBJECT_ELEMENT_NAME => "form"
				));

		if ($formElement = $this->importDocument($formDocument))
			$commentFormElement->appendChild($formElement);

		if ($this->getBuildOption(self::BUILD_LOCALES)) {

			$commentFormElement->createAppendElement("locales", array(
					"title" => __("Post comment")
				));

		}

		return $this;
	}

}