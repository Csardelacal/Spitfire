<?php namespace spitfire\io;

use spitfire\io\renderers\RenderableFieldHidden;
use spitfire\io\session\Session;

class XSSToken implements RenderableFieldHidden
{
	
	public function getEnforcedFieldRenderer() {
		return null;
	}

	public function getPostId() {
		return '_XSS_';
	}
	
	private function getSession() {
		return Session::getInstance();
	}

	public function getValue() {
		$session = $this->getSession();
		if (false == $xss_token = $session->get('_XSS_')) {
			$xss_token = base64_encode(rand());
			$session->set('_XSS_', $xss_token);
		}
		
		return $xss_token;
	}

	public function getVisibility() {
		return renderers\RenderableField::VISIBILITY_FORM;
	}

	public function getCaption() {
		return null;
	}

}