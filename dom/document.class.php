<?php
namespace lowtone\dom;
use DOMDocument,
	DOMElement,
	DOMException,
	DOMNode,
	DOMNodeList,
	DOMText,
	XSLTProcessor,
	lowtone\types\arrays\XArray,
	lowtone\types\arrays\Map,
	lowtone\util\buildables\interfaces\Buildable,
	lowtone\util\options\Options;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\dom
 */
class Document extends DOMDocument implements interfaces\ElementHandler, Buildable {
	
	/**
	 * @var handlers\ElementHandler
	 */
	protected $itsElementHandler;

	protected $itsElementNameFilter;
	
	/**
	 * A list of options used to build the document.
	 * @var Options
	 */
	protected $itsBuildOptions;
	
	protected $itsTemplate;

	protected $itsXPath;
	
	const ELEMENT_CLASS = "lowtone\\dom\\Element";

	const OPTION_CONTENT_TYPE = "content_type";

	const CONTENT_TYPE_XML = "text/xml",
		CONTENT_TYPE_HTML = "text/html";
	
	/**
	 * Constructor for the Document.
	 * @param string $version The version in the declaration for this 
	 * document. Defaults to 1.0.
	 * @param string $encoding The character encoding in the declaration for 
	 * this document. Defaults to utf-8.
	 */
	public function __construct($version = "1.0", $encoding = "utf-8") {
		parent::__construct($version, $encoding);
		
		$this->setElementClass(self::ELEMENT_CLASS);
		
		$this->itsElementHandler = handlers\ElementHandler::create($this);
		
		$this->itsBuildOptions = new Options();
		
	}
	
	public function load($filename, $options = 0) {
		if (isset($this))
			return parent::load($filename, $options);

		$document = new static();

		$document->load($filename, $options);

		return $document;
	}

	public function loadXml($source, $options = 0) {
		return self::__cast(parent::loadXml($source, $options));
	}

	public function loadHtml($source) {
		return self::__cast(parent::loadHtml($source));
	}
	
	/**
	 * Create an element and optionally add a text value.
	 * @param string $name The name for the element.
	 * @param string|NULL $value The value for the element. The value is, unlike 
	 * with createElement, appended using createTextNode and therefore will be
	 * escaped.
	 * @return DOMElement Returns the created element on success.
	 */
	public function createElement($name, $value = NULL, $parent = false) {
		if (is_callable($this->itsElementNameFilter))
			$name = call_user_func($this->itsElementNameFilter, $name, $this);

		try {
			$element = $name instanceof DOMElement ? $name : parent::createElement((string) $name);	
		} catch (DOMException $e) {
			$e = new exceptions\CreateElementException($e->getMessage(), $e->getCode(), $e);

			$e
				->setElementName($name)
				->setELementValue($value);

			throw $e;
		}
		
		if (is_array($value) || $value instanceof XArray || $value instanceof \stdClass)
			$element->appendCreateElements((array) $value);
		else if (!is_null($value))
			$element->appendChild($this->createTextNode($value));
		
		return $element;
	}

	public function createTextNode($content) {
		if (!is_string($content) && is_callable($content))
			$content = call_user_func($content);

		return parent::createTextNode((string) $content);
	}
	
	/**
	 * Overloads the parent appendChild() function adding an extra parameter 
	 * $returnDoc which if TRUE will set the return value to the document for 
	 * chaining.
	 * @see DOMDocument::appendChild()
	 * @param DOMNode $node This function appends a child to an existing list of 
	 * children or creates a new list of children. The child can be created with 
	 * e.g. DOMDocument::createElement(), DOMDocument::createTextNode() etc. or 
	 * simply by using any other node.
	 * @param bool $returnDoc Whether to return the Document object. Defaults to
	 * FALSE.
	 * @return DOMNode|Document If $returnDoc is set to TRUE the document object
	 * is returned for chaining multiple appendChild() calls or other functions.
	 * If $returnDoc is set to FALSE the newly added child node is returned.
	 */
	/*public function appendChild(DOMNode $child, $parent = false) {
		if ($parent)
			return parent::appendChild($child);
			
		return $this->itsElementHandler->appendChild($child);
	}*/
	
