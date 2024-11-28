<?php

$env = parse_ini_file(__DIR__ . '/../../.env');

$dbHost = $env['DB_HOST'];
$dbName = $env['DB_NAME'];
$dbUsername = $env['DB_USERNAME'];
$dbPassword = $env['DB_PASSWORD'];
$dbTableName = $env['DB_TABLE_NAME'];