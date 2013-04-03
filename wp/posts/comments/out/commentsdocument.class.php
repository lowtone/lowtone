<?php
namespace lowtone\wp\posts\comments\out;
use lowtone\dom\Document;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\posts\comments\out
 */
class CommentsDocument extends Document {
	
	protected $itsComments;
	
	const COMMENT_DOCUMENT_OPTIONS = "comment_document_options";
	
	public function __construct(array $comments) {
		parent::__construct();
		
		$this->itsComments = $comments;
	}
	
	public function updateBuildOptions(array $options) {
		parent::updateBuildOptions($options);
		
		return $this->transferBuildOptions(self::COMMENT_DOCUMENT_OPTIONS, array(
			CommentDocument::BUILD_LOCALES => $this->getBuildOption(self::BUILD_LOCALES)
		));
	}
	
	public function build(array $options = NULL) {
		$this->updateBuildOptions($options);
		
		$commentsElement = $this
			->createAppendElement("comments");
		
		// Comments
		
		foreach ($this->itsComments as $comment) {
			$commentDocument = new CommentDocument($comment);
			
			$commentDocument->build($this->getBuildOption(self::COMMENT_DOCUMENT_OPTIONS));
			
			if ($commentElement = $this->importDocument($commentDocument))
				$commentsElement->appendChild($commentElement);
			
		}
		
		// Locales
		
		if ($this->getBuildOption(self::BUILD_LOCALES)) {
			
			$commentsElement
				->appendCreateElement("locales", array(
					"title" => __("Replies to this article"),
					"no_comments" => __("No comments.")
				));
			
		}
		
		return $this;
	}
	
}