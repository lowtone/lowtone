<?php
namespace lowtone\wp\posts\thumbnails;
use lowtone\wp\attachments\Attachment;

class Thumbnail extends Attachment {

	public static function __getDocumentClass() {
		return "lowtone\\wp\\posts\\thumbnails\\out\\ThumbnailDocument";
	}
	
}