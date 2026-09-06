<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Alex\GpsTrackerServer\classes\Database;
use Alex\GpsTrackerServer\classes\Route;
use Alex\GpsTrackerServer\classes\Headers;
use Alex\GpsTrackerServer\actions\ReadRouteAPI;
use Alex\GpsTrackerServer\actions\ReadYearsAPI;
use Alex\GpsTrackerServer\actions\AddPointAPI;
use Alex\GpsTrackerServer\actions\ReadLatestAPI;
use Alex\GpsTrackerServer\actions\Upload;

$headers = new Headers();
$headers->handlePreflightRequest();

$path    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segment = basename(rtrim($path, '/'));

$database = new Database();
$route    = new Route();

switch ($segment) {
    case 'track':
        $headers->setHeaders();
        $headers->validateApiKey();
        (new ReadRouteAPI($database, $route))->handleGetRequest();
        break;

    case 'years':
        $headers->setHeaders();
        $headers->validateApiKey();
        (new ReadYearsAPI($database, $route))->handleGetRequest();
        break;

    case 'point':
        $headers->setHeaders();
        $headers->validateApiKey();
        (new AddPointAPI($database, $route))->handlePostRequest();
        break;

    case 'latest':
        $headers->setHeaders();
        $headers->validateApiKey();
        (new ReadLatestAPI($database, $route))->handleGetRequest();
        break;

    case 'upload':
        (new Upload($database, $route))->handle();
        break;

    case 'test':
        $headers->setHeaders();
        echo json_encode(['message' => 'Hello, Nautilus!']);
        break;

    default:
        $headers->setHeaders();
        http_response_code(404);
        echo json_encode(['message' => 'Endpoint not found.']);
}
