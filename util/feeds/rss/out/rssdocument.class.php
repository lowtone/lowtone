<?php
namespace lowtone\util\feeds\rss\out;
use lowtone\util\feeds\rss\Rss,
	lowtone\types\objects\out\ObjectDocument;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\util\feeds\rss\out
 */
class RssDocument extends ObjectDocument {

	const CHANNEL_DOCUMENT_OPTIONS = "channel_document_options";

	public function __construct(Rss $rss) {
		parent::__construct($rss);

		$this->updateBuildOptions(array(
				self::BUILD_ATTRIBUTES => array(
						Rss::PROPERTY_VERSION
					),
				self::BUILD_ELEMENTS => false
			));
	}

	public function build(array $options = NULL) {
		parent::build($options);

		$rss = $this->itsObject;

		$rssElement = $this->documentElement;

		foreach ($rss->getNamespaces() as $namespace => $uri) 
			$rssElement->setAttribute(sprintf("xmlns:%s", $namespace), $uri);

		// Build channel

		foreach ($rss->getChannels() as $channel) {
			$channelDocument = $channel
				->createDocument()
				->build($this->getBuildOption(self::CHANNEL_DOCUMENT_OPTIONS));

			if ($channelElement = $this->importDocument($channelDocument))
				$rssElement->appendChild($channelElement);

		}

		return $this;
	}

	public function out(array $options = NULL) {
		$options = array_merge(array(
				self::OPTION_CONTENT_TYPE => "application/rss+xml"
			), (array) $options);
		
		return parent::out($options);
	}

}