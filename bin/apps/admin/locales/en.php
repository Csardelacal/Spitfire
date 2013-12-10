<?php

namespace M3W\admin;

use spitfire\locale\sys\En;

class EnLocale extends En
{
	public $username = "Username";
	public $password = "Password";
	public $login    = "Login";
	
	public function getLangCode() {
		return 'en';
	}
}
