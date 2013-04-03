<?php
namespace lowtone\wp\posts\out;
use lowtone\Util,
	lowtone\types\objects\out\ObjectDocument,
	lowtone\wp\dom\interfaces\Document as WpDocument,
	lowtone\wp\posts\Post,
	lowtone\wp\posts\comments\out\CommentsDocument,
	lowtone\wp\posts\comments\out\CommentFormDocument,
	lowtone\wp\users\User,
	lowtone\wp\users\out\UserDocument;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\wp\posts\out
 */
class PostDocument extends ObjectDocument implements WpDocument {

	const BUILD_TERMS = "build_terms",
		BUILD_AUTHOR = "build_author",
		USER_DOCUMENT_OPTIONS = "user_document_options",
		BUILD_THUMBNAIL = "build_thumbnail",
		THUMBNAIL_DOCUMENT_OPTIONS = "thumbnail_document_options",
		BUILD_ATTACHMENTS = "build_attachments",
		BUILD_CUSTOM_FIELDS = "build_custom_fields",
		META_DOCUMENT_OPTIONS = "meta_document_options",
		BUILD_COMMENTS = "build_comments",
		COMMENTS_DOCUMENT_OPTIONS = "comments_document_options",
		BUILD_COMMENT_FORM = "build_comment_form",
		COMMENT_FORM_DOCUMENT_OPTIONS = "comment_form_document_options",
		BUILD_ADJACENT = "build_adjacent",
		ADJACENT_DOCUMENT_OPTIONS = "adjacent_document_options",
		OPTION_ADJACENT = "adjacent";
	
	public function __construct(Post $post) {
		parent::__construct($post);

		$document = $this;
		
		$formatDate = function($date) use ($document) {
			return $date->formatLocalized($document->getBuildOption(PostDocument::DATETIME_FORMAT));
		};

		$dateFormat = get_option(self::DATE_FORMAT);
		$timeFormat = get_option(self::TIME_FORMAT);
		
		$this->updateBuildOptions(array(
			self::OBJECT_ELEMENT_NAME => "post",
			self::BUILD_ATTRIBUTES => array(
				Post::PROPERTY_ID,
				Post::PROPERTY_GUID
			),
			self::BUILD_ELEMENTS => array(
				Post::PROPERTY_POST_AUTHOR,
				Post::PROPERTY_POST_NAME,
				Post::PROPERTY_POST_TITLE,
				Post::PROPERTY_POST_CONTENT,
				Post::PROPERTY_POST_EXCERPT,
				Post::PROPERTY_POST_DATE,
				Post::PROPERTY_POST_MODIFIED,
				Post::PROPERTY_POST_STATUS,
				Post::PROPERTY_COMMENT_STATUS,
				Post::PROPERTY_COMMENT_COUNT, 
				Post::PROPERTY_POST_TYPE,
				Post::PROPERTY_POST_MIME_TYPE,
				"permalink"
			),
			self::DATE_FORMAT => $dateFormat,
			self::TIME_FORMAT => $timeFormat,
			self::DATETIME_FORMAT => $dateFormat . ", " . $timeFormat,
			self::PROPERTY_FILTERS => array(
				Post::PROPERTY_POST_DATE => $formatDate,
				Post::PROPERTY_POST_MODIFIED => $formatDate
			),
			self::STRIP_PROPERTY_PREFIX => "post_"
		));
	}
	
	public function updateBuildOptions(array $options) {
		parent::updateBuildOptions($options);
		
		return $this->transferBuildOptions(self::COMMENTS_DOCUMENT_OPTIONS, array(
			CommentsDocument::BUILD_LOCALES => $this->getBuildOption(self::BUILD_LOCALES)
		));
	}
	
