<?php

namespace Alex\GpsTrackerServer\tests;

use PHPUnit\Framework\TestCase;
use Alex\GpsTrackerServer\classes\Database;

class DatabaseTest extends TestCase {
    public function testConnection() {
        $db = new Database();
        $conn = $db->getConnection();
        $this->assertNotNull($conn, "Database connection should not be null.");
    }

    public function testTableName() {
        $db = new Database();
        $tableName = $db->getTableName();
        $this->assertEquals('expected_table_name', $tableName, "The table name should match the expected value.");
    }
}
