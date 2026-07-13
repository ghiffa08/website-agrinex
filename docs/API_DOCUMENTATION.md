# AgriNex Laravel API Documentation v2.0

**Last Updated:** October 16, 2025  
**Base URL:** `http://127.0.0.1:8000/api/v1`

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Architecture](#architecture)
3. [Authentication](#authentication)
4. [Data Ingestion API (IoT Devices)](#data-ingestion-api-iot-devices)
5. [Dashboard API](#dashboard-api)
6. [Legacy Endpoints](#legacy-endpoints)
7. [Error Handling](#error-handling)
8. [Field Mapping Reference](#field-mapping-reference)
9. [Testing Guide](#testing-guide)

---

## 🎯 Overview

AgriNex API adalah sistem backend untuk Smart Agriculture IoT yang menerima data dari Raspberry Pi dan menyediakan dashboard monitoring. API ini dibangun dengan Laravel 11.x dan MySQL.

### System Components

-   **IoT Devices**: Raspberry Pi mengirim data sensor via REST API
-   **Backend**: Laravel API menerima, validasi, dan simpan data
-   **Database**: MySQL dengan 7 tabel utama
-   **Frontend**: Dashboard dengan Chart.js untuk visualisasi

### Key Features

-   ✅ Real-time sensor data ingestion
-   ✅ Irrigation control & monitoring
-   ✅ 7-day historical data visualization
-   ✅ Node status tracking (13 nodes)
-   ✅ Weather station integration (Node 65)
-   ✅ Comprehensive error handling & validation

---

## 🏗️ Architecture

### Database Structure

```
agri_lara
├── node (13 nodes: 1-12 + 65 weather)
├── getdata_logs (sensor collection sessions)
├── sensor_node_data (Node 1-12 sensor readings)
├── sensor_weather_data (Node 65 weather data)
├── irrigate_logs (irrigation sessions)
├── valve_logs (valve operations)
├── node_logs (node activity & signal quality)
└── json_backup (full JSON request backup)
```

**JSON Backup Table:**
Setiap request dari Raspberry Pi **otomatis disimpan** ke tabel `json_backup` sebelum di-parse. Ini untuk:

-   Audit trail & debugging
-   Data recovery jika parsing error
-   Analisis raw data dari IoT devices
-   Monitoring data size & completeness

Field yang disimpan:

-   `json_data` - Full JSON request body
-   `data_size_kb` - Size request dalam KB
-   `total_records` - Total records dalam request
-   `node_completeness` - Persentase node yang aktif
-   `backup_timestamp` - Waktu backup

### API Layers

```
IoT Device → Controller (Validation) → Service (Business Logic) → Model (Database)
```

### Data Flow

```
Raspberry Pi → POST /api/v1/ingest/* → Laravel → MySQL
                                      ↓
Dashboard ← GET /api/v1/dashboard/* ← Laravel ← MySQL
```

---

## 🔐 Authentication

**Current Status:** API saat ini **tidak menggunakan authentication** untuk kemudahan development.

**Production Recommendation:**

-   Implementasikan Laravel Sanctum untuk token-based authentication
-   Gunakan API Key untuk IoT devices
-   Rate limiting untuk mencegah abuse

---

## 📡 Data Ingestion API (IoT Devices)

API untuk menerima data dari Raspberry Pi. Endpoint ini dipanggil oleh IoT devices untuk mengirim sensor data, irrigation logs, dan valve operations.

### Base Path: `/api/v1/ingest`

---

### 1. Health Check

**Endpoint:** `GET /api/v1/ingest/health`

**Description:** Cek status API dan list semua available endpoints.

**Request:**

```bash
curl -X GET http://127.0.0.1:8000/api/v1/ingest/health
```

**Response:** `200 OK`

```json
{
    "status": "ok",
    "message": "Data Ingestion API is running",
    "version": "2.0",
    "timestamp": "2025-10-16T10:30:00.000000Z",
    "endpoints": {
        "sensor_data": "POST /api/v1/ingest/sensor-data",
        "valve_on": "POST /api/v1/ingest/valve-on",
        "valve_off": "POST /api/v1/ingest/valve-off"
    }
}
```

---

### 2. POST Sensor Data (getdata)

**Endpoint:** `POST /api/v1/ingest/sensor-data`

**Description:** Menerima data sensor dari semua node (Node 1-12) dan weather station (Node 65).

**Request Body:**

```json
{
    "metadata": {
        "timestamp": "2025-10-15T10:21:46.990Z",
        "sesi_id_getdata": 68,
        "source": "Raspberry Pi - AgriNex IoT",
        "version": "2.0"
    },
    "data": {
        "getdata_logs": [
            {
                "id": 215,
                "sesi_id_getdata": 68,
                "waktu_mulai": "2025-10-15T17:21:11.000Z",
                "waktu_selesai": "2025-10-15T17:21:46.000Z",
                "node_sukses": 3,
                "node_gagal": 0
            }
        ],
        "sensor_weather_data": [
            {
                "id": 182,
                "sesi_id_getdata": 68,
                "node_id": 65,
                "voltage": 5.01,
                "current": 38.7,
                "power": 200,
                "light": 73.3,
                "rain": 0,
                "rain_adc": 4095,
                "wind": 0,
                "wind_pulse": 0,
                "humidity": 53,
                "temp_dht": 26.8,
                "ts_counter": 26841583,
                "received_at": "2025-10-15T10:21:14.000Z"
            }
        ],
        "sensor_node_data": [
            {
                "id": 343,
                "sesi_id_getdata": 68,
                "node_id": 1,
                "voltage_v": 3.43,
                "current_ma": 40.5,
                "power_mw": 140,
                "temp_c": 26.25,
                "soil_pct": 62,
                "soil_adc": 1852,
                "ts_counter": 26841819,
                "received_at": "2025-10-15T10:21:15.000Z"
            },
            {
                "id": 344,
                "sesi_id_getdata": 68,
                "node_id": 5,
                "voltage_v": 3.25,
                "current_ma": 48.1,
                "power_mw": 155,
                "temp_c": 26,
                "soil_pct": 60,
                "soil_adc": 1927,
                "ts_counter": 26842005,
                "received_at": "2025-10-15T10:21:24.000Z"
            }
        ],
        "node_logs": [
            {
                "id": 1513,
                "node_id": 65,
                "rssi_dbm": -68,
                "snr_db": 13.3,
                "signal_quality": "Excellent",
                "status": "Aktif",
                "waktu": "2025-10-15T17:21:14.000Z",
                "type_sesi": "getdata",
                "sesi_id": "68",
                "keterangan": null
            }
        ]
    },
    "statistics": {
        "total_records": 17,
        "records_by_table": {
            "getdata_logs": 1,
            "sensor_weather_data": 1,
            "sensor_node_data": 2,
            "node_logs": 13
        },
        "node_status": {
            "expected_nodes": 12,
            "received_nodes": 2,
            "active_nodes": 3,
            "inactive_nodes": 10,
            "completeness_percentage": "16.67%",
            "node_ids": [1, 5]
        }
    }
}
```

**Request Structure Explanation:**

1. **`metadata`** - Request metadata

    - `timestamp`: ISO 8601 timestamp dari Raspberry Pi
    - `sesi_id_getdata`: Session ID untuk tracking
    - `source`: Identifier device pengirim
    - `version`: API version

2. **`data`** - Actual sensor data (akan di-parse dan disimpan ke database)

    - `getdata_logs`: Session logs
    - `sensor_weather_data`: Weather station data (Node 65)
    - `sensor_node_data`: Node sensor readings (Node 1-12)
    - `node_logs`: Signal quality & node status

3. **`statistics`** - Pre-calculated statistics (untuk JSON backup)
    - `total_records`: Total records di request ini
    - `records_by_table`: Count per table
    - `node_status`: Node completeness info
    - **Important**: Statistics ini disimpan ke `json_backup` table

**Validation Rules:**

-   `metadata.sesi_id_getdata`: required, integer
-   `metadata.timestamp`: required, ISO 8601 format
-   `data.getdata_logs`: array (optional)
-   `data.sensor_weather_data`: array (optional)
-   `data.sensor_node_data`: array (optional)
-   `data.node_logs`: array (optional)

**Response:** `200 OK`

```json
{
    "success": true,
    "message": "Sensor data stored successfully",
    "data": {
        "sesi_id": 68,
        "inserted_records": {
            "getdata_logs": 1,
            "sensor_weather_data": 1,
            "sensor_node_data": 2,
            "node_logs": 13
        },
        "total_inserted": 17,
        "backup_info": {
            "json_backup_id": 215,
            "data_size_kb": 5.79,
            "backup_timestamp": "2025-10-16T10:30:00Z"
        }
    },
    "timestamp": "2025-10-16T10:30:00.000000Z"
}
```

**Key Features:**

-   ✅ **Automatic JSON Backup**: Request disimpan ke `json_backup` table
-   ✅ **Data Size Tracking**: Monitoring ukuran data yang dikirim
-   ✅ **Completeness Statistics**: Persentase node yang aktif
-   ✅ **Audit Trail**: Full request history untuk debugging

**Error Response:** `422 Unprocessable Entity`

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "metadata.sesi_id_getdata": ["The sesi_id_getdata field is required."]
    }
}
```

**Curl Example:**

```bash
curl -X POST http://127.0.0.1:8000/api/v1/ingest/sensor-data \
  -H "Content-Type: application/json" \
  -d @sensordata.json
```

---

### 3. POST Valve ON (Irrigation Start)

**Endpoint:** `POST /api/v1/ingest/valve-on`

**Description:** Menerima data ketika valve dibuka (irigasi dimulai). Menyimpan session irigasi dan status valve.

**Request Body:**

```json
{
    "metadata": {
        "timestamp": "2025-10-16T10:51:19.467Z",
        "sesi_id_irrigate": 999,
        "source": "Raspberry Pi - AgriNex IoT",
        "version": "2.0",
        "type": "irrigate"
    },
    "data": {
        "irrigate_logs": [
            {
                "id": 999,
                "sesi_id_irrigate": 999,
                "waktu_mulai": "2025-10-16T17:32:29.000Z",
                "waktu_akhir": null,
                "node_sukses": 0,
                "node_gagal": 0,
                "valve_on_akhir": 0
            }
        ],
        "valve_logs": [
            {
                "id": 999,
                "node_id": 1,
                "sesi_id_irrigate": 999,
                "durasi_detik": 144,
                "volume_air": null,
                "rata_rata": null,
                "pulse": null,
                "status": "ON",
                "waktu": "2025-10-16T17:32:29.000Z"
            },
            {
                "id": 1001,
                "node_id": 1,
                "sesi_id_irrigate": 999,
                "durasi_detik": 144,
                "volume_air": 0,
                "rata_rata": 0,
                "pulse": 0,
                "status": "OFF",
                "waktu": "2025-10-16T17:34:53.000Z"
            },
            {
                "id": 1000,
                "node_id": 5,
                "sesi_id_irrigate": 999,
                "durasi_detik": 180,
                "volume_air": null,
                "rata_rata": null,
                "pulse": null,
                "status": "ON",
                "waktu": "2025-10-16T17:32:29.000Z"
            },
            {
                "id": 1002,
                "node_id": 5,
                "sesi_id_irrigate": 999,
                "durasi_detik": 180,
                "volume_air": 16.204,
                "rata_rata": 5.4,
                "pulse": 7292,
                "status": "OFF",
                "waktu": "2025-10-16T17:35:29.000Z"
            }
        ]
    },
    "statistics": {
        "total_records": 5,
        "records_by_table": {
            "irrigate_logs": 1,
            "valve_logs": 4
        },
        "valve_status": {
            "total_nodes": 2,
            "valve_on_count": 2,
            "valve_off_count": 2,
            "node_ids": [1, 5]
        },
        "irrigation_summary": {
            "total_volume_liters": 16.204,
            "average_duration_seconds": 162,
            "nodes_irrigated": 2
        }
    }
}
```

**Validation Rules:**

-   `metadata.sesi_id_irrigate`: required, integer
-   `metadata.timestamp`: required, ISO 8601 format
-   `data.irrigate_logs`: array (optional)
-   `data.valve_logs`: array (optional)
-   `data.node_logs`: array (optional)

**Response:** `200 OK`

```json
{
    "success": true,
    "message": "Valve ON data stored successfully",
    "data": {
        "sesi_id": 6,
        "inserted_records": {
            "irrigate_logs": 1,
            "valve_logs": 2,
            "node_logs": 2
        },
        "total_inserted": 5,
        "backup_info": {
            "json_backup_id": 216,
            "data_size_kb": 3.45,
            "backup_timestamp": "2025-10-16T10:30:00Z"
        }
    },
    "timestamp": "2025-10-16T10:30:00.000000Z"
}
```

**Key Features:**

-   ✅ **Automatic JSON Backup**: Request disimpan ke `json_backup` table
-   ✅ **Irrigation Tracking**: Monitor valve ON events
-   ✅ **Multi-node Support**: Handle multiple nodes dalam satu session

**Field Mapping:**

-   `waktu_selesai` (request) → `waktu_akhir` (database)
-   Backward compatible: API accepts both field names

**Curl Example:**

```bash
curl -X POST http://127.0.0.1:8000/api/v1/ingest/valve-on \
  -H "Content-Type: application/json" \
  -d @valveon.json
```

---

### 4. POST Valve OFF (Irrigation End)

**Endpoint:** `POST /api/v1/ingest/valve-off`

**Description:** Menerima data ketika valve ditutup (irigasi selesai). Update valve logs dengan volume air dan durasi final.

**Request Body:**

```json
{
    "metadata": {
        "timestamp": "2025-10-15T13:43:08.895Z",
        "type": "valve_logs",
        "node_id": 1,
        "source": "Raspberry Pi - AgriNex IoT",
        "version": "2.0"
    },
    "data": {
        "valve_logs": [
            {
                "id": 96,
                "node_id": 1,
                "sesi_id_irrigate": 22,
                "durasi_detik": 144,
                "volume_air": 1250,
                "rata_rata": 8.68,
                "pulse": 125,
                "status": "OFF",
                "waktu": "2025-10-15T20:07:33.000Z"
            }
        ]
    }
}
```

**Validation Rules:**

-   `metadata.node_id`: required, integer
-   `metadata.timestamp`: required, ISO 8601 format
-   `data.valve_logs`: required, array, min 1 item

**Response:** `200 OK`

```json
{
    "metadata": {
        "timestamp": "2025-10-16T13:43:08.895Z",
        "type": "valve_logs",
        "node_id": 1,
        "source": "Raspberry Pi - AgriNex IoT",
        "version": "2.0"
    },
    "data": {
        "valve_logs": [
            {
                "id": 999,
                "node_id": 1,
                "sesi_id_irrigate": 999,
                "durasi_detik": 144,
                "volume_air": 1250,
                "rata_rata": 8.68,
                "pulse": 125,
                "status": "OFF",
                "waktu": "2025-10-16T20:07:33.000Z"
            }
        ]
    },
    "statistics": {
        "total_records": 1,
        "sessions": [999],
        "valve_status": {
            "valve_on_count": 0,
            "valve_off_count": 1
        },
        "irrigation_summary": {
            "total_volume_liters": 1.25,
            "average_duration_seconds": 144
        }
    }
}
```

**Key Features:**

-   ✅ **Automatic JSON Backup**: Request disimpan ke `json_backup` table
-   ✅ **Volume Tracking**: Record final volume air dan durasi
-   ✅ **Session Linking**: Terhubung dengan sesi_id_irrigate

**Curl Example:**

```bash
curl -X POST http://127.0.0.1:8000/api/v1/ingest/valve-off \
  -H "Content-Type: application/json" \
  -d @valveoff.json
```

---

## 📊 Dashboard API

API untuk frontend dashboard. Menyediakan data agregat, chart data, dan statistik sistem.

### Base Path: `/api/v1/dashboard`

---

### 1. Get Devices

**Endpoint:** `GET /api/v1/dashboard/devices`

**Description:** Mendapatkan list semua node (1-12 + 65) dengan status terkini.

**Request:**

```bash
curl -X GET http://127.0.0.1:8000/api/v1/dashboard/devices
```

**Response:** `200 OK`

```json
{
    "success": true,
    "data": {
        "nodes": [
            {
                "id": 1,
                "nama_node": "Node 1",
                "lokasi": "Greenhouse A",
                "status": "Aktif",
                "last_update": "2025-10-16T10:25:00Z",
                "signal_quality": "Excellent",
                "rssi_dbm": -65,
                "sensor_data": {
                    "voltage_v": 3.43,
                    "current_ma": 40.5,
                    "power_mw": 140,
                    "temp_c": 26.25,
                    "soil_pct": 62
                }
            },
            {
                "id": 65,
                "nama_node": "Weather Station",
                "lokasi": "Central",
                "status": "Aktif",
                "last_update": "2025-10-16T10:25:00Z",
                "signal_quality": "Excellent",
                "rssi_dbm": -68,
                "weather_data": {
                    "light": 73.3,
                    "rain": 0,
                    "wind": 0,
                    "humidity": 53,
                    "temp_dht": 26.8
                }
            }
        ],
        "summary": {
            "total_nodes": 13,
            "active_nodes": 3,
            "inactive_nodes": 10
        }
    }
}
```

---

### 2. Get Chart Data

**Endpoint:** `GET /api/v1/dashboard/charts`

**Description:** Mendapatkan historical data untuk chart (default 7 hari terakhir).

**Query Parameters:**

-   `type`: Chart type (all, light, water, soil, temp, humidity, weather) - default: all
-   `days`: Number of days (1-30) - default: 7
-   `node_id`: Specific node ID (optional)

**Request:**

```bash
curl -X GET "http://127.0.0.1:8000/api/v1/dashboard/charts?days=7&type=all"
```

**Response:** `200 OK`

```json
{
    "success": true,
    "data": {
        "light_intensity": {
            "labels": ["10:21", "10:25", "10:30", "..."],
            "values": [73.3, 75.2, 72.8, "..."],
            "unit": "Lux",
            "node_id": 65,
            "data_points": 58
        },
        "water_volume": {
            "labels": ["10:32", "11:15", "..."],
            "values": [1250, 1180, "..."],
            "unit": "mL",
            "data_points": 25
        },
        "soil_moisture": {
            "labels": ["10:21", "10:25", "..."],
            "values": [62, 60, 58, "..."],
            "unit": "%",
            "nodes": [1, 5],
            "data_points": 58
        },
        "temperature": {
            "labels": ["10:21", "10:25", "..."],
            "values": [26.25, 26.0, 26.5, "..."],
            "unit": "°C",
            "nodes": [1, 5],
            "data_points": 58
        },
        "humidity": {
            "labels": ["10:21", "10:25", "..."],
            "values": [53, 54, 52, "..."],
            "unit": "%",
            "node_id": 65,
            "data_points": 58
        }
    },
    "metadata": {
        "days_requested": 7,
        "date_range": {
            "start": "2025-10-09T00:00:00Z",
            "end": "2025-10-16T23:59:59Z"
        }
    }
}
```

**Timestamp Format:**

-   Format: `"HH:mm"` (24-hour format)
-   Example: `"10:21"`, `"14:45"`
-   Compatible with Chart.js time series

---

### 3. Get Weather

**Endpoint:** `GET /api/v1/dashboard/weather`

**Description:** Mendapatkan data weather station terkini (Node 65).

**Request:**

```bash
curl -X GET http://127.0.0.1:8000/api/v1/dashboard/weather
```

**Response:** `200 OK`

```json
{
    "success": true,
    "data": {
        "node_id": 65,
        "timestamp": "2025-10-16T10:21:14Z",
        "light": 73.3,
        "rain": 0,
        "wind": 0,
        "humidity": 53,
        "temperature": 26.8,
        "voltage": 5.01,
        "current": 38.7,
        "power": 200,
        "signal": {
            "rssi_dbm": -68,
            "snr_db": 13.3,
            "quality": "Excellent"
        }
    }
}
```

---

### 4. Get JSON Backup History

**Endpoint:** `GET /api/v1/dashboard/json-backup`

**Description:** Mendapatkan history JSON backup dari Raspberry Pi requests.

**Query Parameters:**

-   `sesi_id_getdata`: Filter by specific session ID (optional)
-   `limit`: Number of records (default: 50, max: 200)
-   `date_from`: Start date (format: Y-m-d) (optional)
-   `date_to`: End date (format: Y-m-d) (optional)

**Request:**

```bash
curl -X GET "http://127.0.0.1:8000/api/v1/dashboard/json-backup?limit=10"
```

**Response:** `200 OK`

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "sesi_id_getdata": 14,
            "data_size_kb": 5.79,
            "total_records": 17,
            "node_completeness": "16.67%",
            "getdata_logs_count": 1,
            "sensor_weather_count": 1,
            "sensor_node_count": 2,
            "backup_timestamp": "2025-10-15T12:50:24Z",
            "json_data": {
                "metadata": {...},
                "data": {...},
                "statistics": {...}
            }
        }
    ],
    "metadata": {
        "total_records": 8,
        "limit": 10,
        "filters_applied": {}
    }
}
```

**Use Cases:**

-   Debugging: Lihat exact request yang dikirim dari Raspberry Pi
-   Data Recovery: Restore data jika parsing error
-   Analytics: Analisis data size trends, completeness patterns
-   Audit Trail: Track semua request history

---

## 🔧 Legacy Endpoints

Endpoint lama untuk backward compatibility. **Recommended:** Gunakan endpoint `/api/v1/` untuk development baru.

### Base Path: `/api`

-   `POST /api/getdata` - Legacy sensor data endpoint
-   `POST /api/irrigate` - Legacy irrigation endpoint
-   `GET /api/sensor/{node_id}` - Legacy sensor by node
-   `GET /api/weather` - Legacy weather data

---

## ❌ Error Handling

### Error Response Format

```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field_name": ["Validation error message"]
    },
    "code": "ERROR_CODE"
}
```

### HTTP Status Codes

| Code | Description           | Usage                         |
| ---- | --------------------- | ----------------------------- |
| 200  | OK                    | Successful request            |
| 201  | Created               | Resource created successfully |
| 400  | Bad Request           | Invalid request format        |
| 422  | Unprocessable Entity  | Validation failed             |
| 500  | Internal Server Error | Server error                  |

### Common Error Codes

**Validation Errors (422):**

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "metadata.sesi_id_getdata": ["The sesi_id_getdata field is required."],
        "data.sensor_node_data": ["The data.sensor_node_data must be an array."]
    }
}
```

**Server Errors (500):**

```json
{
    "success": false,
    "message": "Failed to store sensor data",
    "error": "SQLSTATE[23000]: Integrity constraint violation",
    "code": "DATABASE_ERROR"
}
```

---

## 🗺️ Field Mapping Reference

### Perbedaan Nama Field (Request vs Database)

Beberapa field di request body berbeda dengan nama kolom di database. Service layer secara otomatis melakukan mapping.

| Request Body Field | Database Column | Table            | Notes               |
| ------------------ | --------------- | ---------------- | ------------------- |
| `waktu_selesai`    | `waktu_akhir`   | irrigate_logs    | Backward compatible |
| `current`          | `current_ma`    | sensor_node_data | Float (was integer) |
| `power`            | `power_mw`      | sensor_node_data | Float (was integer) |
| `volume_ml`        | `volume_air`    | valve_logs       | Renamed for clarity |

### Database Schema Notes

**Non Auto-Increment Fields:**

-   `irrigate_logs.id` - Manual ID assignment required
-   Service layer automatically generates ID: `max(id) + 1`

**Float vs Integer:**

-   `current_ma`, `power_mw` - Changed to FLOAT for precision
-   All weather data fields - FLOAT type

**Status Values:**

-   `node.status`: "Aktif", "Tidak Aktif"
-   `valve_logs.status`: "ON", "OFF"
-   `node_logs.signal_quality`: "Excellent", "Good", "Fair", "Poor"

---

## 🧪 Testing Guide

### Setup Testing Environment

1. **Import Postman Collection:**

    ```
    File > Import > AgriNex_Laravel_API_v2.postman_collection.json
    ```

2. **Set Environment Variable:**

    ```
    base_url = http://127.0.0.1:8000
    ```

3. **Start Laravel Server:**
    ```bash
    cd agrinex-lara
    php artisan serve
    ```

### Test Data Files

Gunakan file JSON yang sudah disediakan:

**sensordata.json** - Sensor data test

```bash
curl -X POST http://127.0.0.1:8000/api/v1/ingest/sensor-data \
  -H "Content-Type: application/json" \
  -d @sensordata.json
```

**valveon.json** - Valve ON test

```bash
curl -X POST http://127.0.0.1:8000/api/v1/ingest/valve-on \
  -H "Content-Type: application/json" \
  -d @valveon.json
```

**valveoff.json** - Valve OFF test

```bash
curl -X POST http://127.0.0.1:8000/api/v1/ingest/valve-off \
  -H "Content-Type: application/json" \
  -d @valveoff.json
```

### Manual Testing Steps

**Test 1: Health Check**

```bash
curl -X GET http://127.0.0.1:8000/api/v1/ingest/health
# Expected: 200 OK with status message
```

**Test 2: Post Sensor Data**

```bash
curl -X POST http://127.0.0.1:8000/api/v1/ingest/sensor-data \
  -H "Content-Type: application/json" \
  -d '{
    "metadata": {"sesi_id_getdata": 999, "timestamp": "2025-10-16T10:00:00Z"},
    "data": {"sensor_node_data": [{"node_id": 1, "voltage_v": 3.3}]}
  }'
# Expected: 200 OK with inserted_records count
```

**Test 3: Get Dashboard Data**

```bash
curl -X GET "http://127.0.0.1:8000/api/v1/dashboard/charts?days=7"
# Expected: 200 OK with chart data arrays
```

### Validation Testing

**Test Invalid Request (Missing Required Field):**

```bash
curl -X POST http://127.0.0.1:8000/api/v1/ingest/sensor-data \
  -H "Content-Type: application/json" \
  -d '{"metadata": {}}'
# Expected: 422 Unprocessable Entity with validation errors
```

**Test Invalid Data Type:**

```bash
curl -X POST http://127.0.0.1:8000/api/v1/ingest/sensor-data \
  -H "Content-Type: application/json" \
  -d '{"metadata": {"sesi_id_getdata": "invalid"}}'
# Expected: 422 with "must be an integer" error
```

### Database Verification

Setelah POST data, verifikasi di database:

```sql
-- Check latest sensor data
SELECT * FROM sensor_node_data ORDER BY id DESC LIMIT 10;

-- Check irrigation sessions
SELECT * FROM irrigate_logs ORDER BY id DESC LIMIT 10;

-- Check valve operations
SELECT * FROM valve_logs ORDER BY id DESC LIMIT 10;

-- Check node activity
SELECT * FROM node_logs ORDER BY id DESC LIMIT 20;
```

### Integration Testing (Raspberry Pi)

**Python Script Example:**

```python
import requests
import json
from datetime import datetime

# Load test data
with open('sensordata.json', 'r') as f:
    payload = json.load(f)

# Update timestamp
payload['metadata']['timestamp'] = datetime.utcnow().isoformat() + 'Z'

# Send to API
response = requests.post(
    'http://127.0.0.1:8000/api/v1/ingest/sensor-data',
    json=payload,
    headers={'Content-Type': 'application/json'}
)

print(f"Status: {response.status_code}")
print(f"Response: {response.json()}")
```

### Performance Testing

**Test API Response Time:**

```bash
# Using curl with timing
curl -w "@curl-format.txt" -o /dev/null -s \
  "http://127.0.0.1:8000/api/v1/dashboard/charts?days=7"

# curl-format.txt content:
time_namelookup:  %{time_namelookup}\n
time_connect:  %{time_connect}\n
time_appconnect:  %{time_appconnect}\n
time_pretransfer:  %{time_pretransfer}\n
time_redirect:  %{time_redirect}\n
time_starttransfer:  %{time_starttransfer}\n
time_total:  %{time_total}\n
```

---

## 📝 Notes for Developers

### Database Considerations

1. **Manual ID Assignment**: Tabel `irrigate_logs` tidak menggunakan AUTO_INCREMENT, service layer handle ini secara otomatis.

2. **Field Naming**: Beberapa field berbeda antara API dan database, mapping otomatis sudah diimplementasikan.

3. **Float Precision**: Field `current_ma` dan `power_mw` menggunakan FLOAT untuk presisi yang lebih baik.

4. **JSON Backup**: **IMPORTANT!** Setiap request dari Raspberry Pi WAJIB include field `statistics` karena akan disimpan ke tabel `json_backup` untuk audit trail. Format statistics:
    ```json
    "statistics": {
      "total_records": 17,
      "records_by_table": {...},
      "node_status": {...}
    }
    ```

### Raspberry Pi Integration

**Recommended Workflow:**

1. Sensor reading → Format JSON → POST to `/ingest/sensor-data`
2. Valve ON → Format JSON → POST to `/ingest/valve-on`
3. Valve OFF → Format JSON → POST to `/ingest/valve-off`
4. Error handling → Retry mechanism with exponential backoff

**Network Reliability:**

-   Implement local queue for offline scenarios
-   Store failed requests and retry when connection restored
-   Use health check endpoint for connectivity monitoring

### Frontend Integration

**Dashboard Auto-Refresh:**

-   Current: 10 minutes interval
-   Use WebSocket for real-time updates (future enhancement)
-   Chart.js configuration optimized for 58+ data points

---

## 📞 Support & Contact

**Developer:** AgriNex Team  
**Laravel Version:** 11.x  
**API Version:** 2.0  
**Last Updated:** October 16, 2025

---

## 🔄 Changelog

### v2.0 (October 2025)

-   ✅ Added 3 new data ingestion endpoints
-   ✅ Implemented DataIngestionController
-   ✅ Field mapping for waktu_selesai → waktu_akhir
-   ✅ Manual ID assignment for irrigate_logs
-   ✅ Comprehensive validation & error handling
-   ✅ Updated Postman collection
-   ✅ Single unified documentation file

### v1.0 (October 2025)

-   Initial Laravel migration from PHP
-   Dashboard API with 7 endpoints
-   7-day historical chart data
-   Alpine.js + Chart.js frontend
-   Database migration to new structure

---

**End of Documentation**
