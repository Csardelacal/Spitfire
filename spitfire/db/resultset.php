<?php

namespace spitfire\storage\database\drivers;

interface resultSetInterface
{
	public function fetch();
	public function fetchAll();
}