<?php
namespace lowtone\wp\attachments;
use lowtone\wp\posts\Post;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\pages
 */
class Attachment extends Post {
	
	const META_ATTACHED_FILE = "_wp_attached_file",
		META_ATTACHMENT_METADATA = "_wp_attachment_metadata";

	/**
	 * Get the path to the attached file (this is a relative path to the uploads
	 * base dir).
	 * @return string Returns the path to the attached file.
	 */
	public function getAttachedFile() {
		return get_post_meta($this->getPostId(), self::META_ATTACHED_FILE, true);
	}

	public function getAttachmentMetadata() {
		return get_post_meta($this->getPostId(), self::META_ATTACHMENT_METADATA, true);
	}

	/**
	 * Attempt to get the size of the attached file.
	 * @return int Returns the size of the attached file in bytes on success or 
	 * FALSE on failure.
	 */
	public function getFileSize() {
		$uploadDir = wp_upload_dir();
		
		if (!($file = realpath($uploadDir["basedir"] . "/" . $this->getAttachedFile())))
			return false;

		try {
			$size = filesize($file);
		} catch (\Exception $e) {
			return false;
		}

		return $size;
	}

	// Static
	
	public static function __getTable() {
		return Post::__getTable();
	}

	public static function __getDocumentClass() {
		return "lowtone\\wp\\attachments\\out\\AttachmentDocument";
	}
	
}