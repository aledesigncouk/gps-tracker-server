<?php

namespace Alex\GpsTrackerServer\actions;

require_once '../../vendor/autoload.php';

use Alex\GpsTrackerServer\classes\Database;
use Alex\GpsTrackerServer\classes\Route;
use Alex\GpsTrackerServer\config\Config;

class ReadRouteAPI
{
    private $db;
    private $dbTable;
    private $route;
    private $apiKey;

    public function __construct($database, $route, $apiKey)
    {
        $this->db = $database->getConnection();
        $this->dbTable = $database->getTableName();
        $this->route = $route;
        $this->apiKey = $apiKey;

        $this->setHeaders();
    }

    // extract common code *********
    private function setHeaders()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY");
    }

    private function handlePreflightRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY");
            header("Access-Control-Max-Age: 86400");
            http_response_code(204);
            exit;
        }
    }

    private function validateApiKey()
    {
        $providedApiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

        if ($providedApiKey !== $this->apiKey) {
            http_response_code(403);
            echo json_encode(["message" => "Unauthorized. Invalid API Key."]);
            exit;
        }
    }
    // *** extract common code ****

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

    public function handleGetRequest()
    {

        $this->handlePreflightRequest();
        $this->validateApiKey();

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

                    $points = $this->route->getRouteByRange($this->db, $this->dbTable, $start, $end);

                    if ($points) {
                        $this->toGeoJson($points, 0000);
                    } else {
                        http_response_code(400);
                        echo json_encode(["message" => "No data found for the specified range."]);
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
$apiKey = Config::get('API_KEY');

$routeApi = new ReadRouteApi($database, $route, $apiKey);
$routeApi->handleGetRequest();
