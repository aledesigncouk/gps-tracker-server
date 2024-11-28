<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once '../config/database.php';
include_once '../class/route.php';

$database = new Database();
$route = new Route();

$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$resource = array_shift($request); // e.g., 'users', 'posts', etc.

// curl -X GET https://www.yoroxid.com/nautilus/api/actions/read.php/2023
switch ($method) {
    case 'GET':
        if($resource) {
            $track = $route->getRouteByYear($db, $resource);
        
            echo json_encode($track);
        }
        break;
}


