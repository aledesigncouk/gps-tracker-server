<?php

class Route {

    public $points = [];

    public function getRoute($dateFrom, $dateTo) { //from the frontend request
        if ($this->id) {
            $stmt = $this->conn->prepare("SELECT * FROM " . $this->itemsTable . " WHERE id = ?"); // select by id
            $stmt->bind_param("i", $this->id);
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM " . $this->itemsTable . " WHERE created >=" . $dateFrom . " AND created <" . $dateTo);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    public function setRoute(PDO $conn, $handle) {


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
   
    }
}