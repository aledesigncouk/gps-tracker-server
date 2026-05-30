<?php

namespace Alex\GpsTrackerServer\actions;

use Alex\GpsTrackerServer\classes\Database;
use Alex\GpsTrackerServer\classes\Route;

class ReadYearsAPI
{
    private $db;
    private $dbTable;
    private $route;

    public function __construct($database, $route)
    {
        $this->db      = $database->getConnection();
        $this->dbTable = $database->getTableName();
        $this->route   = $route;
    }

    public function handleGetRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['message' => 'Unsupported request method.']);
            return;
        }

        echo json_encode($this->route->getYears($this->db, $this->dbTable));
    }
}
