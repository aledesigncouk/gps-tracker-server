<?php

include_once "config.php";

class Database {

	private $dbhost;
	private $dbuserame;
	private $dbpwd;
	private $db;
	private $dbtable;
	private $conn;

	public function __construct() {
		global $host, $username, $password, $dbname;
		$this->dbhost = $host;
		$this->dbuser = $username;
		$this->dbpwd = $password;
		$this->db = $dbname;
		$this->dbtable = $dbtable;
		$this->conn = null;
	}

	public function getConnection() {
		try {
			$dsn = "mysql:host={$this->dbhost};dbname={$this->db};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->dbuser, $this->dbpwd);

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
           
        } catch (PDOException $e) {
            die("Error: Failed to connect to the database. " . $e->getMessage());
        }

        return $this->conn;

	}
}