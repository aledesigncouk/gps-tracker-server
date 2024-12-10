<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY");

include_once '../../config/config.php';
include_once '../../config/api.php';
include_once '../class/database.php';
include_once '../class/route.php';

$providedApiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY");
    header("Access-Control-Max-Age: 86400"); // Cache preflight for 24 hours
    http_response_code(204);
    exit;
}

if ($providedApiKey !== $apikey) {
    http_response_code(403); // Forbidden
    echo json_encode(["message" => "Unauthorized. Invalid API Key."]);
    exit;
}

$database = new Database();
$route = new Route();

$db = $database->getConnection();
$dbtable = $database->getTableName();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // missing all parameters => error
    if (!isset($_GET['start']) && !isset($_GET['end'])) {
        http_response_code(400);
        echo json_encode(["message" => "Missing 'start' and 'end' parameter."]);
        exit;
    }

    // missing end parameter => get by year
    if (!isset($_GET['end']) && isset($_GET['start'])) {
        $year = $_GET['start'] ?? null;

        if (!$year || !is_numeric($year)) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid or missing 'year' parameter."]);
            exit;
        }
    
        $points = $route->getRouteByYear($db, $dbtable, $year);
    
        if ($points) {
    
            echo toGeoJson($points, $year);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "No data found for the specified year."]);
        }
    }

    // start and end provided => get by range
    if (isset($_GET['start']) && isset($_GET['end'])) {

        $year = $start =$_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;

        if (!$start || !is_numeric($start) || !$end || !is_numeric($end)) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid or missing 'start' or 'end' parameter."]);
            exit;
        }
    
        $points = $route->getRouteByRange($db, $dbtable, $start, $end);
    
        if ($points) {
    
            echo toGeoJson($points, $year); // check about range
        } else {
            http_response_code(400);
            echo json_encode(["message" => "No data found for the specified range."]);
        }
        
    }

} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Unsupported request method."]);
}


function toGeoJson($points, $year) {
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

    $outputJson = json_encode($geoJson, JSON_PRETTY_PRINT);

    echo $outputJson;
}
