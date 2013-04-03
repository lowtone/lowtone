<?php
namespace lowtone\wp\posts;
use ErrorException,
	lowtone\db\records\Record,
	lowtone\db\records\collections\Collection,
	lowtone\db\records\schemata\Schema,
	lowtone\db\records\schemata\properties\Property,
	lowtone\net\URL,
	lowtone\types\datetime\DateTime,
	lowtone\wp\taxonomies\Taxonomy,
	lowtone\wp\terms\Term,
	lowtone\wp\posts\comments\Comment,
	lowtone\wp\posts\meta\Meta;

/**
 * Post
 * An Record implementation for WordPress Post objects.
 * 
 * @property int ID The unique ID for the Post.
 * @property int post_author The ID for the author of the Post.
 * @property DateTime post_date The date and time of the Post.
 * @property DateTime post_date_gmt The GMT date and time of the Post
 * @property string post_content The Post's content.
 * @property string post_title The Posts's title.
 * @property string post_excerpt The Post excerpt.
 * @property string post_status The Post status (publish, pending, draft, 
 * private, static, object, attachment, inherit, future or trash).
 * @property string comment_status The comment status (open, closed or 
 * registered_only).
 * @property string ping_status The pingback/trackback status (open or closed).
 * @property string post_password The Post password.
 * @property string post_name The Post's URL slug.
 * @property string to_ping URLs to be pinged.
 * @property string pinged URLs already pinged.
 * @property DateTime post_modified The last modified date and time of the Post.
 * @property DateTime post_modified_gmt The last modified GMT date and time of 
 * the Post.
 * @property string post_content_filtered 
 * @property int post_parent The parent Post's ID (for attachments, etc).
 * @property string post_guid A link to the post.
 * @property int menu_order
 * @property string post_type The Post's type (post, page, attachment etc).
 * @property string mime_type The Post's mime type.
 * @property int comment_count Number of comments.
 * @property array ancestors
 * @property mixed filter
 * 
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\posts
 */
class Post extends Record implements interfaces\Post, interfaces\Registrable {
	
	/**
	 * @var Collection
	 */
	protected $itsMeta;
	
	/**
	 * @var Collection
	 */
	protected $itsTaxonomies;
	
	/**
	 * @var Collection
	 */
	protected $itsTerms;
	
	/**
	 * @var Collection
	 */
	protected $itsComments;
	
	const PROPERTY_ID = "ID",
		PROPERTY_POST_AUTHOR = "post_author",
		PROPERTY_POST_DATE = "post_date",
		PROPERTY_POST_DATE_GMT = "post_date_gmt",
		PROPERTY_POST_CONTENT = "post_content",
		PROPERTY_POST_TITLE = "post_title",
		PROPERTY_POST_EXCERPT = "post_excerpt",
		PROPERTY_POST_STATUS = "post_status",
		PROPERTY_COMMENT_STATUS = "comment_status",
		PROPERTY_PING_STATUS = "ping_status",
		PROPERTY_POST_PASSWORD = "post_password",
		PROPERTY_POST_NAME = "post_name",
		PROPERTY_TO_PING = "to_ping",
		PROPERTY_PINGED = "pinged",
		PROPERTY_POST_MODIFIED = "post_modified",
		PROPERTY_POST_MODIFIED_GMT = "post_modified_gmt",
		PROPERTY_POST_CONTENT_FILTERED = "post_content_filtered",
		PROPERTY_POST_PARENT = "post_parent",
		PROPERTY_GUID = "guid",
		PROPERTY_MENU_ORDER = "menu_order",
		PROPERTY_POST_TYPE = "post_type",
		PROPERTY_POST_MIME_TYPE = "post_mime_type",
		PROPERTY_COMMENT_COUNT = "comment_count",
		PROPERTY_ANCESTORS = "ancestors",
		PROPERTY_FILTER = "filter";

	const STATUS_PUBLISH = "publish",
		STATUS_PENDING = "pending",
		STATUS_DRAFT = "draft",
		STATUS_PRIVATE = "private",
		STATUS_STATIC = "static",
		STATUS_OBJECT = "object",
		STATUS_ATTACHMENT = "attachment",
		STATUS_INHERIT = "inherit",
		STATUS_FUTURE = "future",
		STATUS_TRASH = "trash",
		STATUS_OPEN = "open",
		STATUS_CLOSED = "closed",
		STATUS_REGISTERED_ONLY = "registered_only";

