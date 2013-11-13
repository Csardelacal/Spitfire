<?php namespace spitfire\io;

abstract class PostTarget
{
	
	private $postData;
	
	public function getPostData() {
		return $this->postData;
	}
	
	public function setPostData($post) {
		$this->postData = $post;
		$this->propagate();
	}
	
	private function propagate() {
		foreach ($this->postData as $key => $value) {
			$target = $this->getPostTargetFor($key);
			$target->setPostData($value);
		}
	}
	
	abstract public function getPostTargetFor($name);
	
}