	/**
	 * Create and append a new element.
	 * @param string $name The name for the element.
	 * @param string|NULL $value The value for the element.
	 * @return Element Returns the newly appended element.
	 */
	public function createAppendElement($name, $value = NULL) {
		return $this->itsElementHandler->createAppendElement($name, $value);
	}
	
	/**
	 * Create and append a new element.
	 * @param string $name The name for the element.
	 * @param string|NULL $value The value for the element.
	 * @return Document Returns the document object for chaining.
	 */
	public function appendCreateElement($name, $value = NULL) {
		return $this->itsElementHandler->appendCreateElement($name, $value);
	}
	
	/**
	 * Create an array of elements from the given array using its keys as 
	 * element names and the values as the elemnt's values.
	 * @param array $values A list of element names and values.
	 * @return array Returns an array of Element objects.
	 */
	public function createElements(array $values) {
		return $this->itsElementHandler->createElements($values);
	}
	
	public function appendChildren(array $children) {
		return $this->itsElementHandler->appendChildren($children);
	}
	
	public function appendCreateElements(array $values) {
		return $this->itsElementHandler->appendCreateElements($values);
	}
	
	public function createAppendElements(array $values) {
		return $this->itsElementsHandler->createAppendElements($values);
	}
	
	/**
	 * Create an element using a class that extends the DOMElement class.
	 * @param string $class The path for the extension class.
	 * @param string $name The name for the element.
	 * @param string|NULL $value The value for the element. The value is, unlike 
	 * with createElement, appended using createTextNode and therefore will be
	 * escaped.
	 * @return boolean|DOMElement Returns the created element on success or 
	 * FALSE if the element class couldn't be replaced.
	 */
	public function createExtendedElement($class, $name = "extElement", $value = NULL) {
		$oldClass = $this->itsElementClass;
		
		if (!$this->setElementClass($class))
			return false;
			
		$element = $this->createElement($name, $value);
		
		$this->setElementClass($oldClass);
		
		return $element;
	}
	
	/**
	 * Import a node into the document. This function changes the element class 
	 * to that of the node that is to be imported and changes it back after the 
	 * node is imported.
	 * @see DOMDocument::importNode()
	 * @todo Check if this function is required.
	 * @param DOMNode $importedNode The node to be imported.
	 * @param bool $deep If set to TRUE, this method will recursively import the 
	 * subtree under the importedNode.
	 * @return bool The copied node or FALSE if it cannot be copied.
	 */
	public function importNode(DOMNode $importedNode, $deep = false) {
		$oldClass = $this->itsElementClass;
		$newClass = get_class($importedNode);
		
		if (!$this->setElementClass($newClass))
			return false;
		
		$node = parent::importNode($importedNode, $deep);
		
		$this->setElementClass($oldClass);
		
		return $node;
	}
	
	/**
	 * Import the root element from another document as a new element. 
	 * @param DOMDocument $importedDocument The document from which to import 
	 * its root element.
	 * @return DOMElement|bool Returns the element on success or FALSE on 
	 * failure.
	 */
	public function importDocument(DOMDocument $importedDocument) {
		if (!($importedDocument->documentElement && ($documentElement = $this->importNode($importedDocument->documentElement, true))))
			return false;
			
		return $documentElement;
	}
	
	/**
	 * Apply XSL stylesheet to this document.
	 * @param DOMDocument $template The template to apply to the document.
	 * @return bool Returns TRUE on success or FALSE on failure.
	 */
	public function transform($template = NULL) {
		if (!$template) {
			// Force static load call
			$template = new DOMDocument(); 
			
			$template->load($this->itsTemplate);
		}

		return self::transformDocument($this, $template);
	}
	