	const META_THUMBNAIL_ID = "_thumbnail_id";
	
	/**
	 * Constructor for the Post object
	 * @param array|NULL $post An optional array (or object) that defines the 
	 * properties for the Post object.
	 */
	public function __construct($post = NULL) {
		parent::__construct($post);
		
		if ($post instanceof self) {

			$this->itsMeta = $post->getMeta();
			$this->itsComments = $post->getComments();
			$this->itsTerms = $post->getTerms();

		}
		
	}
	
	// Load
	
	/**
	 * Load the properties for the post. To load the properies it is required 
	 * that the ID property is defined.
	 * @throws ErrorException Throws an exception if either no ID property is 
	 * defined or WordPress failed to load the properties for a post with the 
	 * specified ID.
	 * @return Post Returns the Post object on success. 
	 */
	public function load() {
		if (!is_numeric($postId = $this->getPostId())) 
			throw new ErrorException("Failed to load a Post without a numeric ID", 0, E_NOTICE);
		
		if (is_null($post = get_post($postId, ARRAY_A))) 
			throw new ErrorException(sprintf("Failed loading a Post with ID '%s'", $postId), 0, E_NOTICE);
		
		$this->exchangeArray($post);
			
		return $this;
	}
	
	/**
	 * Load the meta for the Post object.
	 * @throws ErrorException Throws an exception if the ID property isn't 
	 * defined.
	 * @return Post Returns the Post object on success.
	 */
	public function loadMeta() {
		if (!is_numeric($postId = $this->getPostId())) 
			throw new ErrorException("Failed to load Meta for a Post without a numeric ID", 0, E_NOTICE);
		
		$this->itsMeta = Meta::find(array(
				Meta::PROPERTY_POST_ID => $postId
			));
		
		return $this;
	}

	public function loadTaxonomies() {
		$taxonomies = get_taxonomies(array(
					"object_type" => array($this->getPostType())
				), "objects");

		$this->setTaxonomies($taxonomies);

		return $this;
	}
	
	public function loadTerms() {
		if (!is_numeric($postId = $this->getPostId())) 
			throw new ErrorException("Failed to load tags for a Post without a numeric ID", 0, E_NOTICE);

		$taxonomies = array_map(function($taxonomy) {
			return $taxonomy->getName();
		}, $this->getTaxonomies()->getObjects());
		
		if (is_wp_error($terms = wp_get_object_terms($postId, $taxonomies)))
			$terms = array();

		$this->setTerms($terms);
		
		return $this;
	}
	
	/**
	 * Load the comments for the Post object.
	 * @param array|NULL $options An optional list of load options.
	 * @throws ErrorException Throws an exception if the ID property isn't 
	 * defined.
	 * @return Post Returns the Post object on success.
	 */
	public function loadComments(array $options = NULL) {
		if (!is_numeric($postId = $this->getPostId())) 
			throw new ErrorException("Failed to load Comments for a Post without a numeric ID", 0, E_NOTICE);
		
		$options = array_merge((array) $options, array(
			Comment::PROPERTY_POST_ID => $postId
		));
		
		$this->setComments(get_comments($options));
			
		return $this;
	}
	
	// Update
	
	/**
	 * Update the database entry for the Post object with the defined ID 
	 * property or insert a new entry if no ID property is defined.
	 * @return Post Returns the Post object on success.
	 */
	public function save() {
		$data = array_filter(array_map(function($value) {return (string) $value;}, (array) $this));

		if (0 === ($postId = wp_insert_post($data)))
			throw new ErrorException("Failed to save post");

		$this[self::PROPERTY_ID] = $postId;
		
		return $this;
	}
	
	// Delete
	
	/**
	 * Remove the database entry for the Post object with the defined ID 
	 * property.
	 * @return Post Returns the Post object on success.
	 */
	public function delete() {
		wp_delete_post($this->getPostId());
		
		return $this;
	}

	// Setup post data
	
	public function setupPostData() {
		setup_postdata($GLOBALS["post"] = (object) (array) $this); // Create standard object from Post

		return $this;
	}
	
	// Getters
	
	public function permalink() {
		return URL::fromString(get_permalink($this->getPostId()));
	}

	// Attachments
	
	public function getPostThumbnail() {
		if (!($thumbnailId = get_post_thumbnail_id($this->getPostId())))
			return false;

		if (!($post = get_post($thumbnailId)))
			return false;

		return new thumbnails\Thumbnail($post);
	}
	
