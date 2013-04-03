<?php
namespace lowtone\wp\users\out;
use lowtone\types\objects\out\ObjectDocument,
	lowtone\wp\dom\interfaces\Document as WpDocument,
	lowtone\wp\users\User;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\wp\users\out
 */
class UserDocument extends ObjectDocument implements WpDocument {
	
	public function __construct(User $user) {
		parent::__construct($user);
		
		$document = $this;
		
		$formatDate = function($date) use ($document) {
			return $date->formatLocalized($document->getBuildOption(UserDocument::DATETIME_FORMAT));
		};

		$dateFormat = get_option(self::DATE_FORMAT);
		$timeFormat = get_option(self::TIME_FORMAT);
		
		$this->updateBuildOptions(array(
			self::BUILD_ATTRIBUTES => array(
				User::PROPERTY_USER_ID
			),
			self::BUILD_ELEMENTS => array(
				User::PROPERTY_USER_LOGIN,
				User::PROPERTY_USER_NICENAME,
				User::PROPERTY_USER_EMAIL,
				User::PROPERTY_USER_URL,
				User::PROPERTY_USER_REGISTERED,
				User::PROPERTY_DISPLAY_NAME,
				"permalink"
			),
			self::DATE_FORMAT => $dateFormat,
			self::TIME_FORMAT => $timeFormat,
			self::DATETIME_FORMAT => $dateFormat . ", " . $timeFormat,
			self::PROPERTY_FILTERS => array(
				User::PROPERTY_USER_REGISTERED => $formatDate
			),
			self::STRIP_PROPERTY_PREFIX => "user_"
		));
	}

	protected function extractProperties() {
		$properties = parent::extractProperties();

		$properties["permalink"] = get_author_posts_url($this->itsObject->getUserId());

		return $properties;
	}
	
}