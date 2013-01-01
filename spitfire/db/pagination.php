<?php

class Pagination
{
	/**
	 *
	 * @var _SF_DBQuery Query to be paginated. 
	 */
	private $query;
	private $maxJump = 3;
	
	private $url     = false;
	private $param   = 'page';
	
	public function __construct(_SF_DBQuery $query) {
		$this->query = $query;
	}
	
	public function getCurrentPage () {
		return $this->query->getPage();
	}
	
	public function getPageCount() {
		$rpp     = $this->query->getResultsPerPage();
		
		$this->query->setResultsPerPage(-1);
		$results = $this->query->count();
		$this->query->setResultsPerPage($rpp);
		return ceil($results/$rpp);
	}
	
	public function getPageNumbers() {
		$max   = $this->max = $this->getPageCount();
		$pages = Array();
		$pages[] = $this->getCurrentPage();
		if (!in_array(1, $pages)) $pages[] = 1;
		if (!in_array($max, $pages)) $pages[] = $max;
		
		for ($i = 1; $i < $this->maxJump + 1; $i++) {
			$page = $this->getCurrentPage() - $i;
			if ($page > 1 && !in_array($page, $pages)) $pages[] = $page;
		}
		
		for ($i = 1; $i < $this->maxJump*2 + 1; $i++) {
			$page = $this->getCurrentPage() + $i;
			if ($page < $max && !in_array($page, $pages) && count($pages) < $this->maxJump*2 + 2) $pages[] = $page;
		}
		
		sort($pages);
		
		return $pages;
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
			return '<li class="disabled"><a href="' . $this->url . '">' . $caption . '</a>';
		}
		if ($number == $this->getCurrentPage()) {
			return '<li class="active"><a href="' . $this->url . '">' . $caption . '</a>';
		}
		else {
			return '<li><a href="' . $this->url . '">' . $caption . '</a>';
		}
	}

	public function __toString() {
		$pages = $this->getPageNumbers();
		$this->url = $this->url? $this->url : SpitFire::$current_url;
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
		
		return '<ul>' . implode('', $pages_html) . '</ul>';
	}
	
}