	// Meta related
	
	/**
	 * Get the Meta for the Post object.
	 * @see Collection::find()
	 */
	public function getMeta() {
		if (!($this->itsMeta instanceof Collection))
			$this->loadMeta();

		return call_user_func_array(array($this->itsMeta, "find"), func_get_args());
	}
	
	// Comments related
	
	/**
	 * Get the Comment objects for this Post.
	 * @param array|NULL $filter An optional filter for the returned Comment 
	 * objects.
	 * @return array Returns an array of Post objects.
	 */
	public function getComments() {
		if (!($this->itsComments instanceof Collection))
			$this->loadComments();

		return call_user_func_array(array($this->itsComments, "find"), func_get_args());
	}
	
	// Term related
	
	public function getTaxonomies() {
		if (!($this->itsTaxonomies instanceof Collection)) 
			$this->loadTaxonomies();

		return call_user_func_array(array($this->itsTaxonomies, "find"), func_get_args());
	}
	
	public function getTerms() {
		if (!($this->itsTerms instanceof Collection))
			$this->loadTerms();

		return call_user_func_array(array($this->itsTerms, "find"), func_get_args());
	}

	// Adjacent Posts
	
	public function getAdjacent() {
		$conditions = vsprintf("%s=%s AND %s%%s%s", array(
				self::__escapeIdentifier(self::PROPERTY_POST_TYPE),
				self::__escape($this->getPostType()),
				($postDateColumn = self::__escapeIdentifier(self::PROPERTY_POST_DATE)),
				self::__escape((string) $this->getPostDate())
			));

		$left = reset(self::all(array(
				self::OPTION_CONDITIONS => sprintf($conditions, "<"),
				self::OPTION_LIMIT => 1,
				self::OPTION_ORDER => sprintf("%s DESC", $postDateColumn)
			))) ?: NULL;
		
		$right = reset(self::all(array(
				self::OPTION_CONDITIONS => sprintf($conditions, ">"),
				self::OPTION_LIMIT => 1,
				self::OPTION_ORDER => sprintf("%s ASC", $postDateColumn)
			))) ?: NULL;

		return compact("left", "right");
	}
	
	// Post property getters
	
	public function getPostId() {return $this->__get(self::PROPERTY_ID);}
	public function getPostAuthor() {return $this->__get(self::PROPERTY_POST_AUTHOR);}
	public function getPostDate() {return $this->__get(self::PROPERTY_POST_DATE);}
	public function getPostDateGmt() {return $this->__get(self::PROPERTY_POST_DATE_GMT);}
	public function getPostContent() {return $this->__get(self::PROPERTY_POST_CONTENT);}
	public function getPostTitle() {return $this->__get(self::PROPERTY_POST_TITLE);}
	public function getPostExcerpt() {return $this->__get(self::PROPERTY_POST_EXCERPT);}
	public function getPostStatus() {return $this->__get(self::PROPERTY_POST_STATUS);}
	public function getCommentStatus() {return $this->__get(self::PROPERTY_COMMENT_STATUS);}
	public function getPingStatus() {return $this->__get(self::PROPERTY_PING_STATUS);}
	public function getPostPassword() {return $this->__get(self::PROPERTY_POST_PASSWORD);}
	public function getPostName() {return $this->__get(self::PROPERTY_POST_NAME);}
	public function getToPing() {return $this->__get(self::PROPERTY_TO_PING);}
	public function getPinged() {return $this->__get(self::PROPERTY_PINGED);}
	public function getPostModified() {return $this->__get(self::PROPERTY_POST_MODIFIED);}
	public function getPostModifiedGmt() {return $this->__get(self::PROPERTY_POST_MODIFIED_GMT);}
	public function getPostContentFiltered() {return $this->__get(self::PROPERTY_POST_CONTENT_FILTERED);}
	public function getPostParent() {return $this->__get(self::PROPERTY_POST_PARENT);}
	public function getGuid() {return $this->__get(self::PROPERTY_GUID);}
	public function getMenuOrder() {return $this->__get(self::PROPERTY_MENU_ORDER);}
	public function getPostType() {return $this->__get(self::PROPERTY_POST_TYPE);}
	public function getPostMimeType() {return $this->__get(self::PROPERTY_POST_MIME_TYPE);}
	public function getCommentCount() {return $this->__get(self::PROPERTY_COMMENT_COUNT);}
	public function getAncestors() {return $this->__get(self::PROPERTY_ANCESTORS);}
	public function getFilter() {return $this->__get(self::PROPERTY_FILTER);}
	
