<?php

class deLocale extends Locale
{
	public $comment_count = Array('Keine Kommentare', 'Ein kommentar', '%s Kommentare');
	
	public function convertCurrency($original) {
		return parent::convertCurrency($original) / 1.30;
	}

	public function getCurrency() {
		return '€';
	}
	
	public function getLangCode() {
		return 'de';
	}
}