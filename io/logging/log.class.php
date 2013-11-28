<?php
namespace lowtone\io\logging;
use lowtone\Util,
	lowtone\io\File,
	lowtone\io\logging\entries\Entry;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2013, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\io\logging
 */
class Log {
	
	/**
	 * Whether logging is enabled.
	 * @var bool
	 */
	private $itsDisabled;

	/**
	 * Path for the log file.
	 * @var string
	 */
	private $itsLogFile;
		
	/**
	 * Pointer to the log file.
	 * @var resource
	 */
	private $itsFp;

	/**
	 * Log entry queue.
	 * @var array
	 */
	private $itsBuffer;

	/**
	 * A callback to determine who called the log.
	 * @var closure
	 */
	private $itsDomainCallback;

	/**
	 * Log instances per file.
	 * @var array
	 */
	protected static $__instances;

	/**
	 * Constructor for the log.
	 * @param string|NULL $path An optional custom path for the log file. If no
	 * path is supplied a temporary file is created.
	 */
	public function __construct($path = NULL) {
		$caller = function() {
			foreach (debug_backtrace() as $trace) {
				if (!isset($trace["file"]) || __FILE__ == $trace["file"])
					continue;

				return $trace;
			}

			return NULL;
		};

		$this
			->file($path ?: tempnam(sys_get_temp_dir(), "log"))
			->clearBuffer()
			->domainCallback(function() use ($caller) {
				if (NULL === ($_c = $caller()))
					return NULL;

				return File::relPath($_c["file"]) . ":" . $_c["line"];
			});
	}

	/**
	 * Destructor for the log. Writes entries from the buffer to the file and 
	 * closes the file.
	 */
	public function __destruct() {
		$this->close();
	}

	/**
	 * Creates a file pointer for the log file.
	 * @param string $mode The mode for fopen. Defaults to "a".
	 * @return Log Returns the log instance for method chaining.
	 */
	private function open($mode = "a") {
		if (!is_dir($dir = dirname($this->itsLogFile)))
			mkdir($dir, 0777, true);

		if (($this->itsFp = fopen($this->itsLogFile, $mode)) === false) 
			trigger_error(sprintf("Can't open %s!", $this->itsLogFile), E_USER_NOTICE);

		return $this;
	}

	/**
	 * Empty the buffer.
	 * @return Log Returns the log instance for method chaining.
	 */
	private function clearBuffer() {
		$this->itsBuffer = array();

		return $this;
	}

	/**
	 * Write entries from the buffer to the file.
	 * @return Log Returns the log instance for method chaining.
	 */
	private function writeBuffer() {
		if ($this->itsDisabled)
			return $this;

		if (!is_resource($this->itsFp)) 
			$this->open();

		foreach ($this->itsBuffer as $message)
			fwrite($this->itsFp, $message);

		$this->clearBuffer();

		return $this;
	}

	/**
	 * Add a entry to the buffer.
	 * @param mixed $entry The log entry. If a string is provided it is used as 
	 * the message for the entry instance.
	 * @return Log Returns the log instance for method chaining.
	 */
	public function buffer($entry) {
		if (!is_array($entry) && !is_object($entry))
			$entry = array(Entry::PROPERTY_MESSAGE => $entry);

		if (!isset($entry[Entry::PROPERTY_DOMAIN]) && is_callable($this->itsDomainCallback))
			$entry[Entry::PROPERTY_DOMAIN] = call_user_func($this->itsDomainCallback);

		$entry = new Entry($entry);

		$this->itsBuffer[] = $entry;

		return $this;
	}
	
	/**
	 * Add a message to the buffer and directly write all messages from the 
	 * buffer to the file.
	 * @param mixed $message The log entry. If a string is provided it is used 
	 * as the message for the entry instance.
	 * @return Log Returns the log instance for method chaining.
	 */
	public function write($message) {
		$this
			->buffer($message)
			->writeBuffer();

		return $this;
	}

	/**
	 * Write the output from a given callback to the log file.
	 * @param callable $callback The callback.
	 * @param bool $buffer If TRUE the output is buffered, if FALSE the output
	 * is directly written.
	 * @return Log Returns the log instance for method chaining.
	 */
	public function catchOutput($callback, $buffer = false) {
		$entry = Util::catchOutput($callback);
		return $buffer ? $this->buffer($entry) : $this->write($entry);
	}

	/**
	 * Empty the file and move the pointer to the first line.
	 * @return Log Returns the log instance for method chaining.
	 */
	public function clear() {
		if (is_resource($this->itsFp))
			fclose($this->itsFp);

		$this->open("w");

		return $this;
	}
	
	/**
	 * Write entries from the buffer to the file and close the file.
	 * @return Log Returns the log instance for method chaining.
	 */
	public function close() {
		$this->writeBuffer();

		if (is_resource($this->itsFp))
			fclose($this->itsFp);

		return $this;
	}

	// Properties interface
	
	public function file($file = NULL) {
		if (is_null($file))
			return $this->itsLogFile;

		$this->itsLogFile = $file;

		return $this;
	}

	public function disabled($disabled = NULL) {
		if (is_null($disabled))
			return (bool) $this->itsDisabled;

		$this->itsDisabled = (bool) $disabled;

		return $this;
	}

	public function domainCallback($domainCallback = NULL) {
		if (is_null($domainCallback))
			return $this->itsDomainCallback;

		$this->itsDomainCallback = $domainCallback;

		return $this;
	}

	// Static
	
	/**
	 * Get a Log instance for the given filepath.
	 * @param string $file The path for the required log file.
	 * @return Log Returns a Log instance for the file at the given path.
	 */
	public static function __instance($file) {
		if (isset(self::$__instances[$file]) && ($log = self::$__instances[$file]) instanceof Log)
			return $log;

		return ($__instances[$file] = new Log($file));
	}

}