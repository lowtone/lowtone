<?php
namespace lowtone\wp\themes\data;
use lowtone\db\records\Record,
	lowtone\db\records\schemata\Schema,
	lowtone\db\records\schemata\properties\Property,
	lowtone\db\records\schemata\properties\types\Int;

class Data extends Record {

	private static $__headers = array(
			self::PROPERTY_NAME => "Theme name",
			self::PROPERTY_THEME_URI => "Theme URI",
			self::PROPERTY_DESCRIPTION => "Description",
			self::PROPERTY_AUTHOR => "Author",
			self::PROPERTY_AUTHOR_URI => "Author URI",
			self::PROPERTY_VERSION => "Version",
			self::PROPERTY_TEMPLATE => "Template",
			self::PROPERTY_STATUS => "Status",
			self::PROPERTY_TAGS => "Tags",
			self::PROPERTY_TEXT_DOMAIN => "Text Domain",
			self::PROPERTY_DOMAIN_PATH => "Domain Path",
			self::PROPERTY_404_SIDEBARS => "404 sidebars",
			self::PROPERTY_SEARCH_SIDEBARS => "Search sidebars",
			self::PROPERTY_TAX_SIDEBARS => "Tax sidebars",
			self::PROPERTY_HOME_SIDEBARS => "Home sidebars",
			self::PROPERTY_ATTACHMENT_SIDEBARS => "Attachment sidebars",
			self::PROPERTY_SINGLE_SIDEBARS => "Single sidebars",
			self::PROPERTY_PAGE_SIDEBARS => "Page sidebars",
			self::PROPERTY_CATEGORY_SIDEBARS => "Category sidebars",
			self::PROPERTY_TAG_SIDEBARS => "Tag sidebars",
			self::PROPERTY_AUTHOR_SIDEBARS => "Author sidebars",
			self::PROPERTY_DATE_SIDEBARS => "Date sidebars",
			self::PROPERTY_ARCHIVE_SIDEBARS => "Archive sidebars",
			self::PROPERTY_SIDEBARS => "Sidebars",
			self::PROPERTY_MENUS => "Menus",
			self::PROPERTY_THUMBNAIL_SIZE => "Thumbnail size",
			self::PROPERTY_IMAGE_SIZES => "Image sizes"
		);
	
	const PROPERTY_NAME = "name",
		PROPERTY_THEME_URI = "theme_uri",
		PROPERTY_DESCRIPTION = "description",
		PROPERTY_AUTHOR = "author",
		PROPERTY_AUTHOR_URI = "author_uri",
		PROPERTY_VERSION = "version",
		PROPERTY_TEMPLATE = "template",
		PROPERTY_STATUS = "status",
		PROPERTY_TAGS = "tags",
		PROPERTY_TEXT_DOMAIN = "text_domain",
		PROPERTY_DOMAIN_PATH = "domain_path",
		PROPERTY_404_SIDEBARS = "404_sidebars",
		PROPERTY_SEARCH_SIDEBARS = "search_sidebars",
		PROPERTY_TAX_SIDEBARS = "tax_sidebars",
		PROPERTY_HOME_SIDEBARS = "home_sidebars",
		PROPERTY_ATTACHMENT_SIDEBARS = "attachment_sidebars",
		PROPERTY_SINGLE_SIDEBARS = "single_sidebars",
		PROPERTY_PAGE_SIDEBARS = "page_sidebars",
		PROPERTY_CATEGORY_SIDEBARS = "category_sidebars",
		PROPERTY_TAG_SIDEBARS = "tag_sidebars",
		PROPERTY_AUTHOR_SIDEBARS = "author_sidebars",
		PROPERTY_DATE_SIDEBARS = "date_sidebars",
		PROPERTY_ARCHIVE_SIDEBARS = "archive_sidebars",
		PROPERTY_SIDEBARS = "sidebars",
		PROPERTY_MENUS = "menus",
		PROPERTY_THUMBNAIL_SIZE = "thumbnail_size",
		PROPERTY_IMAGE_SIZES = "image_sizes";

	// Static
	
	public static function extract($file) {
		$headers = array_map(function($property) {
			return $property["header"];
		}, (array) static::__getSchema());

		$data = get_file_data($file, $headers);

		return new static($data);
	}
	
	public static function __createSchema($defaults = NULL) {
		$parseImageSizes = function($value) {
			if (is_array($value))
				return $value;

			return array_map(function($size) {
				@list($dimensions, $name, $crop) = array_reverse(array_map("trim", explode(":", $size)));

				@list($width, $height) = array_map("trim", explode("x", $dimensions));

				return array(
						"name" => $name ?: $dimensions,
						"width" => $width,
						"height" => $height,
						"crop" => "crop" == $crop
					);
			}, explode(",", $value));
		};

		$sidebars = function($defaults = NULL) {
			return new Int($defaults);
		};

		$defaults = Schema::mergeSchemata(array(
				static::PROPERTY_THUMBNAIL_SIZE => array(
						Property::ATTRIBUTE_SET => function($val) use ($parseImageSizes) {
							return reset($parseImageSizes($val));
						}
					),
				static::PROPERTY_IMAGE_SIZES => array(
						Property::ATTRIBUTE_SET => $parseImageSizes
					),
				static::PROPERTY_404_SIDEBARS => $sidebars(),
				static::PROPERTY_SEARCH_SIDEBARS => $sidebars(),
				static::PROPERTY_TAX_SIDEBARS => $sidebars(),
				static::PROPERTY_HOME_SIDEBARS => $sidebars(array(Int::ATTRIBUTE_DEFAULT_VALUE => 1)),
				static::PROPERTY_ATTACHMENT_SIDEBARS => $sidebars(),
				static::PROPERTY_SINGLE_SIDEBARS => $sidebars(array(Int::ATTRIBUTE_DEFAULT_VALUE => 1)),
				static::PROPERTY_PAGE_SIDEBARS => $sidebars(array(Int::ATTRIBUTE_DEFAULT_VALUE => 1)),
				static::PROPERTY_CATEGORY_SIDEBARS => $sidebars(array(Int::ATTRIBUTE_DEFAULT_VALUE => 1)),
				static::PROPERTY_TAG_SIDEBARS => $sidebars(),
				static::PROPERTY_AUTHOR_SIDEBARS => $sidebars(),
				static::PROPERTY_DATE_SIDEBARS => $sidebars(),
				static::PROPERTY_ARCHIVE_SIDEBARS => $sidebars(),
				static::PROPERTY_SIDEBARS => $sidebars(array(Int::ATTRIBUTE_DEFAULT_VALUE => 4)),
			), $defaults);

		$schema = parent::__createSchema($defaults);

		foreach ($schema as $name => &$property)
			$property["header"] = static::__header($name);

		return $schema;
	}

	private static function __header($property) {
		return static::$__headers[$property] ?: $property;
	}

}