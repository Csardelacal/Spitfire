<?php

class adminApp extends App
{
	public function getAssetsDirectory() {
		
	}

	public function getTemplateDirectory() {
		
	}

	public function enable() {
		spitfire()->registerController('admin', $this->basedir . 'admin.php', $this);
	}
}