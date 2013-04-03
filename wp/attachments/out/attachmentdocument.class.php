<?php
namespace lowtone\wp\attachments\out;
use lowtone\dom\Element,
	lowtone\wp\attachments\Attachment,
	lowtone\wp\posts\out\PostDocument;

class AttachmentDocument extends PostDocument {

	const BUILD_ATTACHMENT_METADATA = "build_attachment_metadata";
	
	public function __construct(Attachment $attachment) {
		parent::__construct($attachment);

		$this->setElementNameFilter(function($name) {
			if (!Element::validateName($name))
				$name = Element::normalizeName($name);

			return $name;
		});

		$this->updateBuildOptions(array(
			self::BUILD_ATTACHMENT_METADATA => true,
			self::OBJECT_ELEMENT_NAME => "attachment"
		));
	}

	public function build(array $options = NULL) {
		parent::build($options);

		$attachmentElement = $this->documentElement;

		if ($this->getBuildOption(self::BUILD_ATTACHMENT_METADATA)) {

			$attachmentElement->appendCreateElement("attachment_metadata", $this->extractAttachmentMetaData());

		}

		return $this;
	}

	public function extractAttachmentMetaData() {
		$attachmentMetaData = $this->itsObject->getAttachmentMetadata();

		$uploadsDir = wp_upload_dir();

		$attachmentMetaData["url"] = ($url = $uploadsDir["baseurl"] . "/" . @$attachmentMetaData["file"]);

		if (isset($attachmentMetaData["sizes"])) {
			$dirUrl = pathinfo($url, PATHINFO_DIRNAME);

			foreach ($attachmentMetaData["sizes"] as &$size) {
				if (!($file = $size["file"]))
					continue;

				$size["url"] = $dirUrl . "/" . $size["file"];
			}

		}

		return $attachmentMetaData;
	}

}