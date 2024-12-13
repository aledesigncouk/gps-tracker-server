<?php 

class Years {

    public function getYears(PDO $conn, $dbtable) {
        $stmt = $conn->prepare("SELECT DISTINCT YEAR(STR_TO_DATE(datatime, '%Y-%m-%d %H:%i:%s')) AS year FROM `" . $dbtable . "`");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

}