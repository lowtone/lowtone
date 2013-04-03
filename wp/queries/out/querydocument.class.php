<?php
namespace lowtone\wp\queries\out;
use lowtone\dom\Document,
	lowtone\ui\pagination\out\PaginationDocument,
	lowtone\wp\posts\out\PostListDocument,
	lowtone\wp\queries\Query,
	lowtone\wp\queries\pagination\Pagination;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\wp\queries\out
 */
class QueryDocument extends Document {
	
	/**
	 * @var Query
	 */
	protected $itsQuery;
	
	const BUILD_POSTS = "build_posts",
		POST_LIST_DOCUMENT_OPTIONS = "post_list_document_options",
		BUILD_PAGINATION = "build_pagination",
		PAGINATION_DOCUMENT_OPTIONS = "pagination_document_options";
	
	public function __construct(Query $query) {
		parent::__construct();
		
		$this->itsQuery = $query;

		// Set element name filter to handle numeric query vars

		$this->setElementNameFilter(function($name, $document) {
			if (!is_numeric($name))
				return $name;

			$element = $document->createElement("param");
			
			$element->setAttribute("key", $name);

			return $element;
		});
		
	}
	
	public function build(array $options = NULL) {
		$this->updateBuildOptions((array) $options);

		$queryVars = array_filter($this->itsQuery->getQueryVars(), function($value) {
			return $value || is_numeric($value);
		});
		
		$queryElement = $this->createAppendElement("query", array(
			"query_vars" => $queryVars,
			"post_count" => $this->itsQuery->getPostCount(),
			"found_posts" => $this->itsQuery->getFoundPosts(),
			"max_num_pages" => $this->itsQuery->getMaxNumPages(),
			"current_post" => $this->itsQuery->getCurrentPost()
		));
		
		foreach ($this->itsQuery->getContext() as $context) {
			if (is_numeric($context))
				$context = "is_" . $context; // @todo Numeric values can't be attribute names, change this.

			$queryElement->setAttribute($context, "1");
		}
		
		if ($this->getBuildOption(self::BUILD_POSTS)) {
			$postListDocument = new PostListDocument($this->itsQuery->getPosts());
			
			$postListDocument->build($this->getBuildOption(self::POST_LIST_DOCUMENT_OPTIONS));
			
			if ($postListElement = $this->importDocument($postListDocument))
				$queryElement->appendChild($postListElement);
				
		}
		
		if ($this->getBuildOption(self::BUILD_PAGINATION) && $this->itsQuery->getMaxNumPages() > 1) {
			$paginationDocument = new PaginationDocument(new Pagination($this->itsQuery));
			
			$paginationDocument->build($this->getBuildOption(self::PAGINATION_DOCUMENT_OPTIONS));
			
			if ($paginationElement = $this->importDocument($paginationDocument))
				$queryElement->appendChild($paginationElement);
				
		}
		
		return $this;
	}
	
}