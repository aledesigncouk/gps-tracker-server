<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY");

include_once '../config/database.php';
include_once '../config/config.php';
include_once '../class/route.php';

$providedApiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

if ($providedApiKey !== $apiKey) {
    http_response_code(403); // Forbidden
    echo json_encode(["message" => "Unauthorized. Invalid API Key."]);
    exit;
}

$database = new Database();
$route = new Route();

$db = $database->getConnection();
$dbtable = $database->getTable();

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$year = $_GET['year'] ?? null;

if ($method === 'GET') {
    if (!$year || !is_numeric($year)) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid or missing 'year' parameter."]);
        exit;
    }

    $track = $route->getRouteByYear($db, $dbtable, $year);
    if ($track) {
        echo json_encode($track);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "No data found for the specified year."]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Unsupported request method."]);
}

function setRequest() {}

// curl -X GET https://www.yoroxid.com/nautilus/api/actions/read.php?year=2023
