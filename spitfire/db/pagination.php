<?php

use spitfire\storage\database\Query;

/**
 * This class is the base for the database query pagination inside of Spitfire.
 * It provides the necessary tools to generate a list of pages inside your 
 * applications so queries aren't able to collapse your system / clients.
 * 
 * By default this class includes a getEmpty method that returns a message when 
 * no results are available. Although it is not a good practice to allow classes
 * perform actions that aren't strictly related to their task. But the improvement
 * on readability gained in Views is worth the change.
 * 
 * @link http://www.spitfirephp.com/wiki/index.php/Database/pagination Related data and tutorials
 * 
 * @todo Somehow this class should cache the counts, so the database doesn't need to read the data every time.
 * @todo This class should help paginating without the use of LIMIT
 */
class Pagination
{
	private $query;
	private $maxJump = 3;
	
	private $url       = false;
	private $param     = 'page';
	private $pages     = Array();
	private $pageCount = null;
	
	public function __construct(Query $query = null, $name = null) {
		if ($query !== null && $query->getResultsPerPage() < 1) {
			$query->setResultsPerPage(20);
		}
		
		$this->query = $query;
		$this->name  = $name;
		$this->query->setPage((int)$_GET[$this->param][$this->getName()]);
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
	 * Returns the paginator URL. The URL will be used to replace the value of the
	 * parameter this class uses to add an entry for this pagination.
	 * 
	 * @return URL
	 */
	public function getURL() {
		if ($this->url) {
			return $this->url;
		} else {
			return $this->url = URL::current();
		}
	}
	
	public function makeURL($page) {
		if (!$this->isValidPageNumber($page)) return null;
		
		$url   = $this->getURL();
		$pages = $url->getParameter($this->param);
		$name  = $this->getName();
		
		if (!is_array($pages)) $pages = Array();
		$pages[$name] = $page;
		$url->setParam($this->param, $pages);
		return $url;
	}
	
	public function getName() {
		return ($this->name !== null)? $this->name : '*';
	}
	
	/**
	 * This function calculates the pages to be displayed in the pagination. It 
	 * calculates the ideal amount of pages to be displayed (based on the max you want)
	 * and generates an array with the numbers for those pages.
	 * 
	 * If you use the default maxJump of 3 you will always receive up to 9 pages.
	 * Those include the first, the last, the current and the three higher and lower
	 * pages. For page 7/20 you will receive (1,4,5,6,7,8,9,10,20).
	 * 
	 * In case the pagination doesn't find enough elements whether on the right or
	 * left it will try to extend this with results on the other one. This avoids
	 * broken looking paginations when reaching the final results of a set.
	 * 
	 * @return array
	 */
	public function getPageNumbers() {
		#Adds the maxjump up with the special pages (first, last, current)
		$iterationLimit = $slots = $this->maxJump * 2 + 3;
		$current = $this->getCurrentPage();
		
		if ($this->addPage($current)) $slots--;
		if ($this->addPage(1)) $slots--;
		if ($this->addPage($this->getPageCount())) $slots--;
		
		for ($i = 0; $i < $iterationLimit; $i++) {
			if ($slots > 0) if ($this->addPage ($current + $i)) $slots--;
			if ($slots > 0) if ($this->addPage ($current - $i)) $slots--;
		}
		
		$this->pages = array_filter($this->pages);
		sort($this->pages);
		
		return $this->pages;
	}
	
	public function addPage($number) {
		if (in_array($number, $this->pages)) return false;
		return $this->pages[] = $this->isValidPageNumber($number);
	}
	
	/**
	 * This function checks whether the page is a good candidate for being added. 
	 * Therefore performing three checks before allowing a pagination to add it:
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
	public function isValidPageNumber($number) {
		if ($number < 1)                     return false;
		if ($number > $this->getPageCount()) return false;
		
		return $number;
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
	
	/**
	 * This function receives a caption and a URL to generate one page's link for
	 * your pagination. This function is by default designed to work best with 
	 * the most common CSS frameworks out there (Bootstrap and Foundation) and will
	 * probably work with many others.
	 * 
	 * If you want to change the output of every page do this here. Simply create
	 * a class that extends this one and replace this method with whatever you 
	 * fancy printing.
	 * 
	 * @param string $caption
	 * @param URL $url
	 * @return string
	 */
	public function stringifyPage($caption, $url, $current = false) {
		if ($url !== null) {
			$class = $current? ' class="active current"' : '';
			return sprintf('<li%s><a href="%s">%s</a></li>', $class, $url, $caption);
		}
		else {
			return sprintf('<li class="disabled unavailable"><a>%s</a></li>', $caption);
		}
	}
	
	public function getEmpty() {
		return '<!--Automatically generated by Pagination::getEmpty()-->'
		. '<div><center><i>No results to display...</i></center></div>'
		. '<!---Automatically generated by Pagination::getEmpty()-->';
	}

	public function __toString() {
		$pages      = $this->getPageNumbers();
		$previous   = 0;
		$current    = $this->getCurrentPage();
		$pages_html = Array();
		
		if (empty($pages)) return $this->getEmpty();
		
		//Previous
		$pages_html[] = $this->stringifyPage('&laquo;', $this->makeURL($current-1));
		//Pages
		foreach ($pages as $page) {
			if ($previous + 1 < $page) {
				$pages_html[] = $this->stringifyPage('...', null);
			}
			$pages_html[] = $this->stringifyPage($page, $this->makeURL($page), $page === $current);
			$previous = $page;
		}
		//Next
		$pages_html[] = $this->stringifyPage('&raquo;', $this->makeURL($current+1));
		
		return '<ul class="pagination">' . implode('', $pages_html) . '</ul>';
	}
	
}