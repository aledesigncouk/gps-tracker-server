<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/Database.php';
include_once '../class/Route.php';

$database = new Database();
$route = new Route();

$db = $database->getConnection();

$track = $route->getRouteByYear($db, 2023);

echo json_encode($track);