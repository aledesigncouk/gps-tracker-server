
# Nautilus Tracker - Backend

The Nautilus Tracker Backend API is a server-side solution designed to provide easy access to GPS tracking data stored in a MySQL database. Built using PHP, this API offers two primary endpoints that allow clients to retrieve and interact with GPS data based on specific parData Retrieval by Date Rangeameters.

### Features:

- **Data Retrieval by Date Range**: The first endpoint allows users to retrieve GPS data within a specified date range. Clients can pass start and end dates as query parameters, and the server will query the MySQL database to fetch the relevant data. The data is returned in a structured format, consisting of GeoJSON objects, with each object containing a year property and coordinates represented in a LineString geometry. This makes it ideal for visualizing GPS tracking data on a map, showing movement over time.

**Example response**: 
```json
[
    {
        "type": "Feature",
        "properties": {
            "year": 2023
        },
        "geometry": {
            "coordinates": [[-122.4194, 37.7749], [-122.4194, 37.7750]],
            "type": "LineString"
        }
    }
]

```

- **Available Years Endpoint**: The second endpoint returns a list of all years that are represented in the database. This allows clients to dynamically filter and choose the specific time period they wish to query. The server retrieves this information by querying the year column in the database and returning a unique list of years.

### Installation:

Follow these steps to set up and run the Nautilus Tracker app on your local machine.

#### Prerequisites:

- PHP 8 or higher
- Composer 2.8 or higher
- Docker 28 or higher
 
#### Set Up Environment Variables


## Configuration & Deployment

The `.env` file is **generated automatically by the GitHub Actions deployment workflow** on every push to `master`. It is not committed to the repository — the local `.env` is a sample only.

The workflow reads the following **GitHub Actions secrets** (Settings → Secrets and variables → Actions) and writes them into a fresh `.env` before uploading to the remote server via FTP:

| Secret name | `.env` key written | Purpose |
|---|---|---|
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

> **Important:** any manual edits to the `.env` file on the remote server will be overwritten on the next push to `master`. To make a permanent change to credentials or config, update the corresponding GitHub secret and push to trigger a new deployment.


## CSV input format
CSV file rows must contain the first three fields formatted in the following way:
DD/MM/YYYY HH:MM:SS, latitude, longitude
01/09/2023 00:35:55,52.66646,-2.4749416, [ ... ]

