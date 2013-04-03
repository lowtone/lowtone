<?php
namespace lowtone\wp\admin\postbox\handlers;
use lowtone\wp\admin\postbox\PostBox;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\admin\postbox\handlers
 */
class PostBoxHandler {

	protected $itsPostBoxes = array();

	public function out() {
		$postBoxesDocument = new out\PostBoxesDocument($this->itsPostBoxes);

		$postBoxesDocument->build();
		
		if ($templateDocument = out\PostBoxesDocument::load(__DIR__ . "/templates/postboxes.xsl"))
			$postBoxesDocument = $postBoxesDocument->transform($templateDocument);

		echo $postBoxesDocument->saveHTML();
	}

	public function addPostBox($postBox) {
		if (!($postBox instanceof PostBox))
			$postBox = PostBox::create($postBox);

		$this->itsPostBoxes[] = $postBox;

		return $this;
	}

}