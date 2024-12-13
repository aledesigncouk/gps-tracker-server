<?php

class Route {

    public function getRouteByYear(PDO $conn, $dbtable, $year) {

        $stmt = $conn->prepare("SELECT * FROM `" .$dbtable. "` WHERE YEAR(datatime) = :year");
        $stmt->bindValue(":year", $year); // to check the best data format for the database

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // check to use loop

        return $result;
    }

    public function getRouteByRange(PDO $conn, $dbtable, $start, $end) {

        $dateFrom = DateTime::createFromFormat('Y-d-m', $start)->format('Y-m-d');
        $dateTo = DateTime::createFromFormat('Y-d-m', $end)->format('Y-m-d');

        $stmt = $conn->prepare("SELECT * FROM `" .$dbtable. "` WHERE datatime >= :dateFrom AND datatime < :dateTo");
        $stmt->bindValue(":dateFrom", $dateFrom);
        $stmt->bindValue(":dateTo", $dateTo);
        
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function setRoute(PDO $conn, $dbtable, $handle) {

        $stmt = $conn->prepare("INSERT INTO `" .$dbtable. "` (datatime, lat, lon) VALUES (:dt, :lat, :lng)");
   
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            if (count($data) < 3) {
                continue; // Skip incomplete rows
            }
        
            $dt = date('Y-m-d H:i:s', strtotime($data[0]));
            $lat = (float)$data[1];
            $lng = (float)$data[2];
        
            $stmt->bindParam(':dt', $dt, PDO::PARAM_STR);
            $stmt->bindParam(':lat', $lat); // to check the best data format for the database
            $stmt->bindParam(':lng', $lng);
            $stmt->execute();
        }   
    }
}