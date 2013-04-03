<?php
namespace lowtone\ui\pagination\out;
use lowtone\dom\Document,
	lowtone\ui\pagination\Pagination;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\ui\pagination\out
 */
class PaginationDocument extends Document {
	
	/**
	 * @var Pagination
	 */
	protected $itsPagination;
	
	const RANGE_FORMAT = "range_format",
		BUILD_FIRST = "build_first",
		BUILD_LAST = "build_last",
		BUILD_NEXT = "build_next",
		BUILD_PREVIOUS = "build_previous",
		BUILD_PAGES = "build_pages",
		MAX_VISIBLE_PAGES = "max_visible_pages";
	
	public function __construct(Pagination $pagination) {
		parent::__construct();
		
		$this->itsPagination = $pagination;
		
		$this->updateBuildOptions(array(
			self::BUILD_LOCALES => true,
			self::RANGE_FORMAT => "%d - %d",
			self::BUILD_FIRST => true,
			self::BUILD_LAST => true,
			self::BUILD_NEXT =>true,
			self::BUILD_PREVIOUS => true,
			self::BUILD_PAGES => true,
			self::MAX_VISIBLE_PAGES => 5
		));
	}
	
	public function build(array $options = NULL) {
		$this->updateBuildOptions((array) $options);
		
		$this->itsPagination->setVisiblePages($this->getBuildOption(self::MAX_VISIBLE_PAGES));
		
		$paginationElement = $this
			->createAppendElement("pagination")
				->setAttributes(array(
					"current" => $this->itsPagination->getCurrent(),
					"last" => $this->itsPagination->getTotal()
				));
		
		// First page
		
		if ($this->getBuildOption(self::BUILD_FIRST) && $this->itsPagination->hasFirst()) {
			
			$firstPageElement = $paginationElement->createAppendElement("first");
					
			$firstPageElement->appendChild($this->createPageElement($this->itsPagination->getFirst()));
			
			if ($this->getBuildOption(self::BUILD_LOCALES)) {
				
				$firstPageElement->createAppendElement("locales", array(
					"title" => __("First")
				));
				
			}
			
		}
		
		// Previous page
		
		if ($this->getBuildOption(self::BUILD_PREVIOUS) && $this->itsPagination->hasPrevious()) {
			
			$previousPageElement = $paginationElement->createAppendElement("previous");
					
			$previousPageElement->appendChild($this->createPageElement($this->itsPagination->getPrevious()));
			
			if ($this->getBuildOption(self::BUILD_LOCALES)) {
				
				$previousPageElement->createAppendElement("locales", array(
					"title" => __("Previous")
				));
				
			}
			
		}
		
		// Pages
		
		if ($this->getBuildOption(self::BUILD_PAGES)) {
			
			$pagesElement = $paginationElement->createAppendElement("pages");
			
			foreach ($this->itsPagination->getVisiblePages() as $page) 
				$pagesElement->appendChild($this->createPageElement($page));
			
		}
		
		// Next page
		
		if ($this->getBuildOption(self::BUILD_NEXT) && $this->itsPagination->hasNext()) {
			
			$nextPageElement = $paginationElement->createAppendElement("next");
			
			$nextPageElement->appendChild($this->createPageElement($this->itsPagination->getNext()));
			
			if ($this->getBuildOption(self::BUILD_LOCALES)) {
				
				$nextPageElement->createAppendElement("locales", array(
					"title" => __("Next")
				));
				
			}
			
		}
		
		// Last page
		
		if ($this->getBuildOption(self::BUILD_LAST) && $this->itsPagination->hasLast()) {
			
			$lastPageElement = $paginationElement->createAppendElement("last");
			
			$lastPageElement->appendChild($this->createPageElement($this->itsPagination->getLast()));
			
			if ($this->getBuildOption(self::BUILD_LOCALES)) {
				
				$lastPageElement->createAppendElement("locales", array(
					"title" => __("Last")
				));
				
			}
			
		}
		
		if ($this->getBuildOption(self::BUILD_LOCALES)) {
			
			$paginationElement->createAppendElement("locales", array(
				"title" => __("Page"),
				"of" => __("of"),
				"pages" => _n("page", "pages", $this->itsPagination->getTotal()),
				"items" => _n("item", "items", $this->itsPagination->getTotalItems())
			));
			
		}
		
		return $this;
	}
	
	protected function createPageElement($page) {
		$page = (array) $page;
		
		$page["range"]["text"] = vsprintf($this->getBuildOption(self::RANGE_FORMAT), $page["range"]);
		
		$elements = array_diff_key($page, array_flip(array("current")));
		
		$pageElement = $this->createElement("page", $elements);
		
		if ($page["current"])
			$pageElement->setAttribute("current", "1");
			
		return $pageElement;
	}
	
}