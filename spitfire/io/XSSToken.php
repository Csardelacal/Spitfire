<?php namespace spitfire\io;

use spitfire\io\renderers\RenderableFieldHidden;

class XSSToken implements RenderableFieldHidden
{
	
	private $session;
	
	public function getEnforcedRenderer() {
		return null;
	}

	public function getPostId() {
		return '_XSS_';
	}
	
	private function getSession() {
		
		if ($this->session === null) {
			$this->session = new session();
		}
		
		return $this->session;
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
		return renderers\RenderableField::VISIBILITY_ALL;
	}

}