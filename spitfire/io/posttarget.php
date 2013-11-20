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
	
	public function clearPostData() {
		if (is_array($this->postData)) {
			$keys = array_keys($this->postData);
			foreach ($keys as $post) {
				$postTarget = $this->getPostTargetFor($post);
				if ($postTarget !== null) { $postTarget->clearPostData(); }
			}
		}
		$this->postData = null;
	}
	
	public function issetPostData() {
		return isset($this->postData);
	}
	
	private function propagate() {
		if (!is_array($this->postData)) { return; }
		foreach ($this->postData as $key => $value) {
			$target = $this->getPostTargetFor($key);
			if ($target !== null) { $target->setPostData($value); }
		}
	}
	
	abstract public function getPostTargetFor($name);
	
}