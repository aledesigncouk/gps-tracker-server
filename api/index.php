<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Alex\GpsTrackerServer\classes\Database;
use Alex\GpsTrackerServer\classes\Route;
use Alex\GpsTrackerServer\classes\Headers;
use Alex\GpsTrackerServer\actions\ReadRouteAPI;
use Alex\GpsTrackerServer\actions\ReadYearsAPI;
use Alex\GpsTrackerServer\actions\Upload;

$headers = new Headers();
$headers->setHeaders();
$headers->handlePreflightRequest();

$path    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segment = basename(rtrim($path, '/'));

$database = new Database();
$route    = new Route();

switch ($segment) {
    case 'track':
        $headers->validateApiKey();
        (new ReadRouteAPI($database, $route))->handleGetRequest();
        break;

    case 'years':
        $headers->validateApiKey();
        (new ReadYearsAPI($database, $route))->handleGetRequest();
        break;

    case 'upload':
        (new Upload($database, $route))->handle();
        break;

    default:
        http_response_code(404);
        echo json_encode(['message' => 'Endpoint not found.']);
}
