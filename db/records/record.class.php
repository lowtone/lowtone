<?php
namespace lowtone\db\records;
use ReflectionClass,
	lowtone\types\objects\Object,
	lowtone\db\DB,
	lowtone\db\records\schemata\Schema,
	lowtone\db\records\schemata\properties\Property;

/**
 * Active Record implementation for WordPress.
 * 
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\db\records
 */
abstract class Record extends Object {

	/**
	 * @var bool
	 */
	private $__readonly,
		$__locked;

	/**
	 * @var array
	 */
	protected static $__reflection;

	/**
	 * @var array
	 */
	protected static $__schema;

	/**
	 * A shared DB instance for all Record instances created from 
	 * $GLOBALS['wpdb']. Accessible through Record::__getDb().
	 * @var DB
	 */
	protected static $__db;

	const OPTION_CONDITIONS = "conditions",
		OPTION_ORDER = "order",
		OPTION_GROUP = "group",
		OPTION_LIMIT = "limit",
		OPTION_OFFSET = "offset",
		OPTION_JOINS = "joins",
		OPTION_INCLUDE = "include",
		OPTION_SELECT = "select",
		OPTION_FROM = "from",
		OPTION_READONLY = "readonly",
		OPTION_LOCK = "lock",
		OPTION_LOGICAL_OPERATOR = "logical_operator",
		OPTION_RELATIONAL_OPERATOR = "relational_operator";

	const OPTION_OVERWRITE_WITH_DEFAULTS = "overwrite_with_defaults";

	public function __construct($input = NULL, $flags = 0, $iterator_class = "ArrayIterator") {
		$input = array_merge((array) $this->__getSchema()->getDefaults(), (array) $input);
		
		parent::__construct($input, $flags, $iterator_class);
		
		$this->init();
	}

	/**
	 * Initiate the object. Allows to set defaults without redefining a 
	 * constructor.
	 * @return Record Returns the Record for method chaining.
	 */
	public function init() {
		return $this;
	}

	// Single instance methods

	/**
	 * Insert or update the object instance in the database.
	 * @throws Exception Throws an exception if the object is read-only.
	 * @return Record Returns the object.
	 */
	public function save($defaults = NULL, $options = NULL) {
		if (!(isset($this) && $this instanceof Record))
			return static::create($defaults)->save();

		if ($this->__readonly) {
			trigger_error("Cannot save read-only object", E_USER_NOTICE);

			return $this;
		}

		if ($defaults) 
			$this(@$options[self::OPTION_OVERWRITE_WITH_DEFAULTS] ? $defaults : array_diff_key((array) $defaults, (array) $object));

		global $wpdb;

		$schema = $this->__getSchema();

		$columns = array();
		$values = array();

		foreach ((array) $schema as $property => $attributes) {
			$columns[] = $property;
			$values[] = $this->applyFilters(@$this[$property], @$attributes[Property::ATTRIBUTE_SERIALIZE]);
		}

		$query[] = sprintf("REPLACE %s (%s)", self::__escapeIdentifier(static::__getTable()), implode(",", array_map(function($col) {return Record::__escapeIdentifier($col);}, $columns)));

		$query[] = sprintf("VALUES (%s)", implode(",", array_map(function($value) use ($wpdb) {
			if (is_null($value))
				return "NULL";

			return $wpdb->prepare("%s", $value);
		}, $values)));
		
		if ($wpdb->query(implode(" ", $query)) === false) 
			trigger_error(sprintf("Failed saving object of class '%s' (MySQL error: %s)", get_called_class(), mysql_error()), E_USER_WARNING);
		
		return $this;
	}

	/*public function reload() {

	}*/

	// Delete

	public function delete() {

	}

	// Util

	public function __escape($val) {
		return self::__getDb()->quote($val);
	}

	public function __escapeIdentifier($val) {
		return "`" . str_replace("`", "``", $val) . "`";
	}

	// Property access
	
	public function offsetGet($index) {
		$value = parent::offsetGet($index);

		// Apply getters
		
		$value = $this->applyFilters($value, Schema::create(static::__getSchema())->getGetters($index));

		return $value;
	}

