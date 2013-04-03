<?php
namespace lowtone\util\feeds\rss\channels\out;
use lowtone\util\feeds\rss\channels\Channel,
	lowtone\types\objects\out\ObjectDocument;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\util\feeds\rss\channels\out
 */
class ChannelDocument extends ObjectDocument {

	const ITEM_DOCUMENT_OPTIONS = "item_document_options";

	public function __construct(Channel $channel) {
		parent::__construct($channel);
	}

	public function build(array $options = NULL) {
		parent::build($options);

		$channel = $this->itsObject;

		$channelElement = $this->documentElement;

		foreach ($channel->getItems() as $item) {
			$itemDocument = $item
				->createDocument()
				->build($this->getBuildOption(self::ITEM_DOCUMENT_OPTIONS));

			if ($itemElement = $this->importDocument($itemDocument))
				$channelElement->appendChild($itemElement);
		}

		return $this;
	}

}