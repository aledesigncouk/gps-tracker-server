<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/Database.php';
include_once '../class/Items.php';

$database = new Database();
$db = $database->getConnection();

$items = new Items($db);

// keep it for uploading option
// $data = json_decode(file_get_contents("php://input"));

$data = parse_url(basename($_SERVER['REQUEST_URI']));
parse_str($data['query'], $params);

if (
    !empty($params['lat']) &&
    !empty($params['lon'])
) {
    
    $items->datatime = $params['datatime'];
    // $items->datatime = date('Y-m-d H:i:s');
    $items->lat = $params['lat']; // DB: decimal(8,6)
    $items->lon = $params['lon']; // DB: decimal(9,6)


    if ($items->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "Point was created."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to create point."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create point. Data is incomplete."));
}

// errors should be logged and with more information
