<?php

use spitfire\storage\database\Query;

class Pagination
{
	private $query;
	private $maxJump = 3;
	
	private $url       = false;
	private $param     = 'page';
	private $pages     = [];
	private $pageCount = null;
	
	public function __construct(Query $query = null, $name = null) {
		if ($query !== null && $query->getResultsPerPage() < 1) {
			$query->setResultsPerPage(20);
		}
		
		$this->query = $query;
	}
	
	public function getCurrentPage () {
		return $this->query->getPage();
	}
	
	public function getPageCount() {
		if ($this->pageCount !== null) return $this->pageCount;
		
		$rpp     = $this->query->getResultsPerPage();
		$this->query->setResultsPerPage(-1);
		$results = $this->query->count();
		$this->query->setResultsPerPage($rpp);
		
		return $this->pageCount = ceil($results/$rpp);
	}
	
	/**
	 * This function calculates the pages to be displayed in the pagination. It 
	 * calculates the ideal amount of pages to be displayed (based on the max you want)
	 * and generates an array with the numbers for those pages.
	 * 
	 * @return array
	 */
	public function getPageNumbers() {
		//Adds the maxjump up with the special pages (first, last, current)
		$iterationLimit = $slots = $this->maxJump * 2 + 3;
		$current = $this->getCurrentPage();
		
		if ($this->addPage($current)) $slots--;
		if ($this->addPage(1)) $slots--;
		if ($this->addPage($this->getPageCount())) $slots--;
		
		for ($i = 0; $i < $iterationLimit; $i++) {
			if ($slots > 0) if ($this->addPage ($current + $i)) $slots--;
			if ($slots > 0) if ($this->addPage ($current - $i)) $slots--;
		}
		
		sort($this->pages);
		
		return $this->pages;
	}
	
	/**
	 * Adds a page to the pagination. This function checks whether the page is a 
	 * good candidate for being added. Therefore performing three checks before 
	 * adding it:
	 * <ul>
	 * <li>If the page already exists</li>
	 * <li>If the page is lower than one</li>
	 * <li>If the page number is higher than the highest</li>
	 * </ul>
	 * If any of those fails the page won't be added to the set.
	 * 
	 * @param int $number The page number we wanted to add to the query.
	 * @return boolean If the page was added to the pagination
	 */
	public function addPage($number) {
		if (in_array($number, $this->pages)) return false;
		if ($number < 1)                     return false;
		if ($number > $this->getPageCount()) return false;
		
		$this->pages[] = $number;
		return true;
	}
	
	/**
	 * Sets the URL base that is used for pagination URL's. By default no
	 * URL and page are used for parameters
	 * @param URL $url
	 * @param string $param
	 */
	public function setURL(URL $url, $param) {
		$this->url   = $url;
		$this->param = $param;
	}
	
	protected function makePage($number, $caption, $disabled = false) {
		$this->url->setParam($this->param, $number);
		
		if ($number < 1 || $number > $this->max || $disabled ) {
			$this->url->setParam($this->param, 1);
			return '<li class="disabled unavailable"><a href="' . $this->url . '">' . $caption . '</a>';
		}
		if ($number == $this->getCurrentPage()) {
			return '<li class="active current"><a href="' . $this->url . '">' . $caption . '</a>';
		}
		else {
			return '<li><a href="' . $this->url . '">' . $caption . '</a>';
		}
	}

	public function __toString() {
		$pages = $this->getPageNumbers();
		$this->url = $this->url? $this->url : URL::current();
		$max   = $this->max = $this->getPageCount();
		$previous = 0;
		
		$pages_html = Array();
		
		//Previous
		$pages_html[] = $this->makePage($this->getCurrentPage() - 1, '&laquo;');
		//Pages
		foreach ($pages as $page) {
			if ($previous + 1 < $page) {
				$pages_html[] = $this->makePage(1, '...', true);
			}
			$pages_html[] = $this->makePage($page, $page);
			$previous = $page;
		}
		//Next
		$pages_html[] = $this->makePage($this->getCurrentPage() + 1, '&raquo;');
		
		return '<ul class="pagination">' . implode('', $pages_html) . '</ul>';
	}
	
}