<?php namespace M3W\admin\preset_modules;

use M3W\admin\DashboardModule;

class InfoCard implements DashboardModule
{
	
	private $title;
	private $caption;
	private $value;
	private $background;
	
	public function __construct($title, $caption, $value, $background) {
		$this->title = $title;
		$this->caption = $caption;
		$this->value = $value;
		$this->background = $background;
	}
	
	public function calculateValue() {
		if (is_callable($this->value)) { return call_user_func($this->value); }
		else { return $this->value; }
	}
	
	public function getHTML() {
		return sprintf('<div class="info-card" style="background-image: url(%s)"><span class="title">%s</span><span class="value">%s</span><span class="caption">%s</span></div>',
					$this->background,
					$this->title,
					$this->calculateValue(),
					$this->caption
				 );
	}

	public function getSpan() {
		return 1;
	}

}