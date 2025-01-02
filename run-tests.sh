#!/bin/bash

if ! docker info > /dev/null 2>&1; then
  echo "You do not have permission to run Docker commands. Please add your user to the Docker group or run this script with sudo."
  exit 1
fi

set -e

echo "Starting MySQL container..."
docker-compose up -d

echo "Waiting for MySQL to be ready..."
until docker exec mysql_test_db mysqladmin ping -h localhost --silent; do
  sleep 1
done

echo "Setting up the database..."
docker exec -i mysql_test_db mysql -u test_user -ptest_password test_db <<EOF
CREATE TABLE IF NOT EXISTS test_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    datatime DATETIME NOT NULL,
    lat FLOAT NOT NULL,
    lon FLOAT NOT NULL
);
TRUNCATE TABLE test_table;
INSERT INTO test_table (datatime, lat, lon) VALUES
('2023-03-01 00:00:00', 11.123, 21.456),
('2024-01-06 03:22:19', 10.123, 20.456),
('2024-02-15 12:00:00', 11.123, 21.456),
('2025-04-11 00:00:00', 10.123, 20.456);
EOF

echo "Running PHPUnit tests..."
vendor/bin/phpunit

echo "Stopping MySQL container..."
docker-compose down