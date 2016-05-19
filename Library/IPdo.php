<?php

class IPdo {
	protected $conn;
	public function connect($host, $port, $user, $passwd, $dbname) {
		$conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $passwd);
		$this->conn = $conn;
	}
	public function query($sql) {
		return $this->conn->exec($sql);
	}
	public function queryAndFetchAssoc($sql) {
		$this->conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
		$res = $this->conn->query($sql);
 		$res->setFetchMode(PDO::FETCH_ASSOC);
 		$list = $res->fetchAll();
 		return $list;
	}
	public function getLastInsertId() {
		return  $this->conn->lastInsertId();
	}
	public function close() {
		unset($this->conn);
	}
}