	// Setters
	
	// Meta related
	
	/**
	 * Set the Meta for the Post object.
	 * @see MetaHandler::setMeta()
	 */
	public function setMeta($meta) {
		$this->itsMeta = Meta::__createCollection($meta);
		
		return $this;
	}
	
	// Comments related
	
	/**
	 * Set the comments for the Post object.
	 * @param array $comments An array of Comment objects or properties.
	 * @return Post Returns the Post object on succes.
	 */
	public function setComments(array $comments) {
		$this->itsComments = Comment::__createCollection($comments);
		
		return $this;
	}
	
	// Term related
	
	public function setTaxonomies(array $taxonomies) {
		$this->itsTaxonomies = Taxonomy::__createCollection($taxonomies);

		return $this;
	}
	
	public function setTerms(array $terms) {
		$this->itsTerms = Term::__createCollection($terms);
		
		return $this;
	}
	
	// Post property setters
	
	public function setPostId($postId) {return $this->__set(self::PROPERTY_ID, $postId);}
	public function setPostAuthor($postAuthor) {return $this->__set(self::PROPERTY_POST_AUTHOR, $postAuthor);}
	public function setPostDate($postDate) {return $this->__set(self::PROPERTY_POST_DATE, $postDate);}
	public function setPostDateGmt($postDateGmt) {return $this->__set(self::PROPERTY_POST_DATE_GMT, $postDateGmt);}
	public function setPostContent($postContent) {return $this->__set(self::PROPERTY_POST_CONTENT, $postContent);}
	public function setPostTitle($postTitle) {return $this->__set(self::PROPERTY_POST_TITLE, $postTitle);}
	public function setPostExcerpt($postExcerpt) {return $this->__set(self::PROPERTY_POST_EXCERPT, $postExcerpt);}
	public function setPostStatus($postStatus) {return $this->__set(self::PROPERTY_POST_STATUS, $postStatus);}
	public function setCommentStatus($commentStatus) {return $this->__set(self::PROPERTY_COMMENT_STATUS, $commentStatus);}
	public function setPingStatus($pingStatus) {return $this->__set(self::PROPERTY_PING_STATUS, $pingStatus);}
	public function setPostPassword($postPassword) {return $this->__set(self::PROPERTY_POST_PASSWORD, $postPassword);}
	public function setPostName($postName) {return $this->__set(self::PROPERTY_POST_NAME, $postName);}
	public function setToPing($toPing) {return $this->__set(self::PROPERTY_TO_PING, $toPing);}
	public function setPinged($pinged) {return $this->__set(self::PROPERTY_PINGED, $pinged);}
	public function setPostModified($postModified) {return $this->__set(self::PROPERTY_POST_MODIFIED, $postModified);}
	public function setPostModifiedGmt($postModifiedGmt) {return $this->__set(self::PROPERTY_POST_MODIFIED_GMT, $postModifiedGmt);}
	public function setPostContentFiltered($postContentFiltered) {return $this->__set(self::PROPERTY_POST_CONTENT_FILTERED, $postContentFiltered);}
	public function setPostParent($postParent) {return $this->__set(self::PROPERTY_POST_PARENT, $postParent);}
	public function setGuid($guid) {return $this->__set(self::PROPERTY_GUID, $guid);}
	public function setMenuOrder($menuOrder) {return $this->__set(self::PROPERTY_MENU_ORDER, $menuOrder);}
	public function setPostType($postType) {return $this->__set(self::PROPERTY_POST_TYPE, $postType);}
	public function setPostMimeType($postMimeType) {return $this->__set(self::PROPERTY_POST_MIME_TYPE, $postMimeType);}
	public function setCommentCount($commentCount) {return $this->__set(self::PROPERTY_COMMENT_COUNT, $commentCount);}
	public function setAncestors($ancestors) {return $this->__set(self::PROPERTY_ANCESTORS, $ancestors);}
	public function setFilter($filter) {return $this->__set(self::PROPERTY_FILTER, $filter);}

	// Post Type
	
