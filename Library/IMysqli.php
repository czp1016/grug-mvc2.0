<?php

class IMysqli {
	protected $conn;
	function connect($host, $user, $passwd, $dbname) {
		$conn = mysqli_connect($host, $user, $passwd, $dbname);
		$this->conn = $conn;
	}
	function query($sql) {
		$res = mysqli_query($this->conn, $sql);
		return $res;
	}
	function queryAndFetchAssoc($sql) {
		$res = $this->query($sql);
 		while($row = mysqli_fetch_assoc($res)){
 			$list[] = $row;
 		}
 		return $list;
	}
	public function getLastInsertId() {
		return  mysqli_insert_id($this->conn);
	}
	function close() {
		mysqli_close($this->conn);
	}
}