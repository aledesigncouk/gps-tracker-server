<?php

namespace Alex\GpsTrackerServer\actions;

use Alex\GpsTrackerServer\classes\Database;
use Alex\GpsTrackerServer\classes\Route;

class ReadLatestAPI
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

        $point = $this->route->getLatestPoint($this->db, $this->dbTable);

        if ($point === null) {
            http_response_code(404);
            echo json_encode(['message' => 'No points recorded yet.']);
            return;
        }

        echo json_encode([
            'id'       => (int) $point['id'],
            'datatime' => $point['datatime'],
            'lat'      => (float) $point['lat'],
            'lon'      => (float) $point['lon'],
        ]);
    }
}
