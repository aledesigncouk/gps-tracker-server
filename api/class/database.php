<?php

include_once "../../config/config.php";

class Database {

	private $dbHost;
	private $dbUser;
	private $dbPwd;
	private $db;
	private $dbTable;
	private $conn;

	public function __construct() {
		global $dbHost, $dbUsername, $dbPassword, $dbName, $dbTableName;
		$this->dbHost = $dbHost;
		$this->dbUser = $dbUsername;
		$this->dbPwd = $dbPassword;
		$this->db = $dbName;
		$this->dbTable = $dbTableName;
		$this->conn = null;
	}

	public function getConnection() {
		try {
			$dsn = "mysql:host={$this->dbHost};dbname={$this->db};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->dbUser, $this->dbPwd);

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
           
        } catch (PDOException $e) {
            die("Error: Failed to connect to the database. " . $e->getMessage());
        }

        return $this->conn;

	}

	public function getTableName() {
		return $this->dbTable;
	}
}