	/**
	 * Check the Post Type. Allows to:
	 * - Check if the post_type property matches the type defined by the class.
	 * - Check if the post_type property matches a given type.
	 * - Check if a post with a given ID matches the type defined by the class.
	 * - Check if a post with a given ID matches a given type.
	 * - Check if a post with a given ID matches the type defined by the class 
	 * of the called object.
	 * @param string|NULL $type The required Post Type. Defaults to the value 
	 * returned by __postType() for the called class.
	 * @return bool Returns TRUE if the subject is of the required class or 
	 * FALSE if not.
	 */
	public function is($type = NULL) {
		$static = !(isset($this) && $this instanceof Post);
		$class = get_called_class();

		if (is_numeric($type)) {
			list($id, $type) = func_get_args();

			$post = new $class(get_post($id));

			return $post->is($type);
		}

		if ($static)
			throw new \ErrorException(sprintf("Can not statically call %s() without an ID", __METHOD__));

		if (!is_string($type))
			$type = $this->__postType();

		return $this->post_type() == $type;
	}

	// Static
	
	public static function create($properties = NULL, array $options = NULL) {
		$properties = (array) $properties;

		$options = array_merge(array(
				self::OPTION_CLASS => @$GLOBALS["wp_post_types"][$properties[self::PROPERTY_POST_TYPE]]->{self::OPTION_POST_CLASS} ?: get_called_class()
			), (array) $options);

		return parent::create($properties, $options);
	}
	
