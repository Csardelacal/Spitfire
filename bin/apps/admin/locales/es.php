<?php

namespace M3W\admin;

use spitfire\locale\sys\Es;

class EsLocale extends Es
{
	public $username = "Nombre de usuario";
	public $password = "Contraseña";
	public $login    = "Login";
	
	public function getLangCode() {
		return 'es';
	}
}
