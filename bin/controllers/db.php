<?php

class controller_db
{
	public function index($object, $params) {
		echo 'Use create, truncate, delete or insert actions';
	}
	
	public function detail($object, $params) {
		echo 'Detail method not present';
	}
	
	public function create($object, $params) {
		$pdo = pdo();
		$result = $pdo->query('CREATE TABLE followers (stalker BIGINT, victim BIGINT)');
		print_r($result);
		$result = $pdo->query('CREATE TABLE notifications (src BIGINT, dest BIGINT, msg VARCHAR(45))');
		print_r($result);
	}
	
	public function truncate($object, $params) {
		$pdo = pdo();
		$result = $pdo->query('TRUNCATE TABLE followers');
		$result = $pdo->query('TRUNCATE TABLE notifications');
	}
	
	public function delete($object, $params) {
		$pdo = pdo();
		$result = $pdo->query('DROP TABLE followers');
		$result = $pdo->query('DROP TABLE notifications');
		print_r($result);
	}
	
	public function insert($object, $params) {
		$pdo = pdo();
		$stmt = $pdo->prepare('INSERT INTO followers VALUES (?,?)');
		for ($i = 0; $i < 5; $i++) {
			$stmt->execute(Array($i, 1));
		}
		$stmt = $pdo->prepare("INSERT INTO notifications SELECT victim, stalker, :msg FROM followers WHERE victim = :victim");
		$stmt->execute(Array('msg' => 'Hello world', 'victim' => 1));
	}
	
	public function test($object, $params) {
		$db = pdo();
		$stmt = $db->prepare("SELECT * FROM notifications WHERE dest = :dest");
		$stmt->execute(Array('dest' => 1));
		while ($result = $stmt->fetchObject('DBO')) print_r($result->msg);
	}
}