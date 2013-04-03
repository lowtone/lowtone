<?php
namespace lowtone\ui\pagination\interfaces;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\ui\pagination\interfaces
 */
interface Pagination {
	
	// Pages
	
	/**
	 * Get the current page number.
	 * @return int Returns the number for the current page.
	 */
	public function getCurrent();
	
	/**
	 * Get the total number of pages.
	 * @return int Returns the total number of pages.
	 */
	public function getTotal();
	
	/**
	 * Get an URL for the given page number.
	 * @param int $number The page number for the required URL.
	 * @return string Returns an URL for the required page number.
	 */
	public function getPageURL($number);
	
	// Items
	
	/**
	 * Get the number of items per page.
	 * @return int Returns the number of items per page.
	 */
	public function getItemsPerPage();
	
	/**
	 * Get the total number of items.
	 * @return int Returns the total number of items.
	 */
	public function getTotalItems();
	
}