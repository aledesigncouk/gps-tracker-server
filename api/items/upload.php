<?php
include_once '../config/Database.php';
include_once '../class/Items.php';

try {
   
    $database = new Database();
    $db = $database->getConnection();

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
                    
                    $stmt = $db->prepare("INSERT INTO points (datatime, lat, lon) VALUES (:dt, :lat, :lng)");

                    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                     
                        if (count($data) >= 3) {
                           
                            $dt = date('Y-m-d H:i:s', strtotime($data[0])); // Convert to MySQL DATETIME format
                            $lat = (float)$data[1];
                            $lng = (float)$data[2];

                            $stmt->bindParam(':dt', $dt);
                            $stmt->bindParam(':lat', $lat);
                            $stmt->bindParam(':lng', $lng);
                            $stmt->execute();
                        }
                    }
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
}
?>