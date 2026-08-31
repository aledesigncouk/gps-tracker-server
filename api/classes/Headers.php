<?php

namespace Alex\GpsTrackerServer\classes;

use Alex\GpsTrackerServer\config\Config;

class Headers
{
    public function setHeaders()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY");
    }

    public function handlePreflightRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY");
            header("Access-Control-Max-Age: 86400");
            http_response_code(204);
            exit;
        }
    }

    public function validateApiKey()
    {
        $apiKey = Config::get('API_KEY');
        $providedApiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

        if ($providedApiKey !== $apiKey) {
            http_response_code(403);
            echo json_encode(["message" => "Unauthorized. Invalid API Key."]);
            exit;
        }
    }
}