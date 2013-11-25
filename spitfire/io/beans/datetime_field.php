<?php namespace spitfire\io\beans;

use spitfire\io\renderers\RenderableFieldDateTime;
use spitfire\validation\ValidationResult;
use spitfire\validation\ValidationError;

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
	
	public function validate() {
		$post = parent::getRequestValue();
		$errs = Array();
		
		if ((int)$post['hours']   > 23) {$errs[] = new ValidationError('There are not more than 24 hours a day', '', $this);}
		if ((int)$post['hours']   <  0) {$errs[] = new ValidationError('Hours cannot be negative', '', $this);}
		if ((int)$post['minutes'] <  0) {$errs[] = new ValidationError('Minutes cannot be negative', '', $this);}
		if ((int)$post['minutes'] > 59) {$errs[] = new ValidationError('Minutes cannot be more than 59', '', $this);}
		
		if (!empty($errs)) { return new ValidationResult($errs); }
		else { return false; }
	}

}