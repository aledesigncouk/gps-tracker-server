<?php

namespace Alex\GpsTrackerServer\actions;

require_once '../../vendor/autoload.php';

use Alex\GpsTrackerServer\classes\Database;
use Alex\GpsTrackerServer\classes\Route;
use Alex\GpsTrackerServer\classes\Headers;

class ReadRouteAPI
{
    private $db;
    private $dbTable;
    private $route;

    public function __construct($database, $route)
    {
        $this->db = $database->getConnection();
        $this->dbTable = $database->getTableName();
        $this->route = $route;
        
    }

    private function toGeoJson($points, $year)
    {

        usort($points, function ($a, $b) {
            return strtotime($a['datatime']) - strtotime($b['datatime']);
        });

        $coordinates = array_map(function ($item) {
            return [floatval($item['lat']), floatval($item['lon'])];
        }, $points);

        $geoJson = [
            'type' => 'Feature',
            'properties' => [
                'year' => $year
            ],
            'geometry' => [
                'coordinates' => $coordinates,
                'type' => 'LineString'
            ]
        ];

        echo json_encode($geoJson);
    }

    public function handleGetRequest($headers)
    {

        $headers->handlePreflightRequest();
        $headers->validateApiKey();

        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {

            $start = $_GET['start'] ?? null;
            $end = $_GET['end'] ?? null;

            switch (true) {
                case !$start && !$end:
                    http_response_code(400);
                    echo json_encode(["message" => "Missing 'start' and 'end' parameter."]);
                    return;

                case !$start && $end:
                    http_response_code(400);
                    echo json_encode(["message" => "Missing 'start' parameter."]);
                    return;

                case $start && $end:
                    if (empty($start) || empty($end) || !is_string($start) || !is_string($end)) {
                        http_response_code(400);
                        echo json_encode(["message" => "Invalid or missing 'start' or 'end' parameter."]);
                        return;
                    }

                    // improve the return of an empty array
                    $points = $this->route->getRouteByRange($this->db, $this->dbTable, $start, $end);

                    if ($points) {
                        $this->toGeoJson($points, 0000);
                        
                    } else {
                        $this->toGeoJson([], 0000);
                        // http_response_code(200);
                        // echo json_encode(["message" => "No data found for the specified range."]);
                    }

                    return;

                default:
                    http_response_code(400);
                    echo json_encode(["message" => "Invalid request parameters."]);
                    return;
            }
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Unsupported request method."]);
        }
    }
}

$database = new Database();
$route = new Route();
$headers = new Headers();

$routeApi = new ReadRouteApi($database, $route);
$routeApi->handleGetRequest($headers);
