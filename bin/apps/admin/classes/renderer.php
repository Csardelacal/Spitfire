<?php

namespace M3W\admin;

use URL;
use spitfire\io\renderers\SimpleFormRenderer;
use CoffeeBean;

class Renderer extends SimpleFormRenderer
{
	
	public function getListActions($bean, $record) {
		$actions = parent::getListActions($bean, $record);
		$primary = $record->getPrimaryData();
		
		$app = spitfire()->findAppForClass('M3W\admin\\');
		
		$actions[] = '<a href="' . new URL($app, 'edit', $bean->getName(), implode('|', $primary)) . '">Edit</a>';
		$actions[] = '<a href="' . new URL($app, 'delete', $bean->getName(), implode('|', $primary)) . '">Delete</a>';
		
		return $actions;
	}
	
	public function getFormAction(CoffeeBean$bean) {
		$record  = $bean->getRecord();
		$app = spitfire()->findAppForClass('M3W\admin\\');
		
		if ($record) { $primary = implode(':', $record->getPrimaryData()); }
		else { $primary = null; }
		
		
		if (strlen($primary)) { return new URL($app, 'edit', $bean->getName(), $primary); }
		else { return new URL($app, 'create', $bean->getName()); }
	}
	
	public function stringifyHeader($field) {
		$sorting_field = isset($_GET['order']['field'])? $_GET['order']['field'] : null;
		$sort_method = ($sorting_field === $field->getField()->getName()) ? $_GET['order']['method'] : false;
		$str = parent::stringifyHeader($field);
		
		if ($sort_method === false || $sort_method === 'desc') { $method = 'asc';}
		else {$method = 'desc';}
		
		return sprintf('<a href="%s" class="%s">%s</a>', 
				  URL::current()->setParam('order', Array('field' => $field->getField()->getName(), 'method' => $method)), $this->getClass($sort_method), $str);
	}
	
	public function getClass($sort_method) {
		switch ($sort_method) {
			case false : return 'unsorted';
			case 'asc' : return 'sorted asc';
			case 'desc': return 'sorted desc';
		}
	}
	
}