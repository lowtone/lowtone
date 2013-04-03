<?php
namespace lowtone\io\logging;
use lowtone\Util,
	lowtone\io\logging\entries\Entry;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\io\logging
 */
class Log {
	
	private $itsDisabled,
		$itsLogFile, 
		$itsFp,
		$itsBuffer;

	public function __construct($path = NULL) {
		$this
			->file($path ?: tempnam(sys_get_temp_dir(), "log"))
			->clearBuffer();
	}

	public function __destruct() {
		$this->close();
	}

	private function open($mode = "a") {
		if (!is_dir($dir = dirname($this->itsLogFile)))
			mkdir($dir, 0777, true);

		if (($this->itsFp = fopen($this->itsLogFile, $mode)) === false) 
			trigger_error(sprintf("Can't open %s!", $this->itsLogFile), E_USER_NOTICE);

		return $this;
	}

	private function clearBuffer() {
		$this->itsBuffer = array();
	}

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

	public function buffer($entry) {
		if (!is_array($entry))
			$entry = array(Entry::PROPERTY_MESSAGE => $entry);

		$entry = new Entry(array_merge(array(
				Entry::PROPERTY_DOMAIN => pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME)
			), $entry));

		$this->itsBuffer[] = $entry;

		return $this;
	}
	
	public function write($message) {
		$this
			->buffer($message)
			->writeBuffer();

		return $this;
	}

	public function catchOutput($callback, $buffer = false) {
		$entry = Util::catchOutput($callback);
		return $buffer ? $this->buffer($entry) : $this->write($entry);
	}

	public function clear() {
		if (is_resource($this->itsFp))
			fclose($this->itsFp);

		$this->open("w");

		return $this;
	}
	
	public function close() {
		$this->writeBuffer();

		if (is_resource($this->itsFp))
			fclose($this->itsFp);

		return $this;
	}
	
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

}