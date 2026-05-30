<?php

namespace Alex\GpsTrackerServer\actions;

use Alex\GpsTrackerServer\classes\Database;
use Alex\GpsTrackerServer\classes\Route;

class Upload
{
    private $conn;
    private $dbTable;
    private $route;

    public function __construct($database, $route)
    {
        $this->conn    = $database->getConnection();
        $this->dbTable = $database->getTableName();
        $this->route   = $route;
    }

    public function handle()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->renderForm();
            return;
        }

        if (empty($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $this->renderForm('Error uploading the file.');
            return;
        }

        $fileName = $_FILES['csv_file']['name'];

        if (strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) !== 'csv') {
            $this->renderForm('Invalid file extension. Only CSV files are allowed.');
            return;
        }

        $handle = fopen($_FILES['csv_file']['tmp_name'], 'r');
        if ($handle === false) {
            $this->renderForm('Error opening the file.');
            return;
        }

        $this->route->setRoute($this->conn, $this->dbTable, $handle);
        fclose($handle);

        $this->renderForm('File uploaded and stored successfully.');
    }

    private function renderForm($message = '')
    {
        $escaped = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Upload CSV File</title>
        </head>
        <body>
            {$escaped}
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
}
