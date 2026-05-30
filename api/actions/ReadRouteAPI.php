<?php

namespace Alex\GpsTrackerServer\actions;

use Alex\GpsTrackerServer\classes\Database;
use Alex\GpsTrackerServer\classes\Route;

class ReadRouteAPI
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

    private function toGeoJson($points)
    {
        usort($points, function ($a, $b) {
            return strtotime($a['datatime']) - strtotime($b['datatime']);
        });

        $coordinates = array_map(function ($item) {
            return [floatval($item['lat']), floatval($item['lon'])];
        }, $points);

        echo json_encode([
            'type'       => 'Feature',
            'properties' => new \stdClass(),
            'geometry'   => [
                'type'        => 'LineString',
                'coordinates' => $coordinates,
            ],
        ]);
    }

    public function handleGetRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['message' => 'Unsupported request method.']);
            return;
        }

        $start = $_GET['start'] ?? null;
        $end   = $_GET['end']   ?? null;

        if ($start === null && $end === null) {
            http_response_code(400);
            echo json_encode(['message' => "Missing 'start' and 'end' parameters."]);
            return;
        }
        if ($start === null) {
            http_response_code(400);
            echo json_encode(['message' => "Missing 'start' parameter."]);
            return;
        }
        if ($end === null) {
            http_response_code(400);
            echo json_encode(['message' => "Missing 'end' parameter."]);
            return;
        }

        $points = $this->route->getRouteByRange($this->db, $this->dbTable, $start, $end);
        $this->toGeoJson($points ?: []);
    }
}
