<?php

class IMysql {
	protected $conn;

	public function connect($host, $port, $user, $passwd, $dbname) {
		if ($port) {
			$host = $host.":".$port;
		}
		$conn = mysql_connect($host, $user, $passwd);
		mysql_select_db($dbname, $conn);
		$this->conn = $conn;
	}
	public function query($sql) {
 		return mysql_query($sql, $this->conn);
	}
	public function queryAndFetchAssoc($sql) {
		$res = $this->query($sql);
 		while($row = mysql_fetch_assoc($res)){
 			$list[] = $row;
 		}
 		return $list;
	}
	public function getLastInsertId() {
		return  mysql_insert_id();
	}
	public function close() {
		mysql_close($this->conn);
	}
}