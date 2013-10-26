<?php namespace spitfire\locale\sys\es;

class Es extends \spitfire\locale\sys\Es
{
	
	public function getLangCode() {
		return 'es';
	}
	
	public function getCurrency() {
		return '€';
	}
	
	public function convertCurrency($amt) {
		return $amt / 1.31;
	}
	
}