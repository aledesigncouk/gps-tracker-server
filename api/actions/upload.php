<?php

include_once '../config/database.php';

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

// Process CSV file
if (($handle = fopen($fileTmpPath, "r")) === false) {
    renderForm("Error opening the file.");
    exit;
}

$stmt = $conn->prepare("INSERT INTO points (datatime, lat, lon) VALUES (:dt, :lat, :lng)");


while (($data = fgetcsv($handle, 1000, ",")) !== false) {
    if (count($data) < 3) {
        continue; // Skip incomplete rows
    }

    $dt = date('Y-m-d H:i:s', strtotime($data[0])); // Convert to MySQL DATETIME format
    $lat = (float)$data[1];
    $lng = (float)$data[2];

    $stmt->bindParam(':dt', $dt);
    $stmt->bindParam(':lat', $lat);
    $stmt->bindParam(':lng', $lng);
    $stmt->execute();
}

fclose($handle);
renderForm("Data has been successfully imported!");

if (empty($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    renderForm("Error uploading the file.");
    exit;
}

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

