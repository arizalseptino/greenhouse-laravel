# 🌿 Greenhouse Real-Time IoT Data Ingestion API & Cloud Backend

An enterprise-grade, fault-tolerant IoT telemetric data ingestion system built with **Laravel 11**, **MySQL**, and **ESP32 Microcontroller**. Designed to capture, validate, and store real-time microclimate parameters for agricultural data analytics.

---

## 🏗 System Architecture & Data Flow

ESP32 Hardware (DHT22 + Soil Moisture Sensor)
  ├──> Real-time Transmission: HTTP POST (JSON) ──> Laravel REST API (Railway) ──> MySQL Cloud DB
  └──> Connection Drop (Offline Mode): Buffer to MicroSD
        └──> Reconnection Trigger: Auto-flush stored logs with original timestamps to REST API

---

## ⚡ Engineering Highlights & System Metrics

- **Ingestion Latency & Reliability:** Tested with 100% request delivery success rate and average API response time of ~1.0 second on Railway cloud deployment[cite: 3].
- **Hardware Fault Tolerance:** Integrated 5V Mini UPS backup system providing **13 hours 42 minutes** of continuous operation during main power outages[cite: 3].
- **Offline Data Buffering:** MicroSD hybrid storage architecture capable of buffering telemetric records locally during network downtime and automatically syncing when online[cite: 3].
- **Data Quality at Ingestion:** Embedded filter logic to detect and discard hardware ADC saturation anomalies (e.g., raw sensor disconnect value 4095 and uncalibrated 0% readings)[cite: 3].
- **Firmware Mathematical Calibration:** Sensor signal processing implemented on ESP32 using linear regression conversion (R² = 0.9897) for volumetric soil moisture percentage[cite: 3].

---

## 🔌 API Contract & Documentation

### POST /api/sensor-data

Receives real-time telemetric environmental sensor readings.

#### Request Headers:
- `Content-Type: application/json`
- `Accept: application/json`

#### Sample JSON Payload:
{
  "timestamp": "2026-04-14 08:41:00",
  "suhu_udara": 32.28,
  "kelembaban_udara": 76.22,
  "soil_analog": 3211,
  "kelembaban_tanah": 14.48
}

#### Sample Response (201 Created):
{
  "status": "success",
  "message": "Sensor data recorded successfully",
  "data": {
    "id": 4139,
    "timestamp": "2026-04-14 08:41:00",
    "created_at": "2026-04-14T08:41:00.000000Z"
  }
}

---

## 🗄 Database Schema (MySQL)

Table Name: `sensor_data`

- `id`: BIGINT (PK, Auto Increment) - Primary Key
- `timestamp`: DATETIME - Timestamp of physical measurement
- `suhu_udara`: FLOAT - Ambient temperature (°C)
- `kelembaban_udara`: FLOAT - Air relative humidity (%)
- `soil_analog`: INT - Raw ADC sensor reading
- `kelembaban_tanah`: FLOAT - Calibrated soil moisture (%)
- `created_at`: TIMESTAMP - System record creation time

---

## 🛠 Local Setup & Installation

1. Clone Repository:
   git clone [https://github.com/arizalseptino/greenhouse-laravel.git](https://github.com/arizalseptino/greenhouse-laravel.git)
   cd greenhouse-laravel

2. Install Dependencies:
   composer install

3. Environment Configuration:
   cp .env.example .env
   php artisan key:generate

4. Database Migration:
   php artisan migrate

5. Run Development Server:
   php artisan serve
