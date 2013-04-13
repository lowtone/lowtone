<?php
namespace lowtone\io;
use ErrorException,
	lowtone\net\URL;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 0.1
 * @package wordpress\libs\lowtone\io
 */
class File {

	/**
	 * The location of the file.
	 * @var URL
	 */
	protected $itsUrl;

	protected $itsContents;

	public function __construct($path) {
		if (false === strpos("://", $path))
			$path = "file:///" . $path;

		$url = URL::fromString($path);
		
		if ("file" === $url->scheme && preg_match("#^[\.\\\\/]#", $url->path)) {
			$caller = function() {
				foreach (debug_backtrace() as $trace) {
					if (__FILE__ == $trace["file"])
						continue;

					return $trace["file"];
				}

				return NULL;
			};

			if (NULL !== ($c = $caller()))
				$url->path = dirname($c) . DIRECTORY_SEPARATOR . $url->path;

		}

		$this->itsUrl = $url;
	}

	public function get($path = NULL) {
		$file = self::__instance($path);

		if (false === ($contents = file_get_contents((string) $file->itsUrl)))
			throw new ErrorException("Couldn't read from " . (string) $file->itsUrl);

		$file->itsContents = $contents;

		return $file;
	}

	public function put($path = NULL, $contents = NULL) {
		$file = self::__instance($path, $contents);

		if (false === file_put_contents((string) $file->itsUrl, $file->itsContents))
			throw new ErrorException("Couldn't write to " . (string) $file->itsUrl);
		
		return $file;
	}

	public function contents($contents = NULL) {
		if (!isset($contents))
			return $this->itsContents;

		$this->itsContents = $contents;

		return $this;
	}

	/**
	 * Get an instance of the File class. If this function is called in static 
	 * context a new File object is created using the given parameters, if not 
	 * the current File object is returned. In the latter case the parameters 
	 * are ignored.
	 * @param string|NULL $path The path used when a new instance is created.
	 * @param string|NULL $contents The contents for a new File instance.
	 * @return File Returns an instance of the File class on success.
	 */
	public function __instance($path = NULL, $contents = NULL) {
		if (isset($this) && $this instanceof File)
			return $this;

		$file = new File($path);

		if (isset($contents))
			$file->contents($contents);

		return $file;
	}

}