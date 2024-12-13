<?php

class ReadRouteAPI {
    private $db;
    private $dbTable;
    private $route;
    private $apiKey;

    public function __construct($database, $route, $apiKey) {
        $this->db = $database->getConnection();
        $this->dbTable = $database->getTableName();
        $this->route = $route;
        $this->apiKey = $apiKey;

        $this->setHeaders();
    }

    private function setHeaders() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY");
    }

    private function handlePreflightRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY");
            header("Access-Control-Max-Age: 86400");
            http_response_code(204);
            exit;
        }
    }

    private function validateApiKey() {
        $providedApiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

        if ($providedApiKey !== $this->apiKey) {
            http_response_code(403);
            echo json_encode(["message" => "Unauthorized. Invalid API Key."]);
            exit;
        }
    }


    private function toGeoJson($points, $year) {

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

    public function handleGetRequest() {

        $this->handlePreflightRequest();
        $this->validateApiKey();

        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {

            $start = $_GET['start'] ?? null;
            $end = $_GET['end'] ?? null;
            $year = $_GET['year'] ?? null; 

            switch (true) {
                case !$start && !$end && !$year:
                    http_response_code(400);
                    echo json_encode(["message" => "Missing 'start' and 'end' parameter."]);
                    return;

                case !$start && !$year && $end:
                    http_response_code(400);
                    echo json_encode(["message" => "Missing 'start' parameter."]);
                    return;

                case !$end && !$year &&  $start:
                    if (!is_numeric($start)) {
                        http_response_code(400);
                        echo json_encode(["message" => "Invalid or missing 'year' parameter."]);
                        return;
                    }

                    $points = $this->route->getRouteByYear($this->db, $this->dbTable, $start);

                    if ($points) {
                        $this->toGeoJson($points, $start);
                    } else {
                        http_response_code(400);
                        echo json_encode(["message" => "No data found for the specified year."]);
                    }
                    return;

                case $start && $end && !$year:
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

                case $year && !$start && !$end:
                    if (!is_numeric($year)) {
                        http_response_code(400);
                        echo json_encode(["message" => "Invalid or missing 'year' parameter."]);
                        return;
                    }

                    if($year !== 1) {
                        http_response_code(400);
                        echo json_encode(["message" => "Invalid year parameter request."]);
                        return;
                    }

                    $years = json_encode($this->route->getYears($this->db, $this->dbTable));

                    if ($years) {
                        echo $years;
                    } else {
                        http_response_code(400);
                        echo json_encode(["message" => "No data found for years."]);
                        return;
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

include_once '../../config/config.php';
include_once '../../config/api.php';
include_once '../classes/database.php';
include_once '../classes/route.php';

$database = new Database();
$route = new Route();

$routeApi = new ReadRouteApi($database, $route, $apiKey);
$routeApi->handleGetRequest();
