<?php

$env = parse_ini_file(__DIR__ . '/.env');

$host = $env('DB_HOST');
$dbname = $env('DB_NAME');
$username = $env('DB_USERNAME');
$password = $env('DB_PASSWORD');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
            $fileTmpPath = $_FILES['csv_file']['tmp_name'];
            $fileName = $_FILES['csv_file']['name'];
            $fileSize = $_FILES['csv_file']['size'];
            $fileType = $_FILES['csv_file']['type'];

            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $allowedExtensions = array('csv');

            if (in_array($fileExtension, $allowedExtensions)) {
                if (($handle = fopen($fileTmpPath, "r")) !== false) {
                    $stmt = $pdo->prepare("INSERT INTO points (datatime, lat, lon) VALUES (:dt, :lat, :lng)");

                    // Read each line of the CSV file
                    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                        // Ensure the row has at least three fields
                        if (count($data) >= 3) {
                            // Extract the first three fields: dt, lat, lng
                            $dt = date('Y-m-d H:i:s', strtotime($data[0])); // Convert to MySQL DATETIME format
                            $lat = (float)$data[1];
                            $lng = (float)$data[2];

                            // Bind the parameters and execute the statement
                            $stmt->bindParam(':dt', $dt);
                            $stmt->bindParam(':lat', $lat);
                            $stmt->bindParam(':lng', $lng);
                            $stmt->execute();
                        }
                    }
                    // Close the file
                    fclose($handle);
                    echo "Data has been successfully imported!";
                } else {
                    echo "Error opening the file.";
                }
            } else {
                echo "Invalid file extension. Only CSV files are allowed.";
            }
        } else {
            echo "Error uploading the file.";
        }
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>

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
