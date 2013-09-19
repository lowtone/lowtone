<?php
namespace lowtone\io;
use lowtone\net\URL;

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
		$this->url($path);
	}

	public function get($path = NULL) {
		$file = self::__instance($path);

		if (false === ($contents = @file_get_contents((string) $file->itsUrl)))
			throw new exceptions\ReadException("Couldn't read from " . (string) $file->itsUrl);

		$file->itsContents = $contents;

		return $file;
	}

	public function put($path = NULL, $contents = NULL) {
		$file = self::__instance($path, $contents);

		if ("file" !== $file->itsUrl->scheme)
			throw new exceptions\WriteException("Files can only be written to the local file system");

		if (false === file_put_contents((string) $file->itsUrl, $file->itsContents))
			throw new exceptions\WriteException("Couldn't write to " . (string) $file->itsUrl);
		
		return $file;
	}

	public function url($url = NULL) {
		if (!isset($url))
			return $this->itsUrl;

		$this->itsUrl = $this->__createUrl($url);

		return $this;
	}

	public function contents($contents = NULL) {
		if (!isset($contents))
			return $this->itsContents;

		$this->itsContents = $contents;

		return $this;
	}
	
	/**
	 * Get a path relative to the given base.
	 *
	 * @todo Works only when a file can be located using realpath().
	 * 
	 * @param string $path The subject path. Defaults to the file path if called
	 * on a File instance.
	 * @param string $base The base path.
	 * @return string Returns a path relative to the given base.
	 */
	public function relPath($path = NULL, $base = NULL) {
		if (!isset($path)) {

			if (isset($this) && $this instanceof File)
				$path = $this->itsUrl;
			else
				throw new \ErrorException(sprintf("%s requires a path", __FUNCTION__));

		}

		if ($path instanceof URL && "file" == $path->scheme)
			$path = (string) $path->path;

		if (!isset($base))
			$base = ABSPATH;

		$base = explode(DIRECTORY_SEPARATOR, rtrim(realpath((string) $base), DIRECTORY_SEPARATOR));
		$path = explode(DIRECTORY_SEPARATOR, rtrim(realpath((string) $path), DIRECTORY_SEPARATOR));
		
		while (count($base) && count($path) && ($base[0] == $path[0])) {
			array_shift($base);
			array_shift($path);
		}
		
		return str_pad("", count($base) * 3, '..'. DIRECTORY_SEPARATOR) . implode(DIRECTORY_SEPARATOR, $path);
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
		if (isset($this) && $this instanceof File) {
			if (func_num_args() < 1)
				return $this;

			$file = clone $this;
		} else
			$file = new File($path);

		if (isset($path))
			$file->url($path);

		if (isset($contents))
			$file->contents($contents);

		return $file;
	}

	public function __createUrl($url) {
		if (!($url instanceof URL)) {
			$url = (string) $url;

			if (false === strpos($url, "://"))
				$url = "file:///" . $url;

			$url = URL::fromString($url);
		}
		
		if ("file" === $url->scheme && "." == $url->path[0]) {
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

		return $url;
	}

}