	public static function __createSchema() {
		$convertToDateTime = function($val) {
			return DateTime::createFromString($val);
		};

		$timezone = new \DateTimeZone("UTC");

		$convertToDateTimeUTC = function($val) use ($timezone) {
			return DateTime::createFromString($val, $timezone);
		};

		$schema = new Schema(array(
			self::PROPERTY_ID => array(
					Property::ATTRIBUTE_INDEXES => array(Property::INDEX_PRIMARY_KEY),
					Property::ATTRIBUTE_TYPE => Property::TYPE_INT,
					Property::ATTRIBUTE_LENGTH => 20 // BIGINT
				),
			self::PROPERTY_POST_AUTHOR => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_INT,
					Property::ATTRIBUTE_LENGTH => 20, // BIGINT
					Property::ATTRIBUTE_DEFAULT_VALUE => 0
				),
			self::PROPERTY_POST_DATE => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_DATETIME,
					Property::ATTRIBUTE_SET => $convertToDateTime,
					Property::ATTRIBUTE_UNSERIALIZE => $convertToDateTime,
					Property::ATTRIBUTE_DEFAULT_VALUE => "0000-00-00 00:00:00"
				),
			self::PROPERTY_POST_DATE_GMT => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_DATETIME,
					Property::ATTRIBUTE_SET => $convertToDateTimeUTC,
					Property::ATTRIBUTE_UNSERIALIZE => $convertToDateTimeUTC,
					Property::ATTRIBUTE_DEFAULT_VALUE => "0000-00-00 00:00:00"
				),
			self::PROPERTY_POST_CONTENT => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_STRING,
					Property::ATTRIBUTE_LENGTH => 4294967295 // LONGTEXT
				),
			self::PROPERTY_POST_TITLE => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_STRING,
					Property::ATTRIBUTE_LENGTH => 65535 // TEXT
				),
			self::PROPERTY_POST_EXCERPT => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_STRING,
					Property::ATTRIBUTE_LENGTH => 65535 // TEXT
				),
			self::PROPERTY_POST_STATUS => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_STRING,
					Property::ATTRIBUTE_LENGTH => 20,
					Property::ATTRIBUTE_DEFAULT_VALUE => self::STATUS_PUBLISH
				),
			self::PROPERTY_COMMENT_STATUS => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_STRING,
					Property::ATTRIBUTE_LENGTH => 20,
					Property::ATTRIBUTE_DEFAULT_VALUE => self::STATUS_OPEN
				),
			self::PROPERTY_PING_STATUS => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_STRING,
					Property::ATTRIBUTE_LENGTH => 20,
					Property::ATTRIBUTE_DEFAULT_VALUE => self::STATUS_OPEN
				),
			self::PROPERTY_POST_PASSWORD => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_STRING,
					Property::ATTRIBUTE_LENGTH => 20
				),
			self::PROPERTY_POST_NAME => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_STRING,
					Property::ATTRIBUTE_LENGTH => 100
				),
			self::PROPERTY_TO_PING => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_STRING,
					Property::ATTRIBUTE_LENGTH => 65535 // TEXT
				),
			self::PROPERTY_PINGED => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_STRING,
					Property::ATTRIBUTE_LENGTH => 65535 // TEXT
				),
			self::PROPERTY_POST_MODIFIED => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_DATETIME,
					Property::ATTRIBUTE_SET => $convertToDateTime,
					Property::ATTRIBUTE_UNSERIALIZE => $convertToDateTime,
					Property::ATTRIBUTE_DEFAULT_VALUE => "0000-00-00 00:00:00"
				),
			self::PROPERTY_POST_MODIFIED_GMT => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_DATETIME,
					Property::ATTRIBUTE_SET => $convertToDateTimeUTC,
					Property::ATTRIBUTE_UNSERIALIZE => $convertToDateTimeUTC,
					Property::ATTRIBUTE_DEFAULT_VALUE => "0000-00-00 00:00:00"
				),
			self::PROPERTY_POST_CONTENT_FILTERED => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_STRING,
					Property::ATTRIBUTE_LENGTH => 4294967295 // LONGTEXT
				),
			self::PROPERTY_POST_PARENT => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_INT,
					Property::ATTRIBUTE_LENGTH => 20, // BIGINT
					Property::ATTRIBUTE_DEFAULT_VALUE => 0
				),
			self::PROPERTY_GUID => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_STRING,
					Property::ATTRIBUTE_LENGTH => 255
				),
			self::PROPERTY_MENU_ORDER => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_INT,
					Property::ATTRIBUTE_LENGTH => 11,
					Property::ATTRIBUTE_DEFAULT_VALUE => 0
				),
			self::PROPERTY_POST_TYPE => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_STRING,
					Property::ATTRIBUTE_LENGTH => 20,
					Property::ATTRIBUTE_DEFAULT_VALUE => strtolower(end(split("\\\\", get_called_class())))
				),
			self::PROPERTY_POST_MIME_TYPE => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_STRING,
					Property::ATTRIBUTE_LENGTH => 100
				),
			self::PROPERTY_COMMENT_COUNT => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_INT,
					Property::ATTRIBUTE_LENGTH => 20, // BIGINT
					Property::ATTRIBUTE_DEFAULT_VALUE => 0
				)
		));
	
		$schema->setRecordClass(get_called_class());

		return $schema;
	}
	
	public static function __getDocumentClass() {
		return "lowtone\\wp\\posts\\out\\PostDocument";
	}
	
	public static function __getCollectionClass() {
		return "lowtone\\wp\\posts\\collections\\Collection";
	}

	public static function __postType() {
		return strtolower(end(split("\\\\", get_called_class())));
	}

	public static function __register(array $options = NULL) {
		$postType = strtolower($name = ucfirst(@$options[self::OPTION_POST_TYPE] ?: static::__postType()));

		$plural = $name . "s";
		$textdomain = @$options[self::OPTION_TEXTDOMAIN] ?: "default";

		$options = array_merge(array(
				self::OPTION_LABELS => array(
						self::LABEL_NAME => __($plural, $textdomain),
						self::LABEL_SINGULAR_NAME => __($name, $textdomain),
						self::LABEL_ADD_NEW => ($addNew = __(sprintf("New %s", $name), $textdomain)),
						self::LABEL_ALL_ITEMS => __(sprintf("All %s", $plural), $textdomain),
						self::LABEL_ADD_NEW_ITEM => __(sprintf("Add New %s", $name), $textdomain),
						self::LABEL_EDIT_ITEM => __(sprintf("Edit %s", $name), $textdomain),
						self::LABEL_NEW_ITEM => $addNew,
						self::LABEL_VIEW_ITEM => __(sprintf("View %s", $name), $textdomain),
						self::LABEL_SEARCH_ITEMS => __(sprintf("Search %s", $plural), $textdomain),
						self::LABEL_NOT_FOUND => __(sprintf("No %s found", $plural), $textdomain),
						self::LABEL_NOT_FOUND_IN_TRASH => __(sprintf("No %s found in Trash", $plural), $textdomain),
					),
				self::OPTION_POST_CLASS => get_called_class()
			), (array) $options);

		$result = register_post_type($postType, $options);

		if ($result instanceof \WP_Error)
			throw $result;

		return $result;
	}

	public static function __registerPostClass($postType, $class) {
		global $wp_post_types;

		if (!isset($wp_post_types[$postType]))
			return false;

		$wp_post_types[$postType]->{self::OPTION_POST_CLASS} = $class;

		return true;
	}
	
}