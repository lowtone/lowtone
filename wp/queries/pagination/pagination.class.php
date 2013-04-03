<?php
namespace lowtone\wp\queries\pagination;
use lowtone\wp\queries\Query;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\wp\queries\pagination
 */
class Pagination extends \lowtone\ui\pagination\Pagination {
	
	/**
	 * @var Query
	 */
	protected $itsQuery;
	
	public function __construct(Query $query) {
		$this->itsQuery = $query;
		
	}
	
	// Getters
	
	public function getPageURL($number) {
		return esc_url(apply_filters("paginate_links", get_pagenum_link($number)));
	}
	
	public function getQuery() {
		return $this->itsQuery;
	}
	
	public function getCurrent() {
		return $this->itsQuery->getQueryVar("paged") ?: 1;
	}
	
	public function getTotal() {
		return $this->itsQuery->getMaxNumPages();
	}
	
	public function getItemsPerPage() {
		return $this->itsQuery->getPostCount();
	}
	
	public function getTotalItems() {
		return $this->itsQuery->getFoundPosts();
	}
	
}