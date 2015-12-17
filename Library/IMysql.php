<?php

class IMysql {
	protected $conn;
	function connect($host, $user, $passwd, $dbname) {
		$conn = mysql_connect($host, $user, $passwd);
		mysql_select_db($dbname, $conn);
		$this->conn = $conn;
	}
	function query($sql) {
		$res = mysql_query($sql, $this->conn);
 		return $res;
	}
	function queryAndFetchAssoc($sql) {
		$res = $this->query($sql);
 		while($row = mysql_fetch_assoc($res)){
 			$list[] = $row;
 		}
 		return $list;
	}
	public function getLastInsertId() {
		return  mysql_insert_id();
	}
	function close() {
		mysql_close($this->conn);
	}
}