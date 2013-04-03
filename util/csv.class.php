<?php
namespace lowtone\util;
use ArrayObject,
	ErrorException,
	lowtone\dom\Document;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\util
 */
class CSV extends ArrayObject {

	private $itsDelimiter = ",",
		$itsEnclosure = '"';
	
	public function load($filename) {
		$csv = isset($this) && $this instanceof CSV ? $this : new CSV();
		
		if (false === ($handle = fopen($filename, "r")))
			throw new ErrorException(sprintf("Could not load CSV file %s", $filename));

		while (false !== ($line = fgetcsv($handle)))
			$csv[] = $line;

		fclose($handle);

		return $csv;
	}

	public function loadText($text) {
		$csv = self::__instance();

		$lines = preg_split("/\n|\r\n|\r|\f/m", $text);

		foreach ($lines as $line)
			$csv[] = str_getcsv($line);

		return $csv;
	}

	public function append($line) {
		$csv = self::__instance();

		foreach (func_get_args() as $line)
			$csv[] = is_string($line) ? str_getcsv($line) : (array) $line;

		return $csv;
	}

	public function delimiter($delimiter = NULL) {
		if (!isset($delimiter))
			return $this->itsDelimiter;

		$this->itsDelimiter = $delimiter;

		return $this;
	}

	public function enclosure($enclosure = NULL) {
		if (!isset($enclosure))
			return $this->itsEnclosure;

		$this->itsEnclosure = $enclosure;

		return $this;
	}

	public function __toDocument(array $options = NULL) {
		$document = new Document();

		$csvElement = $document->createAppendElement("csv");

		$lines = (array) $this;

		$keys = $options["first_line_keys"] ? array_shift($lines) : array();
		
		foreach ($lines as $line) {
			$lineElement = $csvElement->createAppendElement("line");

			foreach ($line as $index => $col) 
				$lineElement->appendCreateElement($keys[$index] ?: "col", $col);
			
		}

		return $document;
	}

	public function __instance() {
		return isset($this) && $this instanceof CSV ? $this : new CSV();
	}

	public function __toString() {
		$temp = fopen("php://temp", "w");

		foreach ($this as $line)
			fputcsv($temp, $line, $this->delimiter(), $this->enclosure());

		rewind($temp);

		$string = stream_get_contents($temp);

		fclose($temp);

		return $string;
	}

}