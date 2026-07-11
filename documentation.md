# Technical Documentation: AgriNex Mobile App

## 1. System Architecture
The AgriNex Mobile App serves as the frontend client for the AgriNex IoT Ecosystem. 

**Data Flow:**
`IoT End-Nodes (LoRa)` -> `Edge Gateway (ESP32)` -> `Laravel Backend (MySQL)` -> **`Mobile App (REST API)`**

The mobile app will exclusively communicate with the Laravel Backend via HTTP REST endpoints.

## 2. Recommended Tech Stack
- **Framework:** Flutter or React Native (for fast cross-platform deployment).
- **State Management:** Provider/Riverpod (Flutter) or Redux/Zustand (React Native).
- **Networking:** `http` / `dio` (Flutter) or `axios` (React Native).
- **Charts:** `fl_chart` (Flutter) or `react-native-chart-kit` (React Native).

## 3. API Integration Guide

The app will consume the `Dashboard API` layer. Base URL: `http://<SERVER_IP_OR_DOMAIN>/api/v1/dashboard`

### 3.1. Get Devices (Node List)
- **Endpoint:** `GET /devices`
- **Purpose:** Fetches the list of all nodes (Sensor nodes 1-12 and Weather Station 65).
- **Key Response Fields:**
  - `data.nodes[].status`: "Aktif" or "Non Aktif"
  - `data.nodes[].sensor_data`: Contains `soil_pct`, `temp_c`, `voltage_v`.
  - `data.nodes[].weather_data`: Contains `light`, `rain`, `humidity` (Only for Node 65).
  - `data.summary`: Total nodes, active, inactive count.

### 3.2. Get Weather
- **Endpoint:** `GET /weather`
- **Purpose:** Fetches the latest environmental data from Node 65 for the Home Screen header.
- **Key Response Fields:** `light`, `rain`, `wind`, `humidity`, `temperature`.

### 3.3. Get Chart Data
- **Endpoint:** `GET /charts?days={number}&type={all|light|water|soil|temp|humidity}`
- **Purpose:** Fetches timeseries data for rendering graphs.
- **Key Response Fields:**
  - `data.{metric}.labels`: Array of timestamps (e.g., ["10:21", "10:25"]).
  - `data.{metric}.values`: Array of data points corresponding to the labels.

## 4. Edge AI & Hardware Context
*Note for mobile developers to understand the system context:*
- **Fuzzy Logic Automation:** The valves are controlled automatically by the Gateway based on Soil Moisture and Temperature. The mobile app reflects these decisions in the UI.
- **Data Frequency:** Nodes wake up from deep sleep, send data via LoRa, and go back to sleep. Data updates on the API might have 1-5 minute intervals. The app should implement pull-to-refresh rather than aggressive polling to save battery on the mobile device.

## 5. Development Tasks (Roadmap)
1. **Initialize Project:** Setup Flutter/RN project structure and routing.
2. **API Service Layer:** Create models and API clients for `/devices`, `/weather`, and `/charts`.
3. **UI Implementation - Home:** Build weather widget and system summary cards.
4. **UI Implementation - Nodes:** Build ListView for nodes with status indicators.
5. **UI Implementation - Analytics:** Integrate charting library and bind to `/charts` data.
6. **Error Handling:** Add offline states, loading skeletons, and API timeout handlers.
