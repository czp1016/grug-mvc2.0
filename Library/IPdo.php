<?php

class IPdo {
	protected $conn;
	function connect($host, $user, $passwd, $dbname) {
		$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $passwd);
		$this->conn = $conn;
	}
	function query($sql) {
		return $this->conn->exec($sql);
	}
	function queryAndFetchAssoc($sql) {
		$this->conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
		$res = $this->conn->query($sql);
 		$res->setFetchMode(PDO::FETCH_ASSOC);
 		$list = $res->fetchAll();
 		return $list;
	}
	public function getLastInsertId() {
		return  $this->conn->lastInsertId();
	}
	function close() {
		unset($this->conn);
	}
}