	public function build(array $options = NULL) {
		
		// Set global post

		$this->itsObject->setupPostData();

		// Parent build
		
		parent::build($options);

		$postElement = $this->documentElement;

		// Meta
		
		if ($this->getBuildOption(self::BUILD_CUSTOM_FIELDS)) {
			$customFieldsElement = $postElement->createAppendElement("custom_fields");
			
			foreach ((array) $this->itsObject->getMeta() as $meta) {
				$metaDocument = $meta->createDocument();

				$metaDocument->build();

				if ($metaElement = $this->importDocument($metaDocument))
					$customFieldsElement->appendChild($metaElement);

			}
			
			if ($this->getBuildOption(self::BUILD_LOCALES)) {
				
				$customFieldsElement
					->appendCreateElement("locales", array(
						"title" => __("Extra fields"),
						"no_meta" => __("No extra fields.")
					));
					
			}
		}

		// Thumbnail
		
		if ($this->getBuildOption(self::BUILD_THUMBNAIL) && ($thumbnail = $this->itsObject->getPostThumbnail())) {
			$thumbnailDocument = $thumbnail->createDocument();

			$thumbnailDocument->build($this->getBuildOption(self::THUMBNAIL_DOCUMENT_OPTIONS));

			if ($thumbnailElement = $this->importDocument($thumbnailDocument))
				$postElement->appendChild($thumbnailElement);

		}

		// Attachments
		
		if ($this->getBuildOption(self::BUILD_ATTACHMENTS)) {

		}
		
		// Terms
		
		if ($this->getBuildOption(self::BUILD_TERMS)) {
			$taxonomies = $this->itsObject->getTaxonomies();

			$termsByTaxonomy = array();

			foreach ($this->itsObject->getTerms() as $term) 
				$termsByTaxonomy[$term->taxonomy()][] = $term;

			$taxonomiesElement = $postElement
				->createAppendElement("taxonomies", array_map(function($taxonomy) use ($termsByTaxonomy) {
					$taxonomyDocument = $taxonomy->createDocument();

					$taxonomyDocument->build();
					
					$taxonomyDocument->documentElement->appendCreateElement("terms", $termsByTaxonomy[$taxonomy->getName()]);

					return $taxonomyDocument;
				}, $taxonomies->getObjects()));
			
		}
		
		// Author
		
		if ($this->getBuildOption(self::BUILD_AUTHOR)) {
			$author = new User(array(
				User::PROPERTY_USER_ID => $this->itsObject->getPostAuthor()
			));
			
			$author->load();
			
			$userDocument = new UserDocument($author);
			
			$userDocument->build($this->getBuildOption(self::USER_DOCUMENT_OPTIONS));
			
			if ($userElement = $this->importDocument($userDocument))
				$postElement->appendChild($userElement);
				
		}
		
		// Comments
		
		if ($this->getBuildOption(self::BUILD_COMMENTS)) {
			$this->itsObject->loadComments();
			
			$commentsDocument = new CommentsDocument($this->itsObject->getComments());
			
			$commentsDocument->build($this->getBuildOption(self::COMMENTS_DOCUMENT_OPTIONS));
			
			if ($commentsElement = $this->importDocument($commentsDocument))
				$postElement->appendChild($commentsElement);
			
		}

		// Comment form

		if ($this->getBuildOption(self::BUILD_COMMENT_FORM) && $this->itsObject->getCommentStatus() == 'open') {

			$commentFormDocument = new CommentFormDocument($this->itsObject);

			$commentFormDocument->build($this->getBuildOption(self::COMMENT_FORM_DOCUMENT_OPTIONS));

			if ($commentFormElement = $this->importDocument($commentFormDocument))
				$postElement->appendChild($commentFormElement);

		}

		// Adjacent Posts
		
		if ($this->getBuildOption(self::BUILD_ADJACENT)) {
			$adjacent = $this->itsObject->getAdjacent();

			$adjacentElement = $postElement->createAppendElement("adjacent");

			$adjacentBuildOptions = array_merge((array) $this->getBuildOption(self::ADJACENT_DOCUMENT_OPTIONS), array(self::OPTION_ADJACENT => true));

			if (($left = @$adjacent["left"]) instanceof Post) {

				$adjacentElement->createAppendElement("left", array(
						"post" => $left->__toDocument()->build($adjacentBuildOptions),
						"locales" => array(
							"title" => apply_filters("adjacent_title_left_" . $this->itsObject->getPostType(), apply_filters("adjacent_title_left", __("Previous post")))
						)
					));

			}

			if (($right = @$adjacent["right"]) instanceof Post) {

				$adjacentElement->createAppendElement("right", array(
						"post" => $right->__toDocument()->build($adjacentBuildOptions),
						"locales" => array(
							"title" => apply_filters("adjacent_title_right_" . $this->itsObject->getPostType(), apply_filters("adjacent_title_right", __("Next post")))
						)
					));

			}

		}
		
		// Locales
		
		if ($this->getBuildOption(self::BUILD_LOCALES)) {
			
			$localesElement = $postElement
				->createAppendElement("locales", array(
					"date_title" => __("Date"),
					"modified_title" => __("Last modified"),
					"read_more" => __("Read more")
				));
			
		}

		do_action("build_post_document", $this, $this->itsObject);
		
		return $this;
	}

	protected function extractProperties() {
		$properties = parent::extractProperties();

		$properties["permalink"] = get_permalink();

		$properties[Post::PROPERTY_POST_TITLE] = apply_filters("post_document_title", Util::catchOutput("the_title"));
		$properties[Post::PROPERTY_POST_CONTENT] = apply_filters("post_document_content", Util::catchOutput("the_content"));

		return $properties;
	}
	
}