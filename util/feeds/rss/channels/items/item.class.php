<?php
namespace lowtone\util\feeds\rss\channels\items;
use lowtone\Util,
	lowtone\db\records\Record,
	lowtone\util\feeds\rss\channels\items\enclosures\Enclosure,
	lowtone\wp\posts\Post,
	lowtone\wp\attachments\Attachment;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\util\feeds\rss\channels\items
 */
class Item extends Record {
	
	const PROPERTY_TITLE = "title",
		PROPERTY_LINK = "link",
		PROPERTY_DESCRIPTION = "description",
		PROPERTY_AUTHOR = "author",
		PROPERTY_CATEGORY = "category",
		PROPERTY_COMMENTS = "comments",
		PROPERTY_ENCLOSURE = "enclosure",
		PROPERTY_GUID = "guid",
		PROPERTY_PUB_DATE = "pubDate",
		PROPERTY_SOURCE = "source";

	const CREATE_PROPERTY_FILTERS = "create_property_filters",
		CREATE_FULL_DESCRIPTION = "create_full_description",
		CREATE_IMAGE = "create_image",
		IMAGE_SIZE = "image_size";

	// Static
	
	public static function createFromPost(Post $post, array $options = NULL) {
		$options = array_merge(array(
				self::CREATE_IMAGE => true,
				self::IMAGE_SIZE => "medium"
			), (array) $options);

		$post->setupPostData();

		$item = new static(array(
				self::PROPERTY_TITLE => html_entity_decode(get_the_title_rss()),
				self::PROPERTY_LINK => $post->getPermaLink(),
				self::PROPERTY_DESCRIPTION => html_entity_decode(@$options[self::CREATE_FULL_DESCRIPTION] ? Util::catchOutput("the_content_rss") : Util::catchOutput("the_excerpt_rss")),
				self::PROPERTY_PUB_DATE => $post->getPostDate()->format("r"),
				self::PROPERTY_GUID => $post->getGuid(),
				self::PROPERTY_COMMENTS => $post->getPermaLink() . "#comments"
			));

		if (@$options[self::CREATE_IMAGE] && ($imageId = get_post_thumbnail_id($post->getPostId())) && ($image = wp_get_attachment_image_src($imageId, @$options[self::IMAGE_SIZE]))) {

			$attachment = Attachment::findById($imageId);

			$item[self::PROPERTY_ENCLOSURE] = new Enclosure(array(
					Enclosure::PROPERTY_URL => $image[0],
					Enclosure::PROPERTY_TYPE => $attachment->getPostMimeType(),
					Enclosure::PROPERTY_LENGTH => $attachment->getFileSize() ?: NULL
				));

		}

		$properties = $item->filterProperties(@$options[self::CREATE_PROPERTY_FILTERS], array($post, @$attachment));

		$properties = apply_filters("rss_create_item_properties", $properties, $post, @$attachment);

		return new static($properties);
	}

}