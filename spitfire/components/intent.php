<?php

class AppConfig
{
	private $models;
	private $beans;
	private $meta;
	
	public function putModel($model) {
		$this->models[] = $model;
	}
	
	public function putBean($bean) {
		$this->beans[] = $bean;
	}
	
	public function setMeta($key, $data) {
		$this->meta[$key] = $data;
	}
	
	public function getModels() {
		return $this->models;
	}
	
	public function getBeans() {
		return $this->beans;
	}
	
	public function getMeta($key = null) {
		if (is_null($key)) return $this->meta;
		else return $this->meta[$key];
	}
}