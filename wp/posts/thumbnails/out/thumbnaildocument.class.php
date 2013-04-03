<?php
namespace lowtone\wp\posts\thumbnails\out;
use lowtone\wp\attachments\out\AttachmentDocument,
	lowtone\wp\posts\thumbnails\Thumbnail;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\wp\posts\thumbnails\out
 */
class ThumbnailDocument extends AttachmentDocument {
	
	public function __construct(Thumbnail $thumbnail) {
		parent::__construct($thumbnail);

		$this->updateBuildOptions(array(
			self::OBJECT_ELEMENT_NAME => "thumbnail"
		));
	}

}