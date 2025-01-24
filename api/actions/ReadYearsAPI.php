<?php
namespace Alex\GpsTrackerServer\actions;

require_once '../../vendor/autoload.php';

use Alex\GpsTrackerServer\classes\Database;
use Alex\GpsTrackerServer\classes\Route;
use Alex\GpsTrackerServer\classes\Headers;

class ReadYearsAPI
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

    public function handleGetRequest($headers)
    {

        $headers->handlePreflightRequest();
        $headers->validateApiKey();

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

$database = new Database();
$route = new Route();
$headers = new Headers();

$routeYears = new ReadYearsApi($database, $route);
$routeYears->handleGetRequest($headers);