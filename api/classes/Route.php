<?php

namespace Alex\GpsTrackerServer\classes;

use PDO;
use DateTime;

class Route {

    public function getRouteByYear(PDO $conn, $dbtable, $year) {

        $stmt = $conn->prepare("SELECT * FROM `" .$dbtable. "` WHERE YEAR(datatime) = :year");
        $stmt->bindValue(":year", $year, PDO::PARAM_INT); // to check the best data format for the database

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // check to use loop

        return $result;
    }

    public function getRouteByRange(PDO $conn, $dbtable, $start, $end) {

        $stmt = $conn->prepare("SELECT * FROM `" .$dbtable. "` WHERE datatime >= :dateFrom AND datatime < :dateTo");
        $stmt->bindValue(":dateFrom", $start);
        $stmt->bindValue(":dateTo", $end);
        
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function setRoute(PDO $conn, $dbtable, $handle) {

        $stmt = $conn->prepare("INSERT INTO `" .$dbtable. "` (datatime, lat, lon) VALUES (:dt, :lat, :lng)");
   
        while (($data = fgetcsv($handle, 0, ",")) !== false) {
            if (count($data) < 3) {
                continue;
            }

            $datetime = DateTime::createFromFormat('d/m/Y H:i:s', $data[0]);
            if (!$datetime) {
                continue; // Skip header row and any rows with unrecognised date format
            }

            $dt  = $datetime->format('Y-m-d H:i:s');
            $lat = (float)$data[1];
            $lng = (float)$data[2];

            $stmt->bindParam(':dt', $dt, PDO::PARAM_STR);
            $stmt->bindParam(':lat', $lat);
            $stmt->bindParam(':lng', $lng);
            $stmt->execute();
        }
    }

    public function addPoint(PDO $conn, $dbtable, $datatime, $lat, $lon) {

        $stmt = $conn->prepare("INSERT INTO `" .$dbtable. "` (datatime, lat, lon) VALUES (:dt, :lat, :lng)");

        $stmt->bindParam(':dt', $datatime, PDO::PARAM_STR);
        $stmt->bindParam(':lat', $lat);
        $stmt->bindParam(':lng', $lon);
        $stmt->execute();

        return (int) $conn->lastInsertId();
    }

    public function getLatestPoint(PDO $conn, $dbtable) {
        $stmt = $conn->prepare("SELECT * FROM `" . $dbtable . "` ORDER BY datatime DESC LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function getYears(PDO $conn, $dbtable) {
        $stmt = $conn->prepare("SELECT DISTINCT YEAR(STR_TO_DATE(datatime, '%Y-%m-%d %H:%i:%s')) AS year FROM `" . $dbtable . "`");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $years = array_map(function($row) {
            return $row['year'];
        }, $result);

        return $years;
    }

}