<?php
namespace lowtone\util\feeds\rss;
use lowtone\db\records\Record,
	lowtone\types\arrays\XArray,
	lowtone\util\feeds\rss\channels\Channel;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\util\feeds\rss
 */
class RSS extends Record {

	protected $itsNamespaces = array();

	protected $itsChannels = array();

	const PROPERTY_VERSION = "version";

	public function __construct(array $properties = NULL) {
		parent::__construct(array_merge(array(
				self::PROPERTY_VERSION => "2.0"
			), (array) $properties));
	}

	public function addNamespace($namespace) {
		if (!is_array($namespace))
			$namespace = XArray::remix(func_get_args());

		$this->itsNamespaces = array_merge($this->itsNamespaces, (array) $namespace);

		return $this;
	}
	
	public function addChannel(Channel $channel) {$this->itsChannels[] = $channel; return $this;}

	// Getters
	
	public function getNamespaces() {return $this->itsNamespaces;}
	public function getChannels() {return $this->itsChannels;}

	// Setters
	
	public function setNamespaces(array $namespaces) {$this->itsNamespaces = $namespaces; return $this;}
	public function setChannels(array $channels) {$this->itsChannels = $channels; return $this;}

	// Static
	
	public static function __getDocumentClass() {
		return "lowtone\\util\\feeds\\rss\\out\\RssDocument";
	}
}