	/**
	 * Remove all childnodes from the document.
	 * @return bool Returns TRUE on success.
	 */
	public function emptyDocument() {
		foreach ($this->childNodes as $child)
			$this->removeChild($child);
			
		return $this;
	}
	
	public function out(array $options = NULL) {
		$type = @$options[self::OPTION_CONTENT_TYPE] ?: self::CONTENT_TYPE_XML;

		header("Content-Type: " . $type . "; charset=" . strtolower($this->encoding));
		
		switch ($type) {
			case self::CONTENT_TYPE_HTML:
				echo $this->saveHtml();
				break;

			default:
				echo $this->saveXML();
		}

		ob_flush();
		flush();

		exit;
	}
	
	/**
	 * Walk through all nodes in the document.
	 * @param function $callback The callback applied to the nodes. The 
	 * callback takes on two parameters. The first being the current node, the
	 * second being the path for the element.
	 * @param int $maxDepth The maximum depth for elements to walk through.
	 * @param DOMNodeList|NULL $nodes The subject node list. If the function was 
	 * called on a document this defaults to the document's child nodes. 
	 * @param array|NULL $path The path to the current element.
	 * @return bool Returns TRUE on success or FALSE on failure.
	 */
	public function walk($callback, $maxDepth = -1, $nodes = NULL, $path = NULL) {
		if (is_null($nodes) && isset($this))
			$nodes = $this->childNodes;

		switch ($nodesClass = get_class($nodes)) {
			case "DOMNodeList":
			case "DOMNamedNodeMap":
				$type = $nodesClass;
				break;
		}

		if (!isset($type))
			return false;
		
		foreach ($nodes as $node) {
			$curPath = (array) $path;
			
			if ($node instanceof DOMElement)
				$curPath[] = $node->nodeName;
				
			$curDepth = count($curPath);
			
			call_user_func($callback, $node, $curPath);
			
			if ($node->hasChildNodes() && ($maxDepth < 1 || $curDepth < $maxDepth))
				self::walk($callback, $maxDepth, $node->childNodes, $curPath);
			
		}
		
		return true;
	}

	// XPath
	
	public function createXPath() {
		return ($this->itsXPath = new XPath($this));
	}

	// Building
	
	public function build(array $options = NULL) {
		return $this;
	}
	
	/**
	 * Update the build options with the given set of options
	 * @param array $options A set of new options to overwrite previous options.
	 * @return array Returns the resulting option set.
	 */
	public function updateBuildOptions(array $options) {
		return $this->itsBuildOptions->updateOptions($options);
	}
	
	/**
	 * Merge a given set of options with an options subset.
	 * @param string $target The target subset option.
	 * @param array $options The additional options.
	 * @param bool $overwrite If TRUE options that were defined earlier will be 
	 * overwritten. Defaults to FALSE.
	 * @return array Returns the resulting option set.
	 */
	public function transferBuildOptions($target, array $options, $overwrite = false) {
		$options = array_filter($options, function($option) {return !is_null($option);});
		$base = (array) $this->getBuildOption($target);
		
		$mergedOptions = $overwrite ? XArray::mergeRecursive($base, $options) : XArray::mergeRecursive($options, $base);
		
		$this->setBuildOption($target, (array) $mergedOptions);
		
		return $this->itsBuildOptions;
	}
	
	// Getters
	
	public function getElementClass() {return $this->itsElementClass;}
	
	public function getClone() {return clone $this;}
	
	public function getBuildOption($option) {return $this->itsBuildOptions->getOption($option);}
	
	public function getTemplate() {return $this->itsTemplate;}

	public function getXPath() {
		if (!($this->itsXPath instanceof XPath))
			$this->createXPath();

		return $this->itsXPath;
	}
	
	// Setters
	
