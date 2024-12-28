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

    public function handleGetRequest() {

        $this->handlePreflightRequest();
        $this->validateApiKey();

        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            $years = json_encode($this->route->getYears($this->db, $this->dbTable));

                    if ($years) {
                        echo $years;
                    } else {
                        http_response_code(400);
                        echo json_encode(["message" => "No data found for years."]);
                        return;
                    }

                    return;
            }
        
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Unsupported request method."]);
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
