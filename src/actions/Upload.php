<?php

namespace Alex\GpsTrackerServer\actions;

use Alex\GpsTrackerServer\classes\Database;
use Alex\GpsTrackerServer\classes\Route;

$route = new Route();
$database = new Database();

$conn = $database->getConnection();

if(!$conn) {
    die("Failed to connect.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    renderForm("Please upload a CSV file.");
    exit;
}

$fileTmpPath = $_FILES['csv_file']['tmp_name'];
$fileName = $_FILES['csv_file']['name'];
$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$allowedExtensions = ['csv'];

if (!in_array($fileExtension, $allowedExtensions)) {
    renderForm("Invalid file extension. Only CSV files are allowed.");
    exit;
}

if (($handle = fopen($fileTmpPath, "r")) === false) {
    renderForm("Error opening the file.");
    exit;
}

$dbtable = $database->getTableName();

$route->setRoute($conn, $dbtable, $handle);

fclose($handle);
$conn = null;

if (empty($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    renderForm("Error uploading the file.");
    exit;
}

// check to close connection after the file is uploaded
// clear form after successful upload
// more field for other data (may require to add an associated table)

function renderForm($message = '') {
    echo <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Upload CSV File</title>
        </head>
        <body>
            <form action="" method="post" enctype="multipart/form-data">
                <label for="csv_file">Upload CSV file:</label>
                <input type="file" name="csv_file" id="csv_file" accept=".csv">
                <br><br>
                <input type="submit" value="Upload and Store">
            </form>
        </body>
        </html>
    HTML;
}