	public function offsetSet($index, $newval) {
		if ($this->__readonly) {
			trigger_error("Cannot set value for read-only object", E_USER_NOTICE);

			return;
		}

		// Apply setters

		$newval = $this->applyFilters($newval, Schema::create(static::__getSchema())->getSetters($index));
			
		parent::offsetSet($index, $newval);
	}

	public function offsetUnset($index) {
		if ($this->__readonly) {
			trigger_error("Cannot unset value for read-only object", E_USER_NOTICE);

			return;
		}

		parent::offsetUnset($index);
	}

	public function __call($name, $arguments) {
		if (!$this->__getSchema()->hasProperty($name)) 
			throw new \ErrorException(sprintf("Call to undefined function %s", get_called_class() . "::" . $name . "()"));

		return parent::__call($name, $arguments);
	}

	/**
	 * Overwrite ArrayObject::exchangeArray() to execute setters on values.
	 * @param mixed $input The new array or object to exchange with the current 
	 * array.
	 * @return array Returns the old array.
	 */
	public function exchangeArray($input) {
		$old = parent::exchangeArray(array());

		foreach ((array) $input as $key => $value)
			$this[$key] = $value;

		return $old;
	}

	// Setters

	public function setReadonly($readonly) {
		$this->__readonly = (bool) $readonly;

		return $this;
	}

	public function setLocked($lock) {
		$this->__locked = (bool) $lock;

		return $this;
	}

	// Static
	
	public static function __createReflection() {
		return (self::$__reflection[$calledClass = get_called_class()] = new ReflectionClass($calledClass));
	}
	
	/**
	 * Automatically create a Schema for the called class.
	 * @return Schema Returns the created Schema.
	 */
	public static function __createSchema($defaults = NULL) {
		return (self::$__schema[get_called_class()] = Schema::fromReflection(static::__getReflection(), $defaults));
	}

	public static function __createDb() {
		return (self::$__db = DB::createFromWpdb());
	}

	/**
	 * Create a tablein the database for the class .
	 * @return bool Returns TRUE on success or FALSE on failure.
	 */
	public static function __storageCreate() {
		global $wpdb;

		$query[] = sprintf("CREATE TABLE IF NOT EXISTS %s", self::__escapeIdentifier(static::__getTable()));

		for ($schema = static::__getSchema(), $definitions = array(); list($property, $attributes) = each($schema);) {
			$type = strtolower($attributes[Property::ATTRIBUTE_TYPE]) ?: Property::TYPE_STRING;

			if ($primaryKey = in_array($property, $schema->getPrimaryKeys())) {

				switch ($type) {
					case Property::TYPE_INT:
						$attributes = array_merge(array(
								Property::ATTRIBUTE_COLUMN_DEFINITION => "NOT NULL AUTO_INCREMENT"
							), (array) $attributes);
						break;

				}

			}

			$definition = self::__escapeIdentifier($property);

			// Type

			switch ($type) {
				case Property::TYPE_STRING:
					$definition .= " TEXT";
					break;

				default: 
					$definition .= " " . strtoupper($type);
			}

			// Length

			switch ($type) {
				case Property::TYPE_STRING:
				case Property::TYPE_INT:
					$definition .= "(" . (int) @$attributes[Property::ATTRIBUTE_LENGTH] . ")";
					break;
			}

			if (!@$attributes[Property::COLUMN_DEFINITION])
				$definition .= " NOT NULL";

			$definitions[] = $definition;
		}

		// Primary keys

		if ($primaryKeys = $schema->getPrimaryKeys()) {

			$definitions[] = "PRIMARY KEY (" .
				implode(",", array_map(function($key) {
					return Record::__escapeIdentifier($key);
				}, $primaryKeys)) . ")";

		}

		$query[] = "(" . implode(",", $definitions) . ")";

		// Table options

		$query[] = "ENGINE=MyISAM";

		$query[] = "DEFAULT CHARSET=utf8";
		
		return $wpdb->query(implode(" ", $query));
	}

	// Definitions

	public static function __getReflection() {
		return (($reflection = @self::$__reflection[get_called_class()]) instanceof ReflectionClass) ? $reflection : static::__createReflection();
	}
	
