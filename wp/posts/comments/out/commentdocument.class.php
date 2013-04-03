<?php
namespace lowtone\wp\posts\comments\out;
use lowtone\types\objects\out\ObjectDocument,
	lowtone\wp\dom\interfaces\Document as WpDocument,
	lowtone\wp\posts\comments\Comment;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\posts\comments\out
 */
class CommentDocument extends ObjectDocument implements WpDocument {
	
	public function __construct($comment) {
		parent::__construct(new Comment($comment));
		
		$document = $this;
		
		$formatDate = function($date) use ($document) {
			return $date->formatLocalized($document->getBuildOption(CommentDocument::DATETIME_FORMAT));
		};

		$dateFormat = get_option(self::DATE_FORMAT);
		$timeFormat = get_option(self::TIME_FORMAT);
		
		$this->updateBuildOptions(array(
			self::BUILD_ATTRIBUTES => array(
				Comment::PROPERTY_ID
			),
			self::BUILD_ELEMENTS => array(
				Comment::PROPERTY_POST_ID,
				Comment::PROPERTY_AUTHOR,
				Comment::PROPERTY_AUTHOR_EMAIL,
				Comment::PROPERTY_AUTHOR_URL,
				Comment::PROPERTY_AUTHOR_IP,
				Comment::PROPERTY_COMMENT_DATE,
				Comment::PROPERTY_CONTENT,
				Comment::PROPERTY_KARMA,
				Comment::PROPERTY_APPROVED,
				Comment::PROPERTY_AGENT,
				Comment::PROPERTY_TYPE,
				Comment::PROPERTY_PARENT,
				Comment::PROPERTY_USER_ID
			),
			self::DATE_FORMAT => $dateFormat,
			self::TIME_FORMAT => $timeFormat,
			self::DATETIME_FORMAT => $dateFormat . ", " . $timeFormat,
			self::PROPERTY_FILTERS => array(
				Comment::PROPERTY_COMMENT_DATE => $formatDate,
				Comment::PROPERTY_COMMENT_DATE_GMT => $formatDate
			),
			self::STRIP_PROPERTY_PREFIX => "comment_"
		));
	}
	
	public function build(array $options = NULL) {
		parent::build($options);

		$commentElement = $this->documentElement;
		
		// Locales
		
		if ($this->getBuildOption(self::BUILD_LOCALES)) {
			
			$commentElement
				->appendCreateElement("locales", array(
					"author" => __("Author"),
					"author_email" => __("Email"),
					"author_url" => __("URL"),
					"author_ip" => __("IP"),
					"date" => __("Date / Time"),
					"content" => __("Message"),
					"karma" => __("Karma"),
					"approved" => __("Approved"),
					"agent" => __("User agent"),
					"type" => __("Type")
				));
			
		}
		
		return $this;
	}
	
}