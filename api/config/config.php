<?php

$env = parse_ini_file(__DIR__ . '/../../.env');

$host = $env['DB_HOST'];
$dbname = $env['DB_NAME'];
$username = $env['DB_USERNAME'];
$password = $env['DB_PASSWORD'];
$dbtable = $env['DB_TABLE'];
// add the table name/names