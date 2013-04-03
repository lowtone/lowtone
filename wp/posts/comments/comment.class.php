<?php
namespace lowtone\wp\posts\comments;
use ErrorException,
	lowtone\db\records\Record,
	lowtone\wp\meta\collections\Collection,
	lowtone\wp\posts\comments\meta\Meta;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\posts\comments
 */
class Comment extends Record {
	
	/**
	 * @var Collection
	 */
	protected $itsMeta;
	
	const PROPERTY_ID = "comment_ID",
		PROPERTY_POST_ID = "comment_post_ID",
		PROPERTY_AUTHOR = "comment_author",
		PROPERTY_AUTHOR_EMAIL = "comment_author_email",
		PROPERTY_AUTHOR_URL = "comment_author_url",
		PROPERTY_AUTHOR_IP = "comment_author_IP",
		PROPERTY_COMMENT_DATE = "comment_date",
		PROPERTY_COMMENT_DATE_GMT = "comment_date_gmt",
		PROPERTY_CONTENT = "comment_content",
		PROPERTY_KARMA = "comment_karma",
		PROPERTY_APPROVED = "comment_approved",
		PROPERTY_AGENT = "comment_agent",
		PROPERTY_TYPE = "comment_type",
		PROPERTY_PARENT = "comment_parent",
		PROPERTY_USER_ID = "user_id";
	
	/**
	 * Load the meta for the Comment object.
	 * @throws ErrorException Throws an exception if the ID property isn't 
	 * defined.
	 * @return Comment Returns the Comment object on success.
	 */
	public function loadMeta() {
		if (!is_numeric($commentId = $this->getCommentId())) 
			throw new ErrorException("Failed to load Meta for a Comment without a numeric ID", 0, E_NOTICE);
		
		$this->itsMeta = Meta::find(array(
				Meta::PROPERTY_COMMENT_ID => $commentId
			));
		
		return $this;
	}
	
	// Getters
	
	public function getCommentId() {return $this->__get(self::PROPERTY_ID);}
	public function getPostId() {return $this->__get(self::PROPERTY_POST_ID);}
	public function getAuthor() {return $this->__get(self::PROPERTY_AUTHOR);}
	public function getAuthorEmail() {return $this->__get(self::PROPERTY_AUTHOR_EMAIL);}
	public function getAuthorURL() {return $this->__get(self::PROPERTY_AUTHOR_URL);}
	public function getAuthorIP() {return $this->__get(self::PROPERTY_AUTHOR_IP);}
	public function getCommentDate() {return $this->__get(self::PROPERTY_COMMENT_DATE);}
	public function getCommentDateGMT() {return $this->__get(self::PROPERTY_COMMENT_DATE_GMT);}
	public function getContent() {return $this->__get(self::PROPERTY_CONTENT);}
	public function getKarma() {return $this->__get(self::PROPERTY_KARMA);}
	public function getApproved() {return $this->__get(self::PROPERTY_APPROVED);}
	public function getAgent() {return $this->__get(self::PROPERTY_AGENT);}
	public function getType() {return $this->__get(self::PROPERTY_TYPE);}
	public function getParent() {return $this->__get(self::PROPERTY_PARENT);}
	public function getUserId() {return $this->__get(self::PROPERTY_USER_ID);}
	
	// Meta related
	
	/**
	 * Get the Meta for the Comment object.
	 * @see MetaHandler::getMeta()
	 */
	public function getMeta() {
		if (!($this->itsMeta instanceof Collection))
			$this->loadMeta();

		return call_user_func_array(array($this->itsMeta, "find"), func_get_args());
	}
	
	// Setters
	
	// Meta related
	
	/**
	 * Set the Meta for the Comment object.
	 * @see MetaHandler::setMeta()
	 */
	public function setMeta($meta) {
		$this->itsMeta = Meta::__createCollection($meta);
		
		return $this;
	}

	// Static

	public static function __getCollectionClass() {
		return "lowtone\\wp\\posts\\comments\\collections\\Collection";
	}
	
}