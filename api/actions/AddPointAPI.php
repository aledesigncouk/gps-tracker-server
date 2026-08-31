<?php

namespace Alex\GpsTrackerServer\actions;

use Alex\GpsTrackerServer\classes\Database;
use Alex\GpsTrackerServer\classes\Route;
use DateTime;
use DateTimeZone;
use Exception;

class AddPointAPI
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

        $lat       = $_GET['lat'] ?? null;
        $lon       = $_GET['lon'] ?? null;
        $timestamp = $_GET['timestamp'] ?? null;

        $missing = array_keys(array_filter([
            'lat'       => $lat,
            'lon'       => $lon,
            'timestamp' => $timestamp,
        ], function ($v) { return $v === null; }));

        if (!empty($missing)) {
            http_response_code(400);
            echo json_encode(['message' => "Missing required parameter(s): " . implode(', ', $missing) . '.']);
            return;
        }

        if (!is_numeric($lat) || $lat < -90 || $lat > 90) {
            http_response_code(400);
            echo json_encode(['message' => "Invalid 'lat' parameter. Must be a number between -90 and 90."]);
            return;
        }

        if (!is_numeric($lon) || $lon < -180 || $lon > 180) {
            http_response_code(400);
            echo json_encode(['message' => "Invalid 'lon' parameter. Must be a number between -180 and 180."]);
            return;
        }

        $datetime = $this->parseTimestamp($timestamp);
        if ($datetime === null) {
            http_response_code(400);
            echo json_encode(['message' => "Invalid 'timestamp' parameter. Must be a valid ISO 8601 datetime (e.g. 2026-08-22T14:30:00Z)."]);
            return;
        }

        $dt = $datetime->format('Y-m-d H:i:s');
        $id = $this->route->addPoint($this->db, $this->dbTable, $dt, (float) $lat, (float) $lon);

        http_response_code(201);
        echo json_encode([
            'id'       => $id,
            'datatime' => $dt,
            'lat'      => (float) $lat,
            'lon'      => (float) $lon,
        ]);
    }

    private function parseTimestamp($timestamp)
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?(Z|[+-]\d{2}:\d{2})$/', $timestamp)) {
            return null;
        }

        try {
            $datetime = new DateTime($timestamp);
        } catch (Exception $e) {
            return null;
        }

        $datetime->setTimezone(new DateTimeZone('UTC'));
        return $datetime;
    }
}