	public function setElementClass($class) {
		if (!$this->registerNodeClass("DOMElement", $class))
			return false;
		
		$this->itsElementClass = $class;
		
		return $this;
	}

	public function setElementNameFilter($filter) {
		$this->itsElementNameFilter = $filter;

		return $this;
	}
	
	public function setBuildOption($option, $value) {$this->itsBuildOptions->setOption($option, $value); return $this;}

	public function setTemplate($template) {$this->itsTemplate = $template; return $this;}
	
	// Magic
	
	public function __toString() {
		return $this->saveXML();
	}

	public function __toArray() {
		$map = new Map();

		$this->walk(function($node, $path) use ($map) {
			if ($node->hasChildnodes())
				return;
			
			$map->path($path, $node->nodeValue);
		});

		return (array) $map;
	}

	public function __cast($input) {
		if ($input instanceof DOMDocument) {
			$document = new Document($input->version, $input->encoding);

			$document->loadXml($input->saveXml());

			$input = $document;
		}

		return $input;
	}
	
	// Static
	
	/**
	 * Create a new Document.
	 * @param string|NULL $documentElementName A name for the document element.
	 * @param array|NULL $children An optional list of children to add to the 
	 * document. These are appended only if a valid document element name is 
	 * supplied.
	 * @param array|NULL $options An optional list of options. Some valid 
	 * options are: 'version', 'charset', 'template'.
	 * @return Document Returns a new Document instance.
	 */
	public static function create($documentElementName = NULL, array $children = NULL, array $options = NULL) {
		$options = array_merge(array("version" => "1.0", "charset" => "utf-8"), (array) $options);

		$document = new static(@$options["version"], @$options["charset"]);

		if (is_string($documentElementName)) {
			$documentElement = $document->createAppendElement($documentElementName, (array) $children);

			if ($documentElementAttributes = @$options["document_element_attributes"])
				$documentElement->setAttributes((array) $documentElementAttributes);
		}

		if (($template = @$options["template"]) && ($templateDocument = Document::load($template))) 
			$document = $document->transform($templateDocument);

		return $document;
	}
	
	public static function loadAsHTML($source) {
		$source = String::removeWhitespace($source);
		$source = '<?xml version="1.0" encoding="utf-8"?><html><body>' . $source . '</body></html>';
		
		if (!($htmlDocument = @self::loadHTML($source)))
			return false;
			
		$inputDocument = new input\InputDocument();
		$inputDocument->formatOutput = false;
		
		if (!($body = $htmlDocument->getElementsByTagName("body")->item(0)))
			return false;

		foreach ($body->childNodes as $htmlNode) {
			if (!($htmlNode instanceof DOMElement))
				continue;
			
			if (!($inputNode = $inputDocument->importNode($htmlNode, true)))
				continue;
				
			$inputDocument->appendChild($inputNode);
		}
		
		return $inputDocument;
	}
	
	/**
	 * Apply XSL stylesheet to the supplied document. Transformation can be 
	 * disabled by setting transform disabled to TRUE.
	 * @param DOMDocument $document The document to transform.
	 * @param DOMDocument $template The template to apply to the document.
	 * @return bool Returns the transformed document on success or when 
	 * transformation is disabled or FALSE on failure.
	 */
	public static function transformDocument(DOMDocument $document, $template) {
		if (!($template instanceof DOMDocument))
			return false;
			
		$xsltp = new XSLTProcessor();
		
		if (!$xsltp->importStylesheet($template))
			return false;
		
		if(!($document = $xsltp->transformToDoc($document)))
			return false;
			
		return $document;
	}
	
	// Node text
	
	public static function getNodeText(DOMNode $node) {
		$text = "";
		
		if (!($node->childNodes instanceof DOMNodeList))
			return $text;
		
		foreach ($node->childNodes as $child) {
			if (!($child instanceof DOMText))
				continue;
				
			$text .= $child->nodeValue;
			
		}
		
		return $text;
	}
	
}