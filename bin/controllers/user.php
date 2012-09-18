<?php

class controller_user extends controller
{
	
	public function index($object, $params) {
		
		$usernames = Array( 'Csardelacal', '03142945H', 'Csar01234', 'Csar_01234', 't', '_Csar01234', 'Csar:01234'	);
		
		foreach ($usernames as $username) {
			$t = new _SF_InputSanitizer($username);
			if ($w = $t->toPassword() ) echo $w, '<br/>';
			else echo "Not a valid password <br/>";
		}

		if ($t = $this->get->say_something->toInt()) echo "The value of say something is $t";
	}
	
	public function detail($object, $params){
		if (valid_username($object) ) {
			$db = db::getInstance();
			$result = $db->query("SELECT * FROM `users`, `cv` WHERE `username` = '{$object}' and `users`.`iduser` = `cv`.`iduser`");
			while ($user = $result->fetch_object('safeDBRead') ) {
				//echo $user;
				echo $user->userName;
				echo $user->bio;
			}
		}
	}
	
}