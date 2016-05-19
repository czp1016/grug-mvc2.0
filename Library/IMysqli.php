<?php

class IMysqli {
	protected $conn;
	public function connect($host, $port, $user, $passwd, $dbname) {
		$conn = mysqli_connect($host, $user, $passwd, $dbname, $port);
		$this->conn = $conn;
	}
	public function query($sql) {
		$res = mysqli_query($this->conn, $sql);
		return $res;
	}
	public function queryAndFetchAssoc($sql) {
		$res = $this->query($sql);
 		while($row = mysqli_fetch_assoc($res)){
 			$list[] = $row;
 		}
 		return $list;
	}
	public function getLastInsertId() {
		return  mysqli_insert_id($this->conn);
	}
	public function close() {
		mysqli_close($this->conn);
	}
}