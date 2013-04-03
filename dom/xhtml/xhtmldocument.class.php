<?php
namespace lowtone\dom\xhtml;
use DOMDocument,
	DOMNode,
	DOMText,
	lowtone\dom\Document;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\dom\xhtml
 */
class XHTMLDocument extends Document {
	
	protected $selfTerminate = array(
		"area",
		"base",
		"basefont",
		"br",
		"col",
		"frame",
		"hr",
		"img",
		"input",
		"link",
		"meta",
		"param"
	);

	const TYPE_XHTML = "xhtml";
	
	public function saveXHTML(DOMNode $node = NULL) {
		if (!$node) 
			$node = $this->firstChild;

		$doc = new DOMDocument("1.0");
		$clone = $doc->importNode($node->cloneNode(false), true);
		$inner  = '';

		if (!($term = in_array(strtolower($clone->nodeName), $this->selfTerminate))) {
			$clone->appendChild(new DOMText(''));
			
			if ($node->childNodes) foreach ($node->childNodes as $child) 
				$inner .= $this->saveXHTML($child);
			
		}

		$doc->appendChild($clone);
		
		$out = $doc->saveXML($clone);
		
		return $term ? substr($out, 0, -2) . ' />' : str_replace('><', ">{$inner}<", $out);

	}

	public function out($type = self::TYPE_XHTML) {
		if ($type != self::TYPE_XHTML)
			return parent::out($type);

		header("Content-Type: text/" . $type);

		echo $this->saveXHTML();

		ob_flush();
		flush();

		exit;
	}
	
}
?>