<?php

namespace M3W\admin;

class esLocale extends enLocale
{
	public $username = "Nombre de usuario";
	public $password = "Contraseña";
	public $login    = "Login";
	
	public function getLangCode() {
		return 'es';
	}
}
