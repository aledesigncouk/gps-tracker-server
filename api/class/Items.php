<?php
class Items {

    private $itemsTable = "points";
    public $id;
    public $datatime;
    public $created;
    public $lat;
    public $lon;
    private $conn;

    public function __construct($db) {$this->conn = $db;}

    // pick included or remove exluded for more complex queries ?
    public function read($dateFrom, $dateTo) { //from the frontend request
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

    public function create() {

        $stmt = $this->conn->prepare("
            INSERT INTO " . $this->itemsTable . "(`datatime`, `lat`, `lon`)
            VALUES(?,?,?)");

        $this->datatime = htmlspecialchars(strip_tags($this->datatime));
        $this->lat = $this->lat; // DB: decimal(8,6)
        $this->lon = $this->lon; // DB: decimal(9,6)

        // we are using string due an unresolved issue on adding record with decimal
        $stmt->bind_param("sss", $this->datatime, $this->lat, $this->lon);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
