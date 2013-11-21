<?php namespace spitfire\io\beans;

use spitfire\io\renderers\RenderableFieldDateTime;

class DateTimeField extends BasicField implements RenderableFieldDateTime
{
	public function getRequestValue() {
		$pd = parent::getRequestValue();
		$ts =  mktime($pd['hours'], $pd['minutes'], 0, $pd['month'], $pd['day'], $pd['year']);
		
		return date('Y-m-d H:i:s', $ts);
	}

	public function getEnforcedFieldRenderer() {
		return null;
	}

}