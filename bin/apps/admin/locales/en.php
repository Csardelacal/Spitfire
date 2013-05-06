<?php

namespace M3W\admin;

use Locale;

class enLocale extends Locale
{
	public $username = "Username";
	public $password = "Password";
	public $login    = "Login";
	
	public function getLangCode() {
		return 'en';
	}
}
