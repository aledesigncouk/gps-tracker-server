<?php

include "../../config.php";

class Database {

	private $dbhost;
	private $dbuserame;
	private $dbpwd;
	private $db;

	public function __construct() {
		global $host, $username, $password, $dbname;
		$this->dbhost = $host;
		$this->dbuser = $username;
		$this->dbpwd = $password;
		$this->db = $dbname;
	}

	public function getConnection() {
		$conn = new mysqli($this->dbhost, $this->dbuser, $this->dbpwd, $this->db);
		if ($conn->connect_error) {
			die("Error failed to connect to MySQL: " . $conn->connect_error);
		} else {
			return $conn;
		}
	}
}