	public static function __getVersion() {
		return static::__getReflection()->hasConstant("VERSION") ? static::VERSION : 0;
	}

	public static function __getSchema() {
		return (($schema = @self::$__schema[get_called_class()]) instanceof Schema) ? $schema : static::__createSchema();
	}

	public static function __getDb() {
		return (self::$__db instanceof DB) ? self::$__db : static::__createDb();
	}

	public static function __getTablePrefix() {
		return $GLOBALS["wpdb"]->prefix;
	}

	public static function __getTableBase() {
		return static::__getReflection()->hasConstant("TABLE") ? static::TABLE : strtolower(end(explode("\\", get_called_class()))) . "s";
	}

	public static function __getTable() {
		return implode(array_filter(array(
			static::__getTablePrefix(),
			static::__getTableBase()
		)));
	}

	/** 
	 * Shorthand function to aquire the primary key.
	 * @return string Returns a property identifier for the primary key.
	 */
	private static function __getPrimaryKeys() {
		return Schema::create(static::__getSchema())->getPrimaryKeys();
	}
	
	// Create

	/**
	 * Create a new instance of the class.
	 * @param array|NULL $properties The properties for the new object.
	 * @return Record Returns a new object.
	 */
	public static function create($properties = NULL, array $options = NULL) {
		$object = parent::create($properties, $options);

		$object
			->setReadonly(@$options[self::OPTION_READONLY])
			->setLocked(@$options[self::OPTION_LOCK]);

		return $object;
	}
	
	// Find

	/**
	 * Create new instances from the given results.
	 * @param array $results The list of results to create the objects from.
	 * @return array Returns an array of Record instances.
	 */
	private static function __createFromResults($results, array $options = NULL) {
		$collectionClass = static::__getCollectionClass();
		
		$objects = array();

		if (!$results || !is_array($results))
			return $collectionClass::create($objects);

		$unserializers = Schema::create(static::__getSchema())->getUnserializers();
		
		foreach ($results as $result) {
			$result = (array) $result;

			$result = array_combine(($keys = array_keys($result)), array_map(function($value, $key) use ($unserializers) {
				return Record::applyFilters($value, @$unserializers[$key]);
			}, $result, $keys));
			
			$objects[] = static::create($result, $options);
		}

		return $collectionClass::create($objects);
	}

	/**
	 * Create new instances from a database query.
	 * @param array $query The SQL query.
	 * @return array Returns an array of Record instances.
	 */
	private static function __createFromQuery($query, array $options = NULL) {
		global $wpdb;

		return static::__createFromResults($wpdb->get_results($wpdb->prepare($query)), $options);
	}

	/**
	 * Find all instances of the class.
	 * @param array $options|NULL An optional list of requirements and options.
	 * @return array Returns an array of Record instances.
	 */
	public static function all(array $options = NULL) {
		global $wpdb;

		$defaults = array(
				self::OPTION_SELECT => "*",
				self::OPTION_FROM => static::__getTable(),
				self::OPTION_LOGICAL_OPERATOR => "AND",
				self::OPTION_RELATIONAL_OPERATOR => "="
			);

		$options = array_merge($defaults, (array) $options);

		$query[] = sprintf("SELECT %s FROM %s", $options[self::OPTION_SELECT], self::__escapeIdentifier($options[self::OPTION_FROM]));

		if ($joins = @$options[self::OPTION_JOINS]) {

			foreach ((array) $joins as $join) 
				$query[] = "LEFT JOIN " . $join;

		}

		if ($conditions = @$options[self::OPTION_CONDITIONS]) {

			if (is_array($conditions)) {

				$conditions = implode(" " . @$options[self::OPTION_LOGICAL_OPERATOR] . " ", array_map(function($value, $key) use ($wpdb, $options) {
					return $wpdb->prepare(Record::__escapeIdentifier($key) . @$options[Record::OPTION_RELATIONAL_OPERATOR]  . "%s", $value);
				}, $conditions, array_keys($conditions)));

			}

			$query[] = "WHERE " . (string) $conditions;

		}

		if ($order = @$options[self::OPTION_ORDER])
			$query[] = "ORDER BY " . $order;

		if (!is_null($offset = @$options[self::OPTION_OFFSET]) || !is_null($limit = @$options[self::OPTION_LIMIT]))
			$query[] = "LIMIT " . (int) $offset . "," . (int) $limit;
		
		return static::__createFromQuery(implode(" ", $query), $options);
	}

	/**
	 * Find records matching the given conditions.
	 * @param array|NULL $conditions An optional list of properties and the values
	 * that should be matched.
	 * @param array|NULL $options An optional list of requirements and options.
	 * @return array Returns an array of Record instances.
	 */
	public static function find(array $conditions = NULL, array $options = NULL) {
		return static::all(array_merge((array) $options, array(self::OPTION_CONDITIONS => (array) $conditions)));
	}

	/**
	 * Find a single instance with the given requirements.
	 * @param array|NULL $conditions An optional list of properties and the values
	 * that should be matched.
	 * @param array $options|NULL An optional list of requirements and options.
	 * @return Record|bool Returns an Record object on success or 
	 * FALSE if no entry matching the given requirements was found.
	 */
	public static function findOne(array $conditions = NULL, array $options = NULL) {
		$options = array_merge((array) $options, array(
				self::OPTION_LIMIT => 1
			));

		return reset(static::find($conditions, $options)->getObjects());
	}

	/**
	 * Get an instance for the first record matching the given requirements.
	 * @param array|NULL $conditions An optional list of properties and the values
	 * that should be matched.
	 * @param array $options|NULL An optional list of requirements and options.
	 * @return Record|bool Returns an Record object on success or 
	 * FALSE if no entry matching the given requirements was found.
	 */
	public static function first(array $conditions = NULL, array $options = NULL) {
		$options = array_merge((array) $options, array(
				self::OPTION_ORDER => implode(",", array_map(function($key) {return sprintf("`%s` ASC", $key);}, static::__getPrimaryKeys()))
			));

		return static::findOne($conditions, $options);
	}

	/**
	 * Get an instance for the last record matching the given requirements.
	 * @param array|NULL $conditions An optional list of properties and the values
	 * that should be matched.
	 * @param array $options|NULL An optional list of requirements and options.
	 * @return Record|bool Returns an Record object on success or 
	 * FALSE if no entry matching the given requirements was found.
	 */
	public static function last(array $conditions = NULL, array $options = NULL) {
		$options = array_merge((array) $options, array(
				self::OPTION_ORDER => implode(",", array_map(function($key) {return sprintf("`%s` DESC", $key);}, static::__getPrimaryKeys()))
			));

		return static::findOne($conditions, $options);
	}

	/**
	 * Find all instances matching the given ID values.
	 * @todo Check ID parameters for multiple primary keys.
	 * @param int|array $id A single numeric ID or array of ID's.
	 * @param int|array $id,... More numeric ID's or arrays of ID's.
	 * @return array|Record Returns an array of Record instances or 
	 * a single Record instance if a single numeric value was supplied 
	 * for the $id parameter.
	 */
	public static function findById($id) {
		global $wpdb;

		$ids = array_unique(call_user_func_array('array_merge', array_map(function($param) {return (array) $param;}, (array) func_get_args())));

		if (!$ids)
			return static::__createCollection(); // Returns empty Collection

		for ($primaryKeys = static::__getPrimaryKeys(), $conditions = array(); list($key, $column) = each($primaryKeys);) {
			$escapedColumnIds = array_filter(
					array_map(function($id) use ($key, $wpdb) {
						$id = (array) $id;

						if (!isset($id[$key]))
							return NULL;
						
						return $wpdb->prepare("%s", $id[$key]);
					}, $ids), 
					function($val) {
						return !is_null($val);
					}
				);

			$conditions[] = sprintf("%s IN (" . implode(",", $escapedColumnIds) . ")", self::__escapeIdentifier($column));
		}

		$result = static::all(array(
				self::OPTION_CONDITIONS => implode(" AND ", $conditions)
			));

		if (!is_array($id) && func_num_args() < 2)
			$result = reset($result);

		return $result;
	}

	public static function __getCollectionClass() {
		return "lowtone\\db\\records\\collections\\Collection";
	}

}