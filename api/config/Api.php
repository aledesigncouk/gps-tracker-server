<?php

namespace Alex\GpsTrackerServer\config;

$env = parse_ini_file(__DIR__ . '/../../.env');

$apiKey = $env['API_KEY'];
