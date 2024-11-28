<?php
// set dynamic table name
class Route {

    public $points = [];

    public function getRouteByRange(PDO $conn, $dateFrom, $dateTo) {

        $stmt = $conn->prepare("SELECT * FROM points WHERE created >= dateFrom AND created < dateTo");
        $stmt->bindValue(":dateFrom", $dateFrom);
        $stmt->bindValue(":dateTo", $$dateTo);
        
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function getRouteByYear(PDO $conn, $year) {

        $stmt = $conn->prepare("SELECT * FROM points WHERE YEAR(datatime) = :year");
        $stmt->bindValue(":year", $year); // PDO::PARAM_INT

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

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