<?php namespace M3W\admin;

class DashboardModuleRenderer
{
	
	public function toHTML(DashboardModule$item) {
		return sprintf('<div class="span%s">%s</div>', $item->getSpan(), $item->getHTML());
	}
	
}