<?php
namespace lowtone\types\objects\collections;
use DateTime,
	lowtone\Util,
	lowtone\types\arrays\XArray,
	lowtone\util\documentable\interfaces\Documentable;

/**
 * A list of Object instances.
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\types\objects\collections
 */
class Collection extends XArray implements Documentable {

	/**
	 * @var Collection
	 */
	protected $itsPrevious;

	protected $itsObjectClass;

	const OPTION_CONDITIONS = "conditions",
		OPTION_NOT = "not",
		OPTION_LIMIT = "limit",
		OPTION_OFFSET = "offset";

	const SORT_PROPERTY = "property",
		SORT_DIRECTION = "direction",
		SORT_TYPE = "type";

	public function offsetSet($index, $newval) {
		$class = $this->getObjectClass();

		$newval = $class::__cast($newval);
			
		parent::offsetSet($index, $newval);
	}

	/**
	 * Tries to convert input entries to object class.
	 * @param array $input The new collection content.
	 * @return array Returns the old content from the collection.
	 */
	public function exchangeArray($input) {
		$class = $this->getObjectClass();

		$input = array_map(function($entry) use ($class) {
			try {
				$entry = $class::__cast($entry);
			} catch (\Exception $e) {
				if (Util::isDebug()) {
					var_dump($entry, $class, $e->getMessage());
					exit;
				}

				return false;
			}

			return $entry;
		}, (array) $input);
	
		return parent::exchangeArray($input);
	}

	public function each($callback, array $args = NULL) {
		$args = array_merge(array(&$object), (array) $args);

		foreach ($this->getObjects() as $object)
			call_user_func_array($callback, $args);

		return $this;
	}

	/**
	 * Remove all objects from the collection. This method would have been 
	 * called empty() if that weren't already a language construct.
	 * @return Collection Returns a new empty Collection. The empty Collection 
	 * has a reference to the Collection before it was emptied allowing to 
	 * revert this action.
	 */
	public function drain() {
		return static::create(array(), $this);
	}

	public function filter($callback, array $args = NULL) {
		$args = array_merge(array(&$object), (array) $args);
		
		$objects = array_filter($this->getObjects(), function($object) use ($callback, $args) {
			return call_user_func_array($callback, $args);
		});

		return static::create($objects, $this);
	}

	/**
	 * Find all instances of the class.
	 * @param array $options|NULL An optional list of requirements and options.
	 * @return array Returns an array of Record instances.
	 */
	public function all(array $options = NULL) {
		$objects = $this->getObjects();

		if ($options[self::OPTION_CONDITIONS]) {
			$conditions = $options[self::OPTION_CONDITIONS];

			$objects = array_filter($objects, function($object) use ($conditions) {
				return $object->match($conditions);
			});

		}

		if (isset($options[self::OPTION_NOT])) {
			$not = $options[self::OPTION_NOT];

			$objects = array_filter($objects, function($object) use ($not) {
				return !$object->match($not);
			});

		}

		if (($hasOffset = isset($options[self::OPTION_OFFSET])) || ($hasLimit = isset($options[self::OPTION_LIMIT]))) {
			$offset = $hasOffset ? $options[self::OPTION_OFFSET] : 0;
			$limit = $hasLimit ? $options[self::OPTION_LIMIT] : NULL;

			$objects = array_slice($objects, $offset, $limit);
		}

		return static::create($objects, $this);
	}

	/**
	 * Find records matching the given conditions.
	 * @param array|NULL $conditions An optional list of properties and the values
	 * that should be matched.
	 * @param array|NULL $options An optional list of requirements and options.
	 * @return array Returns an array of Record instances.
	 */
	public function find($conditions = NULL, array $options = NULL) {
		return $this->all(array_merge((array) $options, array(self::OPTION_CONDITIONS => (array) $conditions)));
	}

	/**
	 * Find a single instance with the given requirements.
	 * @param array $options|NULL An optional list of requirements and options.
	 * @return Record|bool Returns an Record object on success or 
	 * FALSE if no entry matching the given requirements was found.
	 */
	public function findOne($conditions = NULL, array $options = NULL) {
		return reset($this->find($conditions, $options));
	}

	/**
	 * Get an instance for the first record matching the given requirements.
	 * @param array $options|NULL An optional list of requirements and options.
	 * @return Record|bool Returns an Record object on success or 
	 * FALSE if no entry matching the given requirements was found.
	 */
	public function first($conditions = NULL, array $options = NULL) {
		return $this->findOne($conditions, $options);
	}

	/**
	 * Get an instance for the last record matching the given requirements.
	 * @param array $options|NULL An optional list of requirements and options.
	 * @return Record|bool Returns an Record object on success or 
	 * FALSE if no entry matching the given requirements was found.
	 */
	public function last($conditions = NULL, array $options = NULL) {
		return end($this->find($conditions, $options));
	}

	public function not(array $conditions, array $options = NULL) {
		return $this->all(array_merge((array) $options, array(self::OPTION_NOT => $conditions)));
	}

	public function slice($start, $length = NULL) {
		return $this->all(array(self::OPTION_OFFSET => $start, self::OPTION_LIMIT => $length));
	}

	/**
	 * Get the Collection in the previous state.
	 * @return Collection Returns the previous Collection.
	 */
	public function end() {
		return $this->getPrevious() ?: Collection::create();
	}

	public function sort($options) {
		$objects = $this->getObjects();

		if (!is_callable($options)) {

			// Convert to options

			$options = array_map(function($option) {
				return array_merge(array(
						self::SORT_DIRECTION => "asc",
						self::SORT_TYPE => "numeric"
					), is_array($option) ? $option : array(self::SORT_PROPERTY => $option));
			}, (array) $options);

			// Convert to callback

			$options = function($a, $b) use ($options) {
				foreach ($options as $option) {
					if (NULL === ($property = $option[self::SORT_PROPERTY]))
						continue;

					$pA = $a[$property];
					$pB = $b[$property];

					switch ($option[self::SORT_TYPE]) {
						case "natural":
							list($pA, $pB) = 0 == ($strDiff = strcasecmp($pA, $pB)) ? array(0, 0) : ($strDiff < 0 ? array(0, 1) : array(1, 0));
							break;

						case "datetime":
						case "timestamp":
							$pA = is_numeric($pA) ? $pA : ($pA instanceof DateTime ? $pA->getTimestamp() : strtotime($pA));
							$pB = is_numeric($pB) ? $pB : ($pB instanceof DateTime ? $pB->getTimestamp() : strtotime($pB));
							break;
					}

					if ($pA == $pB)
						continue;
					
					$diff = $pA < $pB ? -1 : 1;

					if ("desc" == strtolower($option[self::SORT_DIRECTION]))
						$diff *= -1;

					return $diff;
				}

				return 0;
			};

		}

		usort($objects, $options);

		return self::create($objects, $this);
	}

	public function __get($name) {
		switch ($name) {
			case "length":
				return count($this->getObjects());
		}
	}

	public function __toString() {
		return (string) $this
			->__toDocument()
			->build();
	}

	// Output

	public function createDocument() {
		return $this->__toDocument();
	}

	// Exports
	
	public function __toDocument() {
		$class = static::__getDocumentClass();

		return new $class($this);
	}

	// Getters

	public function getPrevious() {
		return $this->itsPrevious;
	}
	
	public function getObjects() {
		$class = $this->getObjectClass();

		return array_filter((array) $this, function($object) use ($class) {
			return $object instanceof $class;
		});
	}

	public function getObjectClass() {
		return isset($this->itsObjectClass) ? $this->itsObjectClass : static::__getObjectClass();
	}

	// Setters
	
	public function setPrevious(Collection $previous) {
		$this->itsPrevious = $previous;

		return $this;
	}

	public function setObjectClass($objectClass) {
		$this->itsObjectClass = $objectClass;

		return $this;
	}

	// Static
	
	public static function __getObjectClass() {
		return "lowtone\\types\\objects\\Object";
	}
	
	public static function __getDocumentClass() {
		return __NAMESPACE__ . "\\out\\CollectionDocument";
	}

	public static function create($objects = NULL, Collection $previous = NULL) {
		$collection = new static($objects);

		if ($previous instanceof Collection)
			$collection->setPrevious($previous);

		return $collection;
	}
	
}