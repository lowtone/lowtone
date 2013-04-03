<?php
namespace lowtone\util\feeds\rss\channels;
use lowtone\db\records\Record,
	lowtone\util\feeds\rss\channels\items\Item,
	lowtone\wp\queries\Query;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\util\feeds\rss\channels
 */
class Channel extends Record {

	protected $itsItems = array();
	
	const PROPERTY_TITLE = "title",
		PROPERTY_LINK = "link",
		PROPERTY_DESCRIPTION = "description",
		PROPERTY_LANGUAGE = "language",
		PROPERTY_COPYRIGHT = "copyright",
		PROPERTY_MANAGING_EDITOR = "managingEditor",
		PROPERTY_WEB_MASTER = "webMaster",
		PROPERTY_PUB_DATE = "pubDate",
		PROPERTY_LAST_BUILD_DATE = "lastBuildDate",
		PROPERTY_CATEGORY = "category",
		PROPERTY_GENERATOR = "generator",
		PROPERTY_DOCS = "docs",
		PROPERTY_CLOUD = "cloud",
		PROPERTY_TTL = "ttl",
		PROPERTY_IMAGE = "image",
		PROPERTY_RATING = "rating",
		PROPERTY_TEXT_INPUT = "textInput",
		PROPERTY_SKIP_HOURS = "skipHours",
		PROPERTY_SKIP_DAYS = "skipDays";

	const CREATE_ITEM_OPTIONS = "create_item_options";

	public function addItem(Item $item) {
		$this->itsItems[] = $item;

		return $this;
	}

	// Getters
	
	public function getItems() {return $this->itsItems;}

	// Setters
	
	public function setItems(array $items) {$this->itsItems = $items; return $this;}

	// Static
	
	public static function createFromQuery(Query $query, array $properties = NULL, array $options = NULL) {
		$channel = new static($properties);

		foreach ($query->getPosts() as $post) 
			$channel->addItem(Item::createFromPost($post, @$options[self::CREATE_ITEM_OPTIONS]));

		return $channel;
	}

	public static function __getDocumentClass() {
		return "lowtone\\util\\feeds\\rss\\channels\\out\\ChannelDocument";
	}

}