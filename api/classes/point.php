<?php
// validate data, lat, lon
// table name needed apart connection


class Point {
    // private $id;
    private $datatime;
    private $lat;
    private $lon;

    public function __construct($datatime, $lat, $lon) {
        $this->datatime = $datatime;
        $this->lat = $lat;
        $this->lon = $lon;
    }

    // get point with datatime, lat, lon
    public function getPoint() {
        return array(
            "datatime" => $this->datatime,
            "lat" => $this->lat,
            "lon" => $this->lon
        );
    }

    // get datastamp only
    public function getDatetime() {
        return $this->datatime;
    }

}
?>