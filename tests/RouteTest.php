<?php

namespace Alex\GpsTrackerServer\tests;

use PHPUnit\Framework\TestCase;
use Alex\GpsTrackerServer\classes\Route;

use PDO;

class RouteTest extends TestCase {
    public function testGetRouteByYear() {

        $mockConn = new PDO('mysql:host=127.0.0.1;dbname=test_db', 'test_user', 'test_password', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $route = new Route();
        $result = $route->getRouteByYear($mockConn, 'test_table', 2024);

        $this->assertCount(2, $result);
        $this->assertEquals('2024-01-06 03:22:19', $result[0]['datatime']);
        $this->assertEquals('2024-02-15 12:00:00', $result[1]['datatime']);

        $this->assertEquals('2024-01-06 03:22:19', $result[0]['datatime']);
        $this->assertEquals(10.123, $result[0]['lat']);
        $this->assertEquals(20.456, $result[0]['lon']);

        $this->assertEquals('2024-02-15 12:00:00', $result[1]['datatime']);
        $this->assertEquals(11.123, $result[1]['lat']);
        $this->assertEquals(21.456, $result[1]['lon']);

        $this->assertNotContains(['2023-03-01 00:00:00', '2025-04-11 00:00:00'], $result);
        
    }

    public function testGetRouteByRange() {

        $mockConn = new PDO('mysql:host=127.0.0.1;dbname=test_db', 'test_user', 'test_password', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $route = new Route();
        $result = $route->getRouteByRange($mockConn, 'test_table', '2024-01-01 00:00:00', '2024-05-30 00:00:00');

        $this->assertCount(2, $result);

        $this->assertEquals('2024-01-06 03:22:19', $result[0]['datatime']);
        $this->assertEquals(10.123, $result[0]['lat']);
        $this->assertEquals(20.456, $result[0]['lon']);

        $this->assertEquals('2024-02-15 12:00:00', $result[1]['datatime']);
        $this->assertEquals(11.123, $result[1]['lat']);
        $this->assertEquals(21.456, $result[1]['lon']);

        $this->assertNotContains(['2023-03-01 00:00:00', '2025-04-11 00:00:00'], $result);
    }

    public function testAddPoint() {

        $mockConn = new PDO('mysql:host=127.0.0.1;dbname=test_db', 'test_user', 'test_password', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $route = new Route();
        $id = $route->addPoint($mockConn, 'test_table', '2026-08-22 14:30:00', 51.5074, -0.1278);

        $this->assertGreaterThan(0, $id);

        $stmt = $mockConn->prepare("SELECT * FROM test_table WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals('2026-08-22 14:30:00', $result['datatime']);
        $this->assertEquals(51.5074, $result['lat']);
        $this->assertEquals(-0.1278, $result['lon']);
    }
}
