# Product Requirements Document (PRD): AgriNex Mobile App

## 1. Project Overview
**Name:** AgriNex Smart Drip Irrigation Mobile App  
**Platform:** iOS & Android (Cross-platform)  
**Context:** AgriNex is an existing Smart Agriculture IoT platform. Currently, it has a Laravel-based web dashboard. The goal of this project is to build a companion mobile application that allows farmers and agronomists to monitor their irrigation systems (nodes), view real-time weather data, and track irrigation history on the go.

## 2. Target Audience
- **Farmers / Farm Managers:** Need quick access to soil moisture levels, valve status, and weather conditions.
- **Agronomists / Researchers:** Need historical charts (temperature, humidity, water volume) to analyze crop health and irrigation efficiency.

## 3. Core Features & Requirements

### 3.1. Dashboard (Home Screen)
- **Summary Metrics:** Total active nodes, inactive nodes, and overall system health.
- **Weather & Environment Overview:** Real-time data from the nodes including temperature (°C), soil moisture (%), and calculated power metrics (Voltage/Current).
- **Water Consumption:** Real-time tracking of flow rate (L/min) and total volume (Liters) based on flow meter interrupts.
- **Quick Alerts:** Warnings for low battery/voltage, disconnected nodes, or critically low soil moisture.

### 3.2. Node Monitoring (Device List)
- **List View:** Display all 12 sensor nodes with their current status (Active/Inactive), signal quality (RSSI/SNR), and last update timestamp.
- **Node Details:** Clicking a node shows:
  - Soil Moisture (%)
  - Temperature (°C)
  - Power Metrics (Voltage, Current, Power)
  - Current Valve Status (ON/OFF)

### 3.3. Analytics & Charts
- **Historical Data:** Visual charts for the last 7 to 30 days.
- **Metrics to Track:**
  - Water Volume Consumed (Liters) and Flow Rate (L/min)
  - Soil Moisture Trends (%)
  - Temperature (°C)
  - Power Consumption (Voltage, Current, Power in mW)
- **Interactivity:** Tooltips on charts, ability to filter by specific node or time range.

### 3.4. Irrigation & Valve History
- **Logs:** A history screen showing when valves were opened/closed, the duration of irrigation, and the total volume of water dispersed.

## 4. Technical Constraints & Assumptions
- **Read-Only (Phase 1):** The mobile app will initially act as a monitoring tool. Valve actuation is handled locally via Edge AI (Fuzzy Logic on the Gateway) or through the web dashboard.
- **API Dependency:** The app will consume the existing `AgriNex Laravel API v2.0` (`/api/v1/dashboard/*` endpoints).
- **Authentication:** Currently, the API does not use authentication, but the mobile app architecture should be prepared for token-based auth (e.g., Laravel Sanctum) in future updates.

## 5. Success Metrics
- **Performance:** App loads and fetches dashboard data in under 2 seconds.
- **Usability:** Users can check node status in 2 taps or less from the home screen.
- **Reliability:** Graceful error handling when the device is offline or the API is unreachable.
