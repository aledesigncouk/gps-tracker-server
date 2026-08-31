
# Nautilus Tracker - Backend

The Nautilus Tracker Backend API is a server-side solution designed to provide easy access to GPS tracking data stored in a MySQL database. Built using PHP, it exposes endpoints to retrieve GPS data, query available years, and upload new data via CSV.

## Endpoints

All endpoints live under `/api/`.

### `GET /api/test`

Health-check endpoint. No authentication required.

**Response:**
```json
{ "message": "Hello, Nautilus!" }
```

---

### `GET /api/track`

Returns GPS track data for a given date range as a GeoJSON Feature. Requires an API key.

**Query parameters:**

| Parameter | Required | Description |
|-----------|----------|-------------|
| `start` | yes | Start datetime (`YYYY-MM-DD HH:MM:SS`) |
| `end` | yes | End datetime (`YYYY-MM-DD HH:MM:SS`) |

**Example request:**
```
GET /api/track?start=2023-09-01+00:00:00&end=2023-09-30+23:59:59
```

**Example response:**
```json
{
    "type": "Feature",
    "properties": {},
    "geometry": {
        "type": "LineString",
        "coordinates": [
            [52.666, -2.474],
            [52.667, -2.475]
        ]
    }
}
```

---

### `GET /api/years`

Returns a list of all years present in the database. Requires an API key.

**Example response:**
```json
{ "years": [2022, 2023, 2024] }
```

---

### `GET /api/point`

Records a single GPS point. Requires an API key.

**Query parameters:**

| Parameter | Required | Description |
|-----------|----------|-------------|
| `lat` | yes | Latitude, between -90 and 90 |
| `lon` | yes | Longitude, between -180 and 180 |
| `timestamp` | yes | ISO 8601 datetime (e.g. `2026-08-22T14:30:00Z`) |

**Example request:**
```
GET /api/point?lat=51.5074&lon=-0.1278&timestamp=2026-08-22T14:30:00Z
```

**Example response:**
```json
{
    "id": 123,
    "datatime": "2026-08-22 14:30:00",
    "lat": 51.5074,
    "lon": -0.1278
}
```

---

### `POST /api/upload`

Uploads a CSV file and stores its rows in the database. A browser form is served on `GET`.

**Request:** `multipart/form-data` with a `csv_file` field containing a `.csv` file.

**CSV format:** each row must have at least three fields — datetime, latitude, longitude:

```
DD/MM/YYYY HH:MM:SS, latitude, longitude
01/09/2023 00:35:55, 52.66646, -2.4749416
```

---

## Authentication

The `track`, `years`, and `point` endpoints require an `API_KEY` header. The key is configured via the `API_KEY` environment variable (see Configuration below).

---

## Installation

### Prerequisites

- PHP 8 or higher
- Composer 2.8 or higher
- Docker 28 or higher

### Local setup

```bash
composer install
docker-compose up -d
```

---

## Configuration & Deployment

The `.env` file is **generated automatically by the GitHub Actions deployment workflow** on every push to `master`. It is not committed to the repository — the local `.env` is a sample only.

The workflow reads the following **GitHub Actions secrets** (Settings → Secrets and variables → Actions) and writes them into a fresh `.env` before uploading to the remote server via FTP:

| Secret name | `.env` key | Purpose |
|-------------|------------|---------|
| `DB_HOST` | `DB_HOST` | Database server IP / hostname |
| `DB_USERNAME` | `DB_USERNAME` | Database username |
| `DB_PASSWORD` | `DB_PASSWORD` | Database password |
| `DB_NAME` | `DB_NAME` | Database name |
| `DB_TABLE` | `DB_TABLE` | Table name |
| `API_KEY` | `API_KEY` | API authentication key |
| `FTP_SERVER_DEV` | *(FTP host)* | Remote server for deployment |
| `FTP_USER` | *(FTP user)* | FTP username |
| `FTP_PASSWORD` | *(FTP password)* | FTP password |
| `FTP_FOLDER` | *(FTP remote path)* | Remote directory to deploy into |

> **Important:** any manual edits to the `.env` on the remote server will be overwritten on the next push to `master`. To make a permanent change, update the corresponding GitHub secret and push.
