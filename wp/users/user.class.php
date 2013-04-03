<?php
namespace lowtone\wp\users;
use lowtone\db\records\Record,
	lowtone\db\records\schemata\Schema,
	lowtone\db\records\schemata\properties\Property,
	lowtone\types\datetime\DateTime,
	lowtone\wp\meta\collections\Collection,
	lowtone\wp\users\meta\Meta;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\users
 */
class User extends Record {

	private $itsMeta;
	
	const PROPERTY_USER_ID = "ID",
		PROPERTY_USER_LOGIN = "user_login",
		PROPERTY_USER_PASS = "user_pass",
		PROPERTY_USER_NICENAME = "user_nicename",
		PROPERTY_USER_EMAIL = "user_email",
		PROPERTY_USER_URL = "user_url",
		PROPERTY_USER_REGISTERED = "user_registered",
		PROPERTY_USER_ACTIVATION_KEY = "user_activation_key",
		PROPERTY_USER_STATUS = "user_status",
		PROPERTY_DISPLAY_NAME = "display_name";
	
	const META_USER_FIRSTNAME = "user_firstname",
		META_USER_LASTNAME = "user_lastname",
		META_NICKNAME = "nickname",
		META_USER_DESCRIPTION = "user_description",
		META_WP_CAPABILITIES = "wp_capabilities",
		META_ADMIN_COLOR = "admin_color",
		META_CLOSEDPOSTBOXES_PAGE = "closedpostboxes_page",
		META_PRIMARY_BLOG = "primary_blog",
		META_RICH_EDITING = "rich_editing",
		META_SOURCE_DOMAIN = "source_domain";
	
	public function __construct($user = NULL) {
		parent::__construct($user);
	}
	
	public function load() {
		if (!is_numeric($userId = $this->getUserId())) 
			throw new ErrorException("Failed to load a User without a numeric ID", 0, E_NOTICE);
		
		if (($user = get_userdata($userId)) === false) 
			throw new ErrorException(sprintf("Failed loading a User with ID '%s'", $userId), 0, E_NOTICE);
		
		$this->exchangeArray($user->data);
		
		return $this;
	}

	public function loadMeta() {
		if (!is_numeric($userId = $this->getUserId())) 
			throw new ErrorException("Failed to load Meta for a User without a numeric ID", 0, E_NOTICE);

		$this->itsMeta = Meta::find(array(
				Meta::PROPERTY_USER_ID => $userId
			));

		return $this;
	}
	
	// Getters
	
	public function getMeta() {
		if (!($this->itsMeta instanceof Collection))
			$this->loadMeta();

		return call_user_func_array(array($this->itsMeta, "find"), func_get_args());
	}
	
	public function getUserId() {return $this->__get(self::PROPERTY_USER_ID);}
	public function getUserLogin() {return $this->__get(self::PROPERTY_USER_LOGIN);}
	public function getUserPass() {return $this->__get(self::PROPERTY_USER_PASS);}
	public function getUserNicename() {return $this->__get(self::PROPERTY_USER_NICENAME);}
	public function getUserEmail() {return $this->__get(self::PROPERTY_USER_EMAIL);}
	public function getUserURL() {return $this->__get(self::PROPERTY_USER_URL);}
	public function getUserRegistered() {return $this->__get(self::PROPERTY_USER_REGISTERED);}
	public function getUserActivationKey() {return $this->__get(self::PROPERTY_USER_ACTIVATION_KEY);}
	public function getUserStatus() {return $this->__get(self::PROPERTY_USER_STATUS);}
	public function getDisplayName() {return $this->__get(self::PROPERTY_DISPLAY_NAME);}

	// Static
	
	public static function __createSchema($defaults = NULL) {
		$convertToDateTime = function($val) {
			return DateTime::createFromString($val);
		};

		return parent::__createSchema(Schema::mergeSchemata(array(
				self::PROPERTY_USER_REGISTERED => array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_DATETIME,
					Property::ATTRIBUTE_SET => $convertToDateTime,
					Property::ATTRIBUTE_UNSERIALIZE => $convertToDateTime,
					Property::ATTRIBUTE_DEFAULT_VALUE => "0000-00-00 00:00:00"
				)
			), $defaults));
	}
	
}