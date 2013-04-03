<?php
namespace lowtone\ui\pagination;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\ui\pagination
 */
abstract class Pagination implements interfaces\Pagination {
	
	/**
	 * The default number of visible pages.
	 * @var int
	 */
	protected $itsVisiblePages = 5;
	
	/**
	 * Create a page object for the given page number.
	 * @param int $number The number for the required page object.
	 * @return object Returns a page object.
	 */
	protected function createPage($number) {
		return (object) array(
			"current" => (int) $number == $this->getCurrent(),
			"number" => $number,
			"range" => array(
				"start" => ($start = ($number - 1) * $this->getItemsPerPage()) + 1,
				"end" => (($end = $start + $this->getItemsPerPage()) > ($totalItems = $this->getTotalItems()) ? $totalItems : $end)
			),
			"url" => $this->getPageURL($number)
		);
	}
	
	/**
	 * Calculate the number of pages visible before the current within the 
	 * specified visible range. By default using an odd number of visible pages
	 * will have the current page exactly in the center while an even number 
	 * will have the current page before the center (more pages ahead than 
	 * behind).
	 * @param int $num The number of pages within the visible range.
	 * @return int Returns the number of pages visible before the current.
	 */
	protected function calculateBefore($num) {
		return ceil($num / 2) - 1;
	}
	
	/**
	 * Checks if the first page is within the visible range.
	 * @param int|NULL $num The number of visible pages. Defaults to the value 
	 * set by Pagination::itsVisiblePages.
	 * @return bool Returns TRUE if the first page is within the visible range.
	 */
	public function hasFirst($num = NULL) {
		return reset($this->getPageRange($num)) > 1;
	}
	
	/**
	 * Checks if the last page is within the visible range.
	 * @param int|NULL $num The number of visible pages. Defaults to the value 
	 * set by Pagination::itsVisiblePages.
	 * @return bool Returns TRUE if the last page is within the visible range.
	 */
	public function hasLast($num = NULL) {
		return end($this->getPageRange($num)) < $this->getTotal();
	}
	
	/**
	 * Checks if a next page is available.
	 * @return bool Returns TRUE if a page is available after the current.
	 */
	public function hasNext() {
		return $this->getCurrent() < $this->getTotal();
	}
	
	/**
	 * Checks if a previous page is available.
	 * @return bool Returns TRUE if a page is available before the current.
	 */
	public function hasPrevious() {
		return $this->getCurrent() > 1;
	}
	
	// Getters
	
	/**
	 * Determine the first and last page within the visible pages range.
	 * @param int|NULL $num The number of visible pages. Defaults to the value 
	 * set by Pagination::itsVisiblePages.
	 * @return array Returns an associative array with keys 'start' and 'end' 
	 * with their corresponding values.
	 */
	protected function getPageRange($num = NULL) {
		if (!is_numeric($num))
			$num = (int) $this->itsVisiblePages;
		
		$before = $this->calculateBefore($num);
		$start = $this->getCurrent() - $before;
		
		if ($start < 1)
			$start = 1;
			
		$end = $start + $num - 1;
		
		if ($end > ($total = $this->getTotal()))
			$end = $total;
		
		return array(
			"start" => $start,
			"end" => $end
		);
	}
	
	/**
	 * Get a page object for the first page.
	 * @return object Returns a page object.
	 */
	public function getFirst() {
		return $this->createPage(1);
	}
	
	/**
	 * Get a page object for the last page.
	 * @return object Returns a page object.
	 */
	public function getLast() {
		return $this->createPage($this->getTotal());
	}
	
	/**
	 * Get a page object for the next page.
	 * @return object Returns a page object.
	 */
	public function getNext() {
		return $this->createPage($this->getCurrent() + 1);
	}
	
	/**
	 * Get a page object for the previous page.
	 * @return object Returns a page object.
	 */
	public function getPrevious() {
		return $this->createPage($this->getCurrent() - 1);
	}
	
	/**
	 * Get a list of page objects for all visible pages.
	 * @param int $num The number of visible pages.
	 * @return array Returns a list of page objects.
	 */
	public function getVisiblePages($num = NULL) {
		list($start, $end) = array_values($this->getPageRange($num));
		
		for ($i = $start, $pages = array(); $i <= $end; $i++) 
			$pages[] = $this->createPage($i);
		
		return $pages;
	}
	
	// Setters
	
	public function setVisiblePages($num) {$this->itsVisiblePages = (int) $num; return $this;}
	
}