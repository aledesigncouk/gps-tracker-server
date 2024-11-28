<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY");

include_once '../config/database.php';
include_once '../class/route.php';

$providedApiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

if ($providedApiKey !== API_KEY) {
    http_response_code(403); // Forbidden
    echo json_encode(["message" => "Unauthorized. Invalid API Key."]);
    exit;
}else{
    echo 'key accepted';

    $database = new Database();
$route = new Route();

$db = $database->getConnection();
$dbtable = $database->getTable();

$method = $_SERVER['REQUEST_METHOD'];

$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$resource = array_shift($request); // e.g., 'users', 'posts', etc.

// curl -X GET https://www.yoroxid.com/nautilus/api/actions/read.php/2023
switch ($method) {
    case 'GET':
        if($resource) {
            $track = $route->getRouteByYear($db, $dbtable, $resource);
        
            echo json_encode($track);
        }
        break;
}
}




