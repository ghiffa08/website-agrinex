{{-- Dashboard Alpine.js Function --}}
<script>
    function dashboard() {
        return {
            // Language state
            currentLang: localStorage.getItem('sis_lang') || 'id',
            translations: {
                id: {
                    // Header
                    appTitle: 'Irigasi Pintar',
                    appSubtitle: 'Monitoring & otomasi penyiraman',
                    switchLang: 'Ganti ke Bahasa Inggris',
                    refresh: 'Refresh',
                    loading: 'Memuat',
                    admin: 'Admin',
                    login: 'Masuk',
                    // Weather section
                    currentTime: 'Waktu Sekarang',
                    currentDate: 'Tanggal Hari Ini',
                    currentWeather: 'Cuaca Saat Ini',
                    forecast: 'Prakiraan',
                    next24h: '24 Jam',
                    next7d: 'Minggu',
                    day: 'Tanggal',
                    month: 'Bulan',
                    year: 'Tahun',
                    humidity: 'Kelembapan',
                    windSpeed: 'Kecepatan Angin',
                    pressure: 'Tekanan',
                    lightPercent: 'cahaya',
                    // Tasks
                    activities: 'Aktivitas / Peringatan',
                    weeklyTasks: 'Tugas Minggu Ini',
                    upcomingWeek: 'Minggu Ini',
                    noTasks: 'Tidak ada tugas terjadwal minggu ini',
                    prevWeek: '‹ Minggu Lalu',
                    nextWeek: 'Minggu Depan ›',
                    today: 'Kini butuh',
                    daysShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    // Charts
                    environmentSummary: 'Ringkasan Lingkungan',
                    lightIntensity: 'Intensitas Cahaya',
                    waterLevel: 'Ketinggian Air',
                    soilMoisture: 'Kelembapan Tanah',
                    temperature: 'Suhu',
                    airHumidity: 'Kelembapan Udara',
                    time: 'Waktu',
                    // Metrics
                    temp: 'Suhu',
                    hum: 'Kelembapan Udara',
                    soil: 'Kelembapan Tanah',
                    light: 'Cahaya',
                    rain: 'Hujan',
                    water: 'Ketinggian Air',
                    // Devices
                    devices: 'Perangkat',
                    allDevices: 'Semua Perangkat',
                    noDevices: 'Tidak ada data perangkat',
                    viewDetails: 'Detail',
                    battery: 'Baterai',
                    waterUsageToday: 'Pemakaian Hari Ini',
                    lastUpdate: 'Terakhir',
                    // Tank
                    waterTank: 'Tangki Air',
                    capacity: 'Kapasitas',
                    currentLevel: 'Level Saat Ini',
                    status: 'Status',
                    lastUpdated: 'Terakhir Diperbarui',
                    todaySchedule: 'Jadwal Hari Ini',
                    noSchedule: 'Tidak ada jadwal',
                    // Usage
                    waterUsageHistory: 'Riwayat Penggunaan Air',
                    last30Days: '30 Hari Terakhir',
                    last24Hours: '24 Jam Terakhir',
                    dailyData30: 'Data harian dalam 30 hari terakhir',
                    hourlyData24: 'Data per jam dalam 24 jam terakhir',
                    totalUsage: 'Total',
                    avgUsage: 'Rata-rata',
                    peakUsage: 'Puncak',
                    lowUsage: 'Terendah',
                    days: 'hari',
                    noDataYet: 'Belum ada data',
                    // Location
                    location: 'Lokasi',
                    streetView: 'Street View',
                    villageMap: 'Peta Desa',
                    close: 'Tutup',
                    viewFullMap: 'Lihat Peta Lengkap',
                    // Modal
                    deviceDetails: 'Detail Perangkat',
                    sessions: 'Sesi',
                    usageHistory: 'Riwayat Pemakaian',
                    noData: 'Tidak ada data',
                    // Units
                    celsius: '°C',
                    percent: '%',
                    lux: 'lux',
                    mm: 'mm',
                    cm: 'cm',
                    liter: 'L',
                    kmh: 'km/j',
                    hPa: 'hPa'
                },
                en: {
                    // Header
                    appTitle: 'Smart Irrigation',
                    appSubtitle: 'Monitoring & irrigation automation',
                    switchLang: 'Switch to Indonesian',
                    refresh: 'Refresh',
                    loading: 'Loading',
                    admin: 'Admin',
                    login: 'Login',
                    // Weather section
                    currentTime: 'Current Time',
                    currentDate: 'Today\'s Date',
                    currentWeather: 'Current Weather',
                    forecast: 'Forecast',
                    next24h: '24 Hours',
                    next7d: 'Week',
                    day: 'Day',
                    month: 'Month',
                    year: 'Year',
                    humidity: 'Humidity',
                    windSpeed: 'Wind Speed',
                    pressure: 'Pressure',
                    lightPercent: 'light',
                    // Tasks
                    activities: 'Activities / Alerts',
                    weeklyTasks: 'This Week\'s Tasks',
                    upcomingWeek: 'This Week',
                    noTasks: 'No scheduled tasks this week',
                    prevWeek: '‹ Previous Week',
                    nextWeek: 'Next Week ›',
                    today: 'Today need',
                    daysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    // Charts
                    environmentSummary: 'Environmental Summary',
                    lightIntensity: 'Light Intensity',
                    waterLevel: 'Water Level',
                    soilMoisture: 'Soil Moisture',
                    temperature: 'Temperature',
                    airHumidity: 'Air Humidity',
                    time: 'Time',
                    // Metrics
                    temp: 'Temperature',
                    hum: 'Air Humidity',
                    soil: 'Soil Moisture',
                    light: 'Light',
                    rain: 'Rain',
                    water: 'Water Level',
                    // Devices
                    devices: 'Devices',
                    allDevices: 'All Devices',
                    noDevices: 'No device data',
                    viewDetails: 'Details',
                    battery: 'Battery',
                    waterUsageToday: 'Today\'s Usage',
                    lastUpdate: 'Last',
                    // Tank
                    waterTank: 'Water Tank',
                    capacity: 'Capacity',
                    currentLevel: 'Current Level',
                    status: 'Status',
                    lastUpdated: 'Last Updated',
                    todaySchedule: 'Today\'s Schedule',
                    noSchedule: 'No schedule',
                    // Usage
                    waterUsageHistory: 'Water Usage History',
                    last30Days: 'Last 30 Days',
                    last24Hours: 'Last 24 Hours',
                    dailyData30: 'Daily data for the last 30 days',
                    hourlyData24: 'Hourly data for the last 24 hours',
                    totalUsage: 'Total',
                    avgUsage: 'Average',
                    peakUsage: 'Peak',
                    lowUsage: 'Lowest',
                    days: 'days',
                    noDataYet: 'No data yet',
                    // Location
                    location: 'Location',
                    streetView: 'Street View',
                    villageMap: 'Village Map',
                    close: 'Close',
                    viewFullMap: 'View Full Map',
                    // Modal
                    deviceDetails: 'Device Details',
                    sessions: 'Sessions',
                    usageHistory: 'Usage History',
                    noData: 'No data',
                    // Units
                    celsius: '°C',
                    percent: '%',
                    lux: 'lux',
                    mm: 'mm',
                    cm: 'cm',
                    liter: 'L',
                    kmh: 'km/h',
                    hPa: 'hPa'
                }
            },
            darkMode: localStorage.getItem('sis_dark') === '1',
            sidebarOpen: false,
            loadingAll: false,
            loadingDevices: false,
            loadingWeather: true,
            loadingTank: true,
            loadingUsage: true,
            loadingCharts: true,
            loadingSchedule: true,
            fetchError: false,
            lastUpdated: null,
            devices: [],
            get groupedDevices() {
                const groups = {};
                this.devices.forEach(d => {
                    const groupName = d.lahan_pantau_name || 'Unassigned';
                    if (!groups[groupName]) {
                        groups[groupName] = [];
                    }
                    groups[groupName].push(d);
                });
                return groups;
            },
            weatherSummary: {},
            forecastEntries: [],
            forecast24h: [],
            forecastWeekly: [],
            forecastView: '24h',
            calendarBase: new Date(),
            calendarDays: [],
            calendarMonthLabel: '',
            selectedDate: null,
            calendarDetails: null,
            clock: {
                time: '--:--',
                seconds: '',
                dateLong: '',
                dateShort: '',
                day: '',
                month: '',
                year: ''
            },
            // Weekly + tasks view
            weekOffset: 0,
            weekViewDays: [],
            currentTasks: [],
            weekLegend: [{
                    key: 'plowing',
                    label: 'Olah Lahan',
                    bg: 'bg-amber-600'
                },
                {
                    key: 'fert',
                    label: 'Pemupukan',
                    bg: 'bg-green-600'
                },
                {
                    key: 'ship',
                    label: 'Pengiriman',
                    bg: 'bg-yellow-400'
                },
                {
                    key: 'idle',
                    label: 'Tidak ada',
                    bg: 'bg-gray-200'
                }
            ],
            categoryConfig: {
                plowing: {
                    maxRain: 2,
                    maxTemp: 30
                },
                fertilization: {
                    maxRain: 2,
                    minTemp: 30
                },
                shipment: {
                    minRain: 5
                },
            },
            categoryStyles: {
                plowing: {
                    bg: 'bg-gradient-to-b from-amber-500 to-amber-600 text-white',
                    icon: '🚜'
                },
                fert: {
                    bg: 'bg-gradient-to-b from-green-500 to-green-700 text-white',
                    icon: '🧪'
                },
                ship: {
                    bg: 'bg-gradient-to-b from-yellow-300 to-yellow-500 text-gray-800',
                    icon: '🚚'
                },
                idle: {
                    bg: 'bg-gradient-to-b from-gray-50 to-gray-100 text-gray-700 border border-gray-200',
                    icon: '➖'
                },
            },
            showDeviceModal: false,
            selectedDevice: null,
            deviceSessions: [],
            deviceSessionsSummary: null,
            deviceUsageHistory: [],
            loadingDeviceDetail: false,
            tank: {},
            tankUpdatedAt: null,
            plan: {},
            usage: [],
            usage24h: [],
            usageChart: null,
            usageChart24h: null,
            // Environmental Charts
            lightIntensityChart: null,
            waterLevelChart: null,
            soilMoistureChart: null,
            temperatureChart: null,
            humidityChart: null,
            lightIntensityData: {
                labels: [],
                li1: [],
                li2: []
            },
            waterLevelData: {
                labels: [],
                levels: []
            },
            soilMoistureData: {
                labels: [],
                sensors: {} // Will contain SM1, SM2, SM3, etc.
            },
            temperatureData: {
                labels: [],
                t1: [],
                t2: []
            },
            humidityData: {
                labels: [],
                h1: [],
                h2: []
            },
            soilMoistureSensors: [{
                    id: 'SM1',
                    label: 'SM1',
                    color: '#3b82f6'
                },
                {
                    id: 'SM4',
                    label: 'SM4',
                    color: '#a855f7'
                },
                {
                    id: 'SM2',
                    label: 'SM2',
                    color: '#f97316'
                },
                {
                    id: 'SM3',
                    label: 'SM3',
                    color: '#eab308'
                },
                {
                    id: 'SM10',
                    label: 'SM10',
                    color: '#84cc16'
                },
                {
                    id: 'SM7',
                    label: 'SM7',
                    color: '#ef4444'
                },
                {
                    id: 'SM9',
                    label: 'SM9',
                    color: '#ec4899'
                },
                {
                    id: 'SM11',
                    label: 'SM11',
                    color: '#22d3ee'
                },
                {
                    id: 'SM6',
                    label: 'SM6',
                    color: '#9ca3af'
                },
                {
                    id: 'SM5',
                    label: 'SM5',
                    color: '#6366f1'
                },
                {
                    id: 'SM8',
                    label: 'SM8',
                    color: '#facc15'
                }
            ],
            chartMaxPoints: 30,
            // Legacy topStats removed in favor of topMetricCards
            topMetricCards: [{
                    key: 'temp',
                    label: 'SUHU',
                    type: 'gauge',
                    min: 10,
                    max: 45,
                    unit: '°C',
                    value: null,
                    display: '-',
                    pct: 0,
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 11.25V5.25a3 3 0 1 0-6 0v6.052A4.5 4.5 0 1 0 15 11.25Z" /></svg>',
                    desc: '',
                    color: '#16a34a'
                },
                {
                    key: 'humidity',
                    label: 'KELEMBAPAN',
                    type: 'gauge',
                    min: 0,
                    max: 100,
                    unit: '%',
                    value: null,
                    display: '-',
                    pct: 0,
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a6.002 6.002 0 0 0 3.6-10.8c-.8-.8-2.6-2.9-3.6-4.2-1 1.3-2.8 3.4-3.6 4.2A6.002 6.002 0 0 0 12 21Z" /></svg>',
                    desc: '',
                    color: '#16a34a'
                },
                {
                    key: 'light',
                    label: 'CAHAYA',
                    type: 'gauge',
                    min: 0,
                    max: 100,
                    unit: '%',
                    value: null,
                    display: '-',
                    pct: 0,
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" /></svg>',
                    desc: '',
                    color: '#16a34a'
                },
                {
                    key: 'wind',
                    label: 'ANGIN',
                    type: 'gauge',
                    min: 0,
                    max: 15,
                    unit: 'm/s',
                    value: null,
                    display: '-',
                    pct: 0,
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 0 0 4.5 4.5H18a3.75 3.75 0 0 0 1.332-7.257 3 3 0 0 0-3.758-3.848 5.25 5.25 0 0 0-10.233 2.33A4.502 4.502 0 0 0 2.25 15Z" /></svg>',
                    desc: '',
                    color: '#16a34a'
                },
                {
                    key: 'rain',
                    label: 'HUJAN',
                    type: 'plain',
                    min: 0,
                    max: 50,
                    unit: 'mm',
                    value: null,
                    display: '0.0mm',
                    pct: 0,
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M8.625 21v-8.25M15.375 21v-8.25M2.25 12a4.5 4.5 0 0 1 4.5-4.5H18a3.75 3.75 0 0 1 1.332 7.257 3 3 0 0 1-3.758 3.848 5.25 5.25 0 0 1-10.233-2.33A4.502 4.502 0 0 1 2.25 12Z" /></svg>',
                    desc: 'current',
                    color: '#6366f1'
                },
                // {
                //     key: 'tank',
                //     label: 'TANGKI',
                //     type: 'linear',
                //     min: 0,
                //     max: 100,
                //     unit: '%',
                //     value: null,
                //     display: '-',
                //     pct: 0,
                //     icon: '🛢️',
                //     desc: '',
                //     color: '#16a34a'
                // },
                {
                    key: 'battery',
                    label: 'BATERAI',
                    type: 'linear',
                    min: 0,
                    max: 100,
                    unit: '%',
                    value: null,
                    display: '-',
                    pct: 0,
                    icon: '🔋',
                    desc: '',
                    color: '#16a34a'
                },
                // {
                //     key: 'devices',
                //     label: 'DEVICE',
                //     type: 'plain',
                //     min: 0,
                //     max: 50,
                //     unit: '',
                //     value: null,
                //     display: '-',
                //     pct: 0,
                //     icon: '📡',
                //     desc: 'online',
                //     color: '#16a34a'
                // },
            ],
            applyPersistedTheme() {
                if (this.darkMode) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            },
            // Language Methods
            t(key) {
                return this.translations[this.currentLang][key] || key;
            },
            toggleLanguage() {
                this.currentLang = this.currentLang === 'id' ? 'en' : 'id';
                localStorage.setItem('sis_lang', this.currentLang);
                // Update page title
                document.title = this.t('appTitle');
                // Re-render charts with new language
                this.updateChartLanguage();
            },
            updateChartLanguage() {
                // Update chart axis labels
                if (this.lightIntensityChart) {
                    this.lightIntensityChart.options.scales.x.title.text = this.t('time');
                    this.lightIntensityChart.options.scales.y.title.text =
                        `${this.t('lightIntensity')} (${this.t('lux')})`;
                    this.lightIntensityChart.update('none');
                }
                if (this.waterLevelChart) {
                    this.waterLevelChart.options.scales.x.title.text = this.t('time');
                    this.waterLevelChart.options.scales.y.title.text = `${this.t('waterLevel')} (${this.t('cm')})`;
                    this.waterLevelChart.update('none');
                }
                if (this.soilMoistureChart) {
                    this.soilMoistureChart.options.scales.x.title.text = this.t('time');
                    this.soilMoistureChart.options.scales.y.title.text =
                        `${this.t('soilMoisture')} (${this.t('percent')})`;
                    this.soilMoistureChart.update('none');
                }
                if (this.temperatureChart) {
                    this.temperatureChart.options.scales.x.title.text = this.t('time');
                    this.temperatureChart.options.scales.y.title.text =
                        `${this.t('temperature')} (${this.t('celsius')})`;
                    this.temperatureChart.update('none');
                }
                if (this.humidityChart) {
                    this.humidityChart.options.scales.x.title.text = this.t('time');
                    this.humidityChart.options.scales.y.title.text = `${this.t('airHumidity')} (${this.t('percent')})`;
                    this.humidityChart.update('none');
                }
                if (this.usageChart) {
                    this.usageChart.update('none');
                }
                if (this.usageChart24h) {
                    this.usageChart24h.update('none');
                }
            },
            // Location section (no dynamic state needed after refactor)
            showFullMap: false,
            leafletInited: false,
            leafletFullInited: false,
            googleMapsLink: 'https://maps.google.com/?q=-6.9891469,108.6086561',
            villageCenter: {
                lat: -6.9891469,
                lng: 108.6086561
            },
            villagePolygon: [
                [-6.9869, 108.6029],
                [-6.9878, 108.6065],
                [-6.9889, 108.6094],
                [-6.9903, 108.6110],
                [-6.9920, 108.6100],
                [-6.9910, 108.6068],
                [-6.9898, 108.6035]
            ],
            metricSnapshots: {},
            persistDark() {
                localStorage.setItem('sis_dark', this.darkMode ? '1' : '0');
                this.applyPersistedTheme();
            },
            toggleDark() {
                this.darkMode = !this.darkMode;
                this.persistDark();
            },
            metricBy(metricKey) {
                return this.topMetricCards.find(metric => metric.key === metricKey);
            },
            updateMetric(key, val, desc = '') {
                const metric = this.metricBy(key);
                if (!metric) return;
                if (val == null || isNaN(parseFloat(val))) return; // ignore invalid

                const newValue = parseFloat(val);

                // Prevent unnecessary updates that trigger reactivity
                if (metric.value === newValue && metric.desc === desc) return;

                metric.value = newValue;
                if (metric.type === 'plain') {
                    metric.display = metric.value.toFixed(0); // just integer count
                } else {
                    metric.display = (metric.type === 'gauge' && metric.unit === '%') ? Math.round(metric.value) +
                        metric.unit : (metric.value
                            .toFixed ? metric.value.toFixed((metric.unit === '%' || metric.max <= 20) ? 0 : 1) : metric
                            .value) + metric.unit;
                }
                metric.desc = desc;
                metric.pct = this.normalizePct(metric.value, metric.min, metric.max);
                metric.color = this.colorFor(metric.pct);
                // snapshot for tooltip (store first capture per minute)
                this.metricSnapshots[metric.key] = {
                    value: metric.display,
                    ts: new Date()
                };
            },
            normalizePct(value, min, max) {
                if (value == null) return 0;
                const clamped = Math.max(min, Math.min(max, value));
                return ((clamped - min) / (max - min)) * 100;
            },
            colorFor(pct) {
                // 0 red -> 50 orange -> 100 green
                const hue = (pct * 120) / 100; // 0=red 120=green
                return `hsl(${hue}, 70%, 45%)`;
            },
            gaugeStyle(metric) {
                return `background: conic-gradient(${metric.color} 0% ${metric.pct}%, #e5e7eb ${metric.pct}% 100%);`;
            },
            metricIcon(key) {
                const base = 'stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"';
                const icons = {
                    temp: `<svg viewBox='0 0 24 24'><path ${base} d='M10 13.5V5a2 2 0 1 1 4 0v8.5a4 4 0 1 1-4 0Z'/><path ${base} d='M10 10h4'/></svg>`,
                    humidity: `<svg viewBox='0 0 24 24'><path ${base} d='M12 3.5c0 .5-5 6-5 9.5a5 5 0 0 0 10 0c0-3.5-5-9-5-9.5Z'/></svg>`,
                    light: `<svg viewBox='0 0 24 24'><circle ${base} cx='12' cy='12' r='4'/><path ${base} d='M12 2v2M12 20v2M4 12H2M22 12h-2M5.6 5.6 4.2 4.2M19.8 19.8l-1.4-1.4M18.4 5.6l1.4-1.4M4.2 19.8l1.4-1.4'/></svg>`,
                    wind: `<svg viewBox='0 0 24 24'><path ${base} d='M4 12h11a3 3 0 1 0-3-3'/><path ${base} d='M2 16h13a4 4 0 1 1-4 4'/></svg>`,
                    rain: `<svg viewBox='0 0 24 24'><path ${base} d='M7 18c1.5-2 3-4.667 5-9 2 4.333 3.5 7 5 9a5 5 0 0 1-10 0Z'/></svg>`,
                    tank: `<svg viewBox='0 0 24 24'><rect ${base} x='6' y='3' width='12' height='18' rx='2'/><path ${base} d='M6 8h12'/><path ${base} d='M10 13h4'/></svg>`,
                    battery: `<svg viewBox='0 0 24 24'><rect ${base} x='3' y='8' width='16' height='8' rx='2'/><path ${base} d='M21 10v4'/><path ${base} d='M6 12h4'/></svg>`,
                    devices: `<svg viewBox='0 0 24 24'><rect ${base} x='3' y='4' width='13' height='14' rx='2'/><path ${base} d='M8 20h12V8'/><path ${base} d='M12 16h.01'/></svg>`
                };
                return icons[key] || icons.temp;
            },
            getCardTheme(key) {
                // Unified theme
                return 'hover:border-emerald-200:border-emerald-900/50';
            },
            getCardGradient(key) {
                // Unified subtle gradient
                return 'background: linear-gradient(135deg, rgba(16, 185, 129, 0.05) 0%, rgba(16, 185, 129, 0.1) 100%)';
            },
            getIconBackground(key) {
                // Unified emerald background
                return 'background: linear-gradient(135deg, #10b981 0%, #059669 100%)';
            },
            getGaugeColor(key) {
                // Unified emerald color
                return '#10b981';
            },
            getLinearGradient(key) {
                // Unified emerald linear gradient
                return 'from-emerald-400 to-emerald-500';
            },
            computeTopMetrics() {
                // Temperature
                let temp = this.weatherSummary?.temp;
                if ((temp == null || temp === '-') && this.devices.length) {
                    const tempValues = this.devices.map(device => device.temperature_c).filter(value => value != null);
                    if (tempValues.length) temp = tempValues.reduce((accumulator, current) => accumulator + current,
                        0) / tempValues.length;
                }
                if (temp != null && temp !== '-') this.updateMetric('temp', parseFloat(temp), 'now');

                // Humidity
                const hum = this.weatherSummary?.humidity;
                if (hum != null && hum !== '-') this.updateMetric('humidity', parseFloat(hum), 'BMKG');

                // Light - Multiple fallback strategies
                let light = null;
                let lightSource = 'estimasi';

                // Priority 1: From weather summary
                if (this.weatherSummary?.light_pct != null) {
                    light = parseFloat(this.weatherSummary.light_pct);
                    lightSource = 'BMKG';
                }
                // Priority 2: Calculate from cloud cover (tcc)
                else if (this.weatherSummary?.tcc != null) {
                    const tcc = parseFloat(this.weatherSummary.tcc);
                    if (!isNaN(tcc)) {
                        light = Math.max(0, Math.min(100, 100 - tcc));
                        lightSource = 'cloud';
                    }
                }
                // Priority 3: From device sensors
                else if (this.devices.length) {
                    const luxValues = this.devices.map(device => device.light_lux).filter(value => value != null &&
                        value > 0);
                    if (luxValues.length) {
                        const avgLux = luxValues.reduce((accumulator, current) => accumulator + current, 0) / luxValues
                            .length;
                        light = Math.min(100, (avgLux / 12000) * 100);
                        lightSource = 'sensor';
                    }
                }

                // ✅ REMOVED Priority 4: Time-based estimation - No more dummy data
                // Light should only come from real sensors or weather API

                if (light != null) this.updateMetric('light', Math.round(light), lightSource);

                // Wind
                const ws = this.weatherSummary?.wind_speed;
                if (ws != null && ws !== '-') {
                    this.updateMetric('wind', parseFloat(ws), this.weatherSummary?.wind_dir || '');
                }

                // Rain - Multiple sources with fallback
                let rain = null;
                let rainDesc = 'tidak ada';

                // Priority 1: From weather summary
                if (this.weatherSummary?.rain != null) {
                    rain = parseFloat(this.weatherSummary.rain);
                }
                // Priority 2: From first forecast entry
                else if (this.forecastEntries && this.forecastEntries.length > 0) {
                    const firstForecast = this.forecastEntries[0];
                    if (firstForecast?.rain != null) {
                        rain = parseFloat(firstForecast.rain);
                    }
                }

                // Default to 0 if no data (means no rain)
                if (rain == null) rain = 0;

                // Set description
                if (rain > 0) {
                    rainDesc = rain > 5 ? 'lebat' : 'ringan';
                }

                this.updateMetric('rain', rain, rainDesc);

                // Tank
                if (this.tank?.percentage != null) this.updateMetric('tank', parseFloat(this.tank.percentage), 'level');

                // Battery average
                if (this.devices.length) {
                    const batteryPercentages = this.devices.map(device => {
                        if (device.battery_voltage_v == null) return null;
                        const voltage = parseFloat(device.battery_voltage_v);
                        if (isNaN(voltage)) return null;
                        return Math.max(0, Math.min(100, ((voltage - 3.3) / (4.2 - 3.3)) * 100));
                    }).filter(percentage => percentage != null);
                    if (batteryPercentages.length) {
                        const avgBattery = batteryPercentages.reduce((accumulator, current) => accumulator + current,
                            0) / batteryPercentages.length;
                        this.updateMetric('battery', avgBattery, batteryPercentages.length + ' node');
                    }
                }

                // Devices count
                this.updateMetric('devices', this.devices.length, 'online');

                // After metrics update ensure tooltips dataset refreshed
                this.refreshMetricTooltips();
            },
            // Environmental Charts Methods
            // ✅ REMOVED: generateSampleChartData() - No more dummy data
            // All chart data now comes from API: /api/v1/dashboard/charts
            
            initEnvironmentalCharts() {
                // ✅ Skip if CHART_FIX_ENABLED (avoid conflicts)
                if (window.CHART_FIX_ENABLED) {
                    console.log('⏭️ Skipping Alpine.js chart init - Using CHART-FIX instead');
                    return;
                }
                
                // ✅ Destroy existing charts first to prevent "Canvas already in use" error
                if (this.lightIntensityChart) {
                    this.lightIntensityChart.destroy();
                    this.lightIntensityChart = null;
                }
                if (this.waterLevelChart) {
                    this.waterLevelChart.destroy();
                    this.waterLevelChart = null;
                }
                if (this.soilMoistureChart) {
                    this.soilMoistureChart.destroy();
                    this.soilMoistureChart = null;
                }
                if (this.temperatureChart) {
                    this.temperatureChart.destroy();
                    this.temperatureChart = null;
                }
                if (this.humidityChart) {
                    this.humidityChart.destroy();
                    this.humidityChart = null;
                }

                // Initialize empty data structures
                this.soilMoistureSensors.forEach(sensor => {
                    this.soilMoistureData.sensors[sensor.id] = [];
                });

                // Initialize Light Intensity Chart
                const lightCtx = document.getElementById('lightIntensityChart');
                if (lightCtx) {
                    this.lightIntensityChart = new Chart(lightCtx, {
                        type: 'line',
                        data: {
                            labels: this.lightIntensityData.labels,
                            datasets: [{
                                label: 'LI2',
                                data: this.lightIntensityData.li2,
                                borderColor: '#22d3ee',
                                backgroundColor: 'rgba(34, 211, 238, 0.15)',
                                borderWidth: 3,
                                tension: 0.4,
                                pointRadius: 4,
                                pointBackgroundColor: '#22d3ee',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                fill: true
                            }, {
                                label: 'LI1',
                                data: this.lightIntensityData.li1,
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.15)',
                                borderWidth: 3,
                                tension: 0.4,
                                pointRadius: 4,
                                pointBackgroundColor: '#ef4444',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    titleColor: '#fff',
                                    bodyColor: '#fff',
                                    borderColor: '#22d3ee',
                                    borderWidth: 1
                                }
                            },
                            scales: {
                                x: {
                                    display: true,
                                    title: {
                                        display: true,
                                        text: 'Waktu',
                                        color: '#374151',
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        },
                                        padding: {
                                            top: 10,
                                            bottom: 0
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.1)',
                                        drawBorder: true,
                                        borderColor: '#d1d5db',
                                        lineWidth: 1
                                    },
                                    ticks: {
                                        color: '#374151',
                                        maxRotation: 0,
                                        autoSkip: true,
                                        maxTicksLimit: 8,
                                        font: {
                                            size: 11,
                                            weight: '500'
                                        }
                                    },
                                    border: {
                                        display: true,
                                        color: '#9ca3af'
                                    }
                                },
                                y: {
                                    display: true,
                                    title: {
                                        display: true,
                                        text: this.t('lightIntensity') + ' (' + this.t('lux') + ')',
                                        color: '#374151',
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        },
                                        padding: {
                                            bottom: 10
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.1)',
                                        drawBorder: true,
                                        borderColor: '#d1d5db',
                                        lineWidth: 1
                                    },
                                    ticks: {
                                        color: '#374151',
                                        callback: function(value) {
                                            return value.toLocaleString();
                                        },
                                        font: {
                                            size: 11,
                                            weight: '500'
                                        },
                                        padding: 8
                                    },
                                    border: {
                                        display: true,
                                        color: '#9ca3af'
                                    },
                                    min: 0
                                }
                            },
                            interaction: {
                                mode: 'nearest',
                                axis: 'x',
                                intersect: false
                            }
                        }
                    });
                }

                // Initialize Water Level Chart
                const waterCtx = document.getElementById('waterLevelChart');
                if (waterCtx) {
                    this.waterLevelChart = new Chart(waterCtx, {
                        type: 'line',
                        data: {
                            labels: this.waterLevelData.labels,
                            datasets: [{
                                label: 'WL',
                                data: this.waterLevelData.levels,
                                borderColor: '#84cc16',
                                backgroundColor: 'rgba(132, 204, 22, 0.15)',
                                borderWidth: 3,
                                tension: 0.4,
                                pointRadius: 4,
                                pointBackgroundColor: '#84cc16',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    titleColor: '#fff',
                                    bodyColor: '#fff',
                                    borderColor: '#84cc16',
                                    borderWidth: 1
                                }
                            },
                            scales: {
                                x: {
                                    display: true,
                                    title: {
                                        display: true,
                                        text: 'Waktu',
                                        color: '#374151',
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        },
                                        padding: {
                                            top: 10,
                                            bottom: 0
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.1)',
                                        drawBorder: true,
                                        borderColor: '#d1d5db',
                                        lineWidth: 1
                                    },
                                    ticks: {
                                        color: '#374151',
                                        maxRotation: 0,
                                        autoSkip: true,
                                        maxTicksLimit: 8,
                                        font: {
                                            size: 11,
                                            weight: '500'
                                        }
                                    },
                                    border: {
                                        display: true,
                                        color: '#9ca3af'
                                    }
                                },
                                y: {
                                    display: true,
                                    title: {
                                        display: true,
                                        text: 'Ketinggian Air (cm)',
                                        color: '#374151',
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        },
                                        padding: {
                                            bottom: 10
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.1)',
                                        drawBorder: true,
                                        borderColor: '#d1d5db',
                                        lineWidth: 1
                                    },
                                    ticks: {
                                        color: '#374151',
                                        callback: function(value) {
                                            return value.toFixed(1);
                                        },
                                        font: {
                                            size: 11,
                                            weight: '500'
                                        },
                                        padding: 8
                                    },
                                    border: {
                                        display: true,
                                        color: '#9ca3af'
                                    },
                                    min: 0
                                }
                            },
                            interaction: {
                                mode: 'nearest',
                                axis: 'x',
                                intersect: false
                            }
                        }
                    });
                }

                // Initialize Soil Moisture Chart
                const soilCtx = document.getElementById('soilMoistureChart');
                if (soilCtx) {
                    const datasets = this.soilMoistureSensors.map(sensor => {
                        // Convert hex to rgba to avoid circular reference
                        const hexColor = String(sensor.color);
                        const r = parseInt(hexColor.slice(1, 3), 16);
                        const g = parseInt(hexColor.slice(3, 5), 16);
                        const b = parseInt(hexColor.slice(5, 7), 16);
                        const bgColor = `rgba(${r}, ${g}, ${b}, 0.1)`;
                        
                        return {
                            label: sensor.label,
                            data: this.soilMoistureData.sensors[sensor.id],
                            borderColor: hexColor,
                            backgroundColor: bgColor,
                            borderWidth: 2,
                            tension: 0.3,
                            pointRadius: 2,
                            pointBackgroundColor: hexColor,
                            fill: false
                        };
                    });

                    this.soilMoistureChart = new Chart(soilCtx, {
                        type: 'line',
                        data: {
                            labels: this.soilMoistureData.labels,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    backgroundColor: 'rgba(0, 0, 0, 0.9)',
                                    titleColor: '#fff',
                                    bodyColor: '#fff'
                                }
                            },
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Waktu',
                                        color: '#374151',
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        },
                                        padding: {
                                            top: 10,
                                            bottom: 0
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.1)',
                                        drawBorder: true,
                                        borderColor: '#d1d5db'
                                    },
                                    ticks: {
                                        color: '#374151',
                                        maxTicksLimit: 10,
                                        font: {
                                            size: 10,
                                            weight: '500'
                                        }
                                    },
                                    border: {
                                        display: true,
                                        color: '#9ca3af'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Kelembapan Tanah (%)',
                                        color: '#374151',
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        },
                                        padding: {
                                            bottom: 10
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.1)',
                                        drawBorder: true,
                                        borderColor: '#d1d5db'
                                    },
                                    ticks: {
                                        color: '#374151',
                                        font: {
                                            size: 10,
                                            weight: '500'
                                        }
                                    },
                                    border: {
                                        display: true,
                                        color: '#9ca3af'
                                    },
                                    min: 0,
                                    max: 100
                                }
                            }
                        }
                    });
                }

                // Initialize Temperature Chart
                const tempCtx = document.getElementById('temperatureChart');
                if (tempCtx) {
                    this.temperatureChart = new Chart(tempCtx, {
                        type: 'line',
                        data: {
                            labels: this.temperatureData.labels,
                            datasets: [{
                                label: 'T1',
                                data: this.temperatureData.t1,
                                borderColor: '#a855f7',
                                backgroundColor: 'rgba(168, 85, 247, 0.2)',
                                borderWidth: 3,
                                tension: 0.4,
                                pointRadius: 3,
                                pointBackgroundColor: '#a855f7',
                                fill: true
                            }, {
                                label: 'T2',
                                data: this.temperatureData.t2,
                                borderColor: '#22d3ee',
                                backgroundColor: 'rgba(34, 211, 238, 0.2)',
                                borderWidth: 3,
                                tension: 0.4,
                                pointRadius: 3,
                                pointBackgroundColor: '#22d3ee',
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    backgroundColor: 'rgba(0, 0, 0, 0.9)'
                                }
                            },
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Waktu',
                                        color: '#374151',
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        },
                                        padding: {
                                            top: 10,
                                            bottom: 0
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.1)',
                                        drawBorder: true,
                                        borderColor: '#d1d5db'
                                    },
                                    ticks: {
                                        color: '#374151',
                                        maxTicksLimit: 8,
                                        font: {
                                            size: 10,
                                            weight: '500'
                                        }
                                    },
                                    border: {
                                        display: true,
                                        color: '#9ca3af'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Suhu (°C)',
                                        color: '#374151',
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        },
                                        padding: {
                                            bottom: 10
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.1)',
                                        drawBorder: true,
                                        borderColor: '#d1d5db'
                                    },
                                    ticks: {
                                        color: '#374151',
                                        font: {
                                            size: 10,
                                            weight: '500'
                                        },
                                        callback: function(value) {
                                            return value + '°C';
                                        }
                                    },
                                    border: {
                                        display: true,
                                        color: '#9ca3af'
                                    },
                                    min: 20,
                                    max: 35
                                }
                            }
                        }
                    });
                }

                // Initialize Humidity Chart
                const humCtx = document.getElementById('humidityChart');
                if (humCtx) {
                    this.humidityChart = new Chart(humCtx, {
                        type: 'line',
                        data: {
                            labels: this.humidityData.labels,
                            datasets: [{
                                label: 'H2',
                                data: this.humidityData.h2,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                                borderWidth: 3,
                                tension: 0.4,
                                pointRadius: 3,
                                pointBackgroundColor: '#3b82f6',
                                fill: true
                            }, {
                                label: 'H1',
                                data: this.humidityData.h1,
                                borderColor: '#f97316',
                                backgroundColor: 'rgba(249, 115, 22, 0.2)',
                                borderWidth: 3,
                                tension: 0.4,
                                pointRadius: 3,
                                pointBackgroundColor: '#f97316',
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    backgroundColor: 'rgba(0, 0, 0, 0.9)'
                                }
                            },
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Waktu',
                                        color: '#374151',
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        },
                                        padding: {
                                            top: 10,
                                            bottom: 0
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.1)',
                                        drawBorder: true,
                                        borderColor: '#d1d5db'
                                    },
                                    ticks: {
                                        color: '#374151',
                                        maxTicksLimit: 8,
                                        font: {
                                            size: 10,
                                            weight: '500'
                                        }
                                    },
                                    border: {
                                        display: true,
                                        color: '#9ca3af'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Kelembapan Udara (%)',
                                        color: '#374151',
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        },
                                        padding: {
                                            bottom: 10
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.1)',
                                        drawBorder: true,
                                        borderColor: '#d1d5db'
                                    },
                                    ticks: {
                                        color: '#374151',
                                        font: {
                                            size: 10,
                                            weight: '500'
                                        },
                                        callback: function(value) {
                                            return value + '%';
                                        }
                                    },
                                    border: {
                                        display: true,
                                        color: '#9ca3af'
                                    },
                                    min: 30,
                                    max: 65
                                }
                            }
                        }
                    });
                }
            },
            updateEnvironmentalCharts() {
                const now = new Date();
                const timeLabel = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });

                // Get light intensity from multiple sources
                const devices = this.devices || [];
                let hasLightData = false;
                let li1 = null;
                let li2 = null;

                // Priority 1: Try to get from devices
                if (devices.length > 0) {
                    // Try to get from first device
                    if (devices[0]?.light_lux != null && devices[0].light_lux > 0) {
                        li1 = devices[0].light_lux;
                        hasLightData = true;
                    }

                    // Try to get from second device if exists
                    if (devices.length > 1 && devices[1]?.light_lux != null && devices[1].light_lux > 0) {
                        li2 = devices[1].light_lux;
                        hasLightData = true;
                    }
                }

                // Priority 2: If no device data, use BMKG weather data (light_pct)
                if (!hasLightData && this.weatherSummary?.light_pct != null) {
                    // Convert light percentage to lux equivalent (estimate)
                    // Assuming max daylight = 12000 lux
                    const lightPct = parseFloat(this.weatherSummary.light_pct);
                    li1 = (lightPct / 100) * 12000;
                    li2 = li1 * 0.92; // Slight variation for visualization
                    hasLightData = true;
                }

                // Fallback: Create slight variation if only one value exists
                if (hasLightData) {
                    if (li1 != null && li2 == null) {
                        li2 = li1 * 0.95;
                    } else if (li2 != null && li1 == null) {
                        li1 = li2 * 1.05;
                    }

                    // Update chart data
                    this.lightIntensityData.labels.push(timeLabel);
                    this.lightIntensityData.li1.push(li1);
                    this.lightIntensityData.li2.push(li2);

                    // Keep only last N points
                    if (this.lightIntensityData.labels.length > this.chartMaxPoints) {
                        this.lightIntensityData.labels.shift();
                        this.lightIntensityData.li1.shift();
                        this.lightIntensityData.li2.shift();
                    }

                    // Update chart
                    if (this.lightIntensityChart) {
                        this.lightIntensityChart.update('none');
                    }
                }

                // Get water level from tank or water height
                let waterLevel = null;

                // Try multiple sources for water level
                if (this.tank?.water_level_cm != null && this.tank.water_level_cm > 0) {
                    waterLevel = this.tank.water_level_cm;
                } else if (devices.length > 0 && devices[0]?.water_height_cm != null && devices[0].water_height_cm >
                    0) {
                    waterLevel = devices[0].water_height_cm;
                } else if (this.tank?.percentage != null && this.tank.percentage > 0) {
                    waterLevel = (this.tank.percentage / 100 * 150); // Convert percentage to cm
                }

                // Only update if we have valid water level data
                if (waterLevel != null && waterLevel > 0) {
                    this.waterLevelData.labels.push(timeLabel);
                    this.waterLevelData.levels.push(waterLevel);

                    // Keep only last N points
                    if (this.waterLevelData.labels.length > this.chartMaxPoints) {
                        this.waterLevelData.labels.shift();
                        this.waterLevelData.levels.shift();
                    }

                    // Update chart
                    if (this.waterLevelChart) {
                        this.waterLevelChart.update('none');
                    }
                }

                // Debug log to check data
                console.log('Chart Update:', {
                    time: timeLabel,
                    lightData: hasLightData,
                    lightSource: li1 != null ? (devices[0]?.light_lux ? 'device' : 'bmkg') : 'none',
                    li1: li1,
                    li2: li2,
                    li1Count: this.lightIntensityData.li1.length,
                    waterLevel: waterLevel,
                    wlCount: this.waterLevelData.levels.length,
                    devices: devices.length,
                    weatherLight: this.weatherSummary?.light_pct,
                    tankPercent: this.tank?.percentage
                });
            },
            clearChart(type) {
                if (type === 'light') {
                    this.lightIntensityData.labels = [];
                    this.lightIntensityData.li1 = [];
                    this.lightIntensityData.li2 = [];
                    if (this.lightIntensityChart) {
                        this.lightIntensityChart.update();
                    }
                } else if (type === 'water') {
                    this.waterLevelData.labels = [];
                    this.waterLevelData.levels = [];
                    if (this.waterLevelChart) {
                        this.waterLevelChart.update();
                    }
                } else if (type === 'soilMoisture') {
                    this.soilMoistureData.labels = [];
                    this.soilMoistureSensors.forEach(sensor => {
                        this.soilMoistureData.sensors[sensor.id] = [];
                    });
                    if (this.soilMoistureChart) {
                        this.soilMoistureChart.update();
                    }
                } else if (type === 'temperature') {
                    this.temperatureData.labels = [];
                    this.temperatureData.t1 = [];
                    this.temperatureData.t2 = [];
                    if (this.temperatureChart) {
                        this.temperatureChart.update();
                    }
                } else if (type === 'humidity') {
                    this.humidityData.labels = [];
                    this.humidityData.h1 = [];
                    this.humidityData.h2 = [];
                    if (this.humidityChart) {
                        this.humidityChart.update();
                    }
                }
            },
            refreshMetricTooltips() {
                // Attach title attribute dynamically to overlay chips (executed after DOM paint)
                this.$nextTick(() => {
                    document.querySelectorAll('[data-metric-chip]').forEach(el => {
                        const metricKey = el.getAttribute('data-metric-chip');
                        const snap = this.metricSnapshots[metricKey];
                        if (snap) {
                            el.title =
                                `${snap.value} • ${snap.ts.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'})}`;
                        }
                    });
                });
            },
            openFullMap() {
                this.showFullMap = true;
                this.$nextTick(() => this.initLeafletFull());
            },
            closeFullMap() {
                this.showFullMap = false;
            },
            initLeaflet() {
                if (this.leafletInited || !window.L) return;
                const map = L.map('leafletMap', {
                    zoomControl: true,
                    attributionControl: false
                }).setView([this.villageCenter.lat, this.villageCenter.lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19
                }).addTo(map);
                // polygon
                const poly = L.polygon(this.villagePolygon, {
                    color: '#16a34a',
                    weight: 2,
                    fillOpacity: 0.08
                }).addTo(map);
                L.marker([this.villageCenter.lat, this.villageCenter.lng], {
                    title: 'Lokasi'
                }).addTo(map);
                map.fitBounds(poly.getBounds(), {
                    padding: [20, 20]
                });

                // Force low z-index for leaflet container to prevent modal overlap
                setTimeout(() => {
                    const container = document.getElementById('leafletMap');
                    if (container) {
                        const leafletContainer = container.querySelector('.leaflet-container');
                        if (leafletContainer) {
                            leafletContainer.style.zIndex = '1';
                        }
                    }
                }, 100);

                this.leafletInited = true;
            },
            initLeafletFull() {
                if (this.leafletFullInited || !window.L) return;
                const map = L.map('leafletMapFull', {
                    zoomControl: true
                }).setView([this.villageCenter.lat, this.villageCenter.lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19
                }).addTo(map);
                const poly = L.polygon(this.villagePolygon, {
                    color: '#15803d',
                    weight: 2,
                    fillOpacity: 0.1
                }).addTo(map);
                L.marker([this.villageCenter.lat, this.villageCenter.lng], {
                    icon: L.divIcon({ className: 'bg-green-600 rounded-full w-4 h-4 border-2 border-white shadow-lg' })
                }).bindPopup('Pusat Lahan').addTo(map);
                this.leafletFullInited = true;
            },
            async loadDevices() {
                this.loadingDevices = true;
                try {
                    const jsonData = await this.fetchWithCache('/api/v1/dashboard/devices', 'cache_devices');
                    
                    console.log('📊 Devices loaded from API:', jsonData.data?.length || 0);
                    
                    this.devices = (jsonData.data || jsonData || []).map(device => ({
                        // Device identification
                        id: device.device_id,
                        device_id: device.device_id,
                        device_name: device.device_name || `Node ${device.plot_number}`,
                        plot_number: device.plot_number,
                        location: device.location || '',
                        
                        // Treatment info (for card header description and details)
                        treatment_description: device.treatment_description || 'Perlakuan optimal',
                        treatment_type: device.treatment_type,
                        treatment_code: device.treatment_code,
                        fc_target: device.fc_target ? parseFloat(device.fc_target) : null, // FC Target percentage (80.00%)
                        threshold: device.threshold ? parseFloat(device.threshold) : null, // Threshold percentage (17.70%)
                        threshold_adc: device.threshold_adc, // ADC value (678)
                        
                        // Sensor data - use exact field names from API
                        soil_moisture_pct: device.soil_moisture_pct,
                        temperature_c: device.temperature_c,
                        soil_temp_c: device.soil_temp_c,
                        air_temp_c: device.air_temp_c,
                        air_humidity_pct: device.air_humidity_pct,
                        light_lux: device.light_lux,
                        water_height_cm: device.water_height_cm,
                        
                        // Battery - support both field names
                        battery_voltage: device.battery_voltage || device.battery_voltage_v,
                        battery_voltage_v: device.battery_voltage_v || device.battery_voltage,
                        battery_percentage: device.battery_percentage,
                        
                        // Signal strength
                        signal_strength_rssi: device.signal_strength_rssi,
                        signal_strength_pct: device.signal_strength_pct,
                        
                        // Device status
                        connection_state: device.connection_state || device.connection_status || 'offline',
                        connection_status: device.connection_status || device.connection_state || 'offline',
                        valve_state: device.valve_state || device.valve_status || 'closed',
                        valve_status: device.valve_status || device.valve_state || 'closed',
                        is_active: device.is_active,
                        status: device.status || 'normal',
                        
                        // Water usage
                        water_usage_today_l: device.water_usage_today_l ? parseFloat(device.water_usage_today_l) : null,
                        
                        // Timestamps
                        recorded_at: device.recorded_at || device.last_seen,
                        last_seen: device.last_seen || device.recorded_at
                    }));
                    
                    console.log('✅ Devices mapped:', this.devices.map(d => ({
                        id: d.device_id,
                        plot: d.plot_number,
                        name: d.device_name,
                        moisture: d.soil_moisture_pct,
                        temp: d.air_temp_c,
                        connection: d.connection_state
                    })));
                    
                    this.computeTopMetrics();
                } catch (error) {
                    console.error('Device fetch error', error);
                    this.fetchError = true;
                } finally {
                    this.loadingDevices = false;
                }
            },
            async loadDeviceDetail(deviceId) {
                this.loadingDeviceDetail = true;
                this.deviceSessions = [];
                this.deviceUsageHistory = [];
                try {
                    const [sessionsResp, historyResp] = await Promise.all([
                        fetch(`/api/devices/${deviceId}/irrigation/sessions`),
                        fetch(`/api/devices/${deviceId}/usage-history`)
                    ]);
                    if (sessionsResp.ok) {
                        const js = await sessionsResp.json();
                        // Backend returns { sessions: [...], summary: {...} }
                        this.deviceSessions = js.sessions || [];
                        this.deviceSessionsSummary = js.summary || null;
                        this.buildTasks();
                    }
                    if (historyResp.ok) {
                        const jh = await historyResp.json();
                        // Backend returns { history: [...] }
                        this.deviceUsageHistory = jh.history || [];
                    }
                } catch (e) {
                    console.error('Device detail error', e);
                } finally {
                    this.loadingDeviceDetail = false;
                }
            },
            openDeviceModal(d) {
                this.selectedDevice = d;
                this.showDeviceModal = true;
                // Use numeric id if available for route model binding
                const key = d.id || d.device_id;
                this.loadDeviceDetail(key);
            },
            closeDeviceModal() {
                this.showDeviceModal = false;
                this.selectedDevice = null;
                this.deviceSessions = [];
                this.deviceUsageHistory = [];
            },
            async loadTank() {
                this.loadingTank = true;
                try {
                    const jsonData = await this.fetchWithCache('/api/v1/dashboard/tank', 'cache_tank');
                    const tankData = jsonData.data || jsonData;
                    if (tankData) {
                        this.tank = {
                            id: tankData.id,
                            tank_name: tankData.name || tankData.tank_name || 'Tangki Air',
                            current_volume_liters: parseFloat(tankData.water_level_cm || tankData
                                .current_volume_liters || 0),
                            capacity_liters: parseFloat(tankData.capacity || tankData.capacity_liters || 200),
                            percentage: parseFloat(tankData.percentage || 0),
                            water_level_cm: parseFloat(tankData.water_level_cm || 0),
                            status: tankData.status || 'normal'
                        };
                        this.tankUpdatedAt = new Date().toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        this.computeTopMetrics();
                    }
                } catch (e) {
                    console.error('Tank fetch error', e);
                    this.fetchError = true;
                } finally {
                    this.loadingTank = false;
                }
            },
            async loadPlan() {
                this.loadingSchedule = true;
                try {
                    const jsonData = await this.fetchWithCache('/api/v1/dashboard/schedule', 'cache_schedule');
                    if (jsonData.data || jsonData) {
                        this.plan = jsonData.data || jsonData;
                        // Plan currently not represented as metric gauge; could be added later
                        this.buildTasks();
                    }
                } catch (e) {
                    console.error('Plan fetch error', e);
                    this.fetchError = true;
                } finally {
                    this.loadingSchedule = false;
                }
            },
            async loadUsage() {
                this.loadingUsage = true;
                try {
                    const jsonData = await this.fetchWithCache('/api/v1/dashboard/usage', 'cache_usage');
                    // ✅ Convert to plain array BEFORE assigning to Alpine
                    const rawData = jsonData.data || jsonData || [];
                    this.usage = rawData.map(item => ({
                        date: item.date || item.usage_date,
                        usage_date: item.usage_date || item.date,
                        total_l: parseFloat(item.liters || item.total_l) || 0
                    }));
                    this.renderUsageChart30d();
                } catch (error) {
                    console.error('Usage fetch error', error);
                    // ✅ NO FALLBACK - Show error state instead of fake data
                    this.usage = [];
                    this.renderUsageChart30d();
                } finally {
                    this.loadingUsage = false;
                }
            },
            async loadUsageDaily() {
                try {
                    const jsonData = await this.fetchWithCache('/api/v1/dashboard/usage/daily', 'cache_usage_daily');
                    // ✅ Convert to plain array BEFORE assigning to Alpine
                    const rawData = jsonData.data || jsonData || [];
                    this.usage24h = rawData.map(item => ({
                        hour: item.hour,
                        total_l: parseFloat(item.liters || item.total_l) || 0,
                        datetime: item.datetime || item.hour
                    }));
                    this.renderUsageChart24h();
                } catch (error) {
                    console.error('24h Usage fetch error', error);
                    // ✅ NO FALLBACK - Show error state instead of fake data
                    this.usage24h = [];
                    this.renderUsageChart24h();
                }
            },
            // ✅ REMOVED: generateMock24hData() - No more dummy data
            // All 24-hour usage data now comes from API: /api/v1/dashboard/usage/daily
            
            // ✅ REMOVED: generateMock30dData() - No more dummy data
            // All 30-day usage data now comes from API: /api/v1/dashboard/usage
            
            // Helper for SWR (Stale-While-Revalidate) Cache
            async fetchWithCache(url, key, expiryMs = 30000) {
                const cached = sessionStorage.getItem(key);
                let parsedCache = null;
                if (cached) {
                    try {
                        const data = JSON.parse(cached);
                        if (Date.now() - data.ts < expiryMs) {
                            // Valid cache, return immediately but fetch in background to revalidate
                            setTimeout(() => this.revalidateCache(url, key), 100);
                            return data.payload;
                        }
                        parsedCache = data.payload; // Return stale data temporarily
                    } catch (e) {
                        sessionStorage.removeItem(key);
                    }
                }
                
                // If we have stale data, return it but revalidate in background
                if (parsedCache) {
                    setTimeout(() => this.revalidateCache(url, key), 10);
                    return parsedCache;
                }

                // Otherwise fetch fresh
                const res = await fetch(url);
                if (!res.ok) throw new Error('API fetch failed');
                const json = await res.json();
                sessionStorage.setItem(key, JSON.stringify({ ts: Date.now(), payload: json }));
                return json;
            },
            
            async revalidateCache(url, key) {
                try {
                    const res = await fetch(url);
                    if (res.ok) {
                        const json = await res.json();
                        sessionStorage.setItem(key, JSON.stringify({ ts: Date.now(), payload: json }));
                    }
                } catch(e) { }
            },

            async loadEssential(force = false) {
                if (this.loadingAll && !force) return;
                this.loadingAll = true;
                this.fetchError = false;

                try {
                    await Promise.all([
                        this.loadDevices(),
                        this.loadEnvStats()
                    ]);

                    this.computeLightWindFromDevices();
                    this.lastUpdated = new Date();
                    this.computeTopMetrics();
                } catch (error) {
                    console.error('Load essential error:', error);
                    this.fetchError = true;
                } finally {
                    this.loadingAll = false;
                }
            },
            
            // Backwards compatibility for templates calling loadAll()
            async loadAll(force = false) {
                await this.loadEssential(force);
            },
            async loadChartData() {
                this.loadingCharts = true;
                // ✅ Skip if CHART_FIX_ENABLED (avoid conflicts)
                if (window.CHART_FIX_ENABLED) {
                    console.log('⏭️ Skipping Alpine.js chart data load - Using CHART-FIX instead');
                    this.loadingCharts = false;
                    return;
                }
                
                try {
                    // Fetch latest chart data (7 days for better overview)
                    const chartData = await this.fetchWithCache('/api/v1/dashboard/charts?type=all&days=7', 'cache_charts');

                    console.log('✅ Chart data loaded successfully:', {
                        light: chartData.light?.length || 0,
                        water: chartData.water?.length || 0,
                        soil: chartData.soil?.length || 0,
                        temp: chartData.temperature?.length || 0,
                        humidity: chartData.humidity?.length || 0
                    });

                    // ✅ Update light intensity data (API returns: {time, radiation})
                    const lightData = chartData.light || [];
                    
                    if (lightData.length > 0) {
                        this.lightIntensityData.labels = [];
                        this.lightIntensityData.li1 = [];
                        this.lightIntensityData.li2 = [];

                        lightData.forEach(item => {
                            this.lightIntensityData.labels.push(item.time);
                            const rad = parseFloat(item.radiation) || 0;
                            this.lightIntensityData.li1.push(rad);
                            this.lightIntensityData.li2.push(rad);
                        });

                        if (this.lightIntensityChart) {
                            this.lightIntensityChart.data.labels = this.lightIntensityData.labels;
                            this.lightIntensityChart.data.datasets[0].data = this.lightIntensityData.li2;
                            this.lightIntensityChart.data.datasets[1].data = this.lightIntensityData.li1;
                            this.lightIntensityChart.update('active');
                            // Force resize to trigger re-render
                            setTimeout(() => {
                                if (this.lightIntensityChart) {
                                    this.lightIntensityChart.resize();
                                }
                            }, 100);
                            console.log('✅ Light Intensity chart updated with', this.lightIntensityData.labels.length, 'points');
                        }
                    }

                    // ✅ Update water level data (API returns: {time, level})
                    const waterData = chartData.water || [];
                    
                    if (waterData.length > 0) {
                        this.waterLevelData.labels = [];
                        this.waterLevelData.levels = [];

                        waterData.forEach(item => {
                            if (item.level != null) {
                                this.waterLevelData.labels.push(item.time);
                                this.waterLevelData.levels.push(parseFloat(item.level));
                            }
                        });

                        if (this.waterLevelChart) {
                            this.waterLevelChart.data.labels = this.waterLevelData.labels;
                            this.waterLevelChart.data.datasets[0].data = this.waterLevelData.levels;
                            this.waterLevelChart.update('active');
                            // Force resize to trigger re-render
                            setTimeout(() => {
                                if (this.waterLevelChart) {
                                    this.waterLevelChart.resize();
                                }
                            }, 100);
                            console.log('✅ Water Level chart updated with', this.waterLevelData.labels.length, 'points');
                        }
                    }

                    // ✅ Update soil moisture data (API returns: {time, average})
                    const soilData = chartData.soil || [];
                    
                    if (soilData.length > 0) {
                        this.soilMoistureData.labels = [];
                        this.soilMoistureSensors.forEach(sensor => {
                            this.soilMoistureData.sensors[sensor.id] = [];
                        });

                        soilData.forEach(item => {
                            this.soilMoistureData.labels.push(item.time);
                            const avg = parseFloat(item.average) || 0;
                            
                            this.soilMoistureSensors.forEach((sensor) => {
                                this.soilMoistureData.sensors[sensor.id].push(avg);
                            });
                        });

                        if (this.soilMoistureChart) {
                            this.soilMoistureChart.data.labels = this.soilMoistureData.labels;
                            this.soilMoistureChart.data.datasets.forEach((dataset, idx) => {
                                const sensorId = this.soilMoistureSensors[idx].id;
                                dataset.data = this.soilMoistureData.sensors[sensorId];
                            });
                            this.soilMoistureChart.update('active');
                            // Force resize to trigger re-render
                            setTimeout(() => {
                                if (this.soilMoistureChart) {
                                    this.soilMoistureChart.resize();
                                }
                            }, 100);
                            console.log('✅ Soil Moisture chart updated with', this.soilMoistureData.labels.length, 'points');
                        }
                    }

                    // ✅ Update temperature data (API returns: {time, soil_temp})
                    const tempData = chartData.temperature || [];
                    
                    if (tempData.length > 0) {
                        this.temperatureData.labels = [];
                        this.temperatureData.t1 = [];
                        this.temperatureData.t2 = [];

                        tempData.forEach(item => {
                            this.temperatureData.labels.push(item.time);
                            const temp = parseFloat(item.soil_temp) || 0;
                            this.temperatureData.t1.push(temp);
                            this.temperatureData.t2.push(temp);
                        });

                        if (this.temperatureChart) {
                            this.temperatureChart.data.labels = this.temperatureData.labels;
                            this.temperatureChart.data.datasets[0].data = this.temperatureData.t1;
                            this.temperatureChart.data.datasets[1].data = this.temperatureData.t2;
                            this.temperatureChart.update('active');
                            // Force resize to trigger re-render
                            setTimeout(() => {
                                if (this.temperatureChart) {
                                    this.temperatureChart.resize();
                                }
                            }, 100);
                            console.log('✅ Temperature chart updated with', this.temperatureData.labels.length, 'points');
                        }
                    }

                    // ✅ Update humidity data (API returns: {time, humidity})
                    const humidityData = chartData.humidity || [];
                    
                    if (humidityData.length > 0) {
                        this.humidityData.labels = [];
                        this.humidityData.h1 = [];
                        this.humidityData.h2 = [];

                        humidityData.forEach(item => {
                            this.humidityData.labels.push(item.time);
                            const hum = parseFloat(item.humidity) || 0;
                            this.humidityData.h1.push(hum);
                            this.humidityData.h2.push(hum);
                        });

                        if (this.humidityChart) {
                            this.humidityChart.data.labels = this.humidityData.labels;
                            this.humidityChart.data.datasets[0].data = this.humidityData.h2;
                            this.humidityChart.data.datasets[1].data = this.humidityData.h1;
                            this.humidityChart.update('active');
                            // Force resize to trigger re-render
                            setTimeout(() => {
                                if (this.humidityChart) {
                                    this.humidityChart.resize();
                                }
                            }, 100);
                            console.log('✅ Humidity chart updated with', this.humidityData.labels.length, 'points');
                        }
                    }

                    console.log('✅ All charts updated successfully');

                } catch (error) {
                    console.error('❌ Chart data fetch error - charts will remain empty', error);
                } finally {
                    this.loadingCharts = false;
                }
            },
            computeLightWindFromDevices() {
                if (!this.devices.length) return;
                const luxValues = this.devices.map(device => device.light_lux).filter(value => value != null);
                const windValues = this.devices.map(device => device.wind_speed_ms).filter(value => value != null);
                if (luxValues.length) {
                    const avgLux = Math.round(luxValues.reduce((accumulator, current) => accumulator + current, 0) /
                        luxValues.length);
                    this.updateMetric('light', avgLux, `avg ${luxValues.length}`);
                }
                if (windValues.length) {
                    const maxWind = Math.max(...windValues);
                    this.updateMetric('wind', (Math.round(maxWind * 10) / 10), 'max');
                }
            },
            async loadEnvStats() {
                this.loadingWeather = true;
                try {
                    let weatherPromise = this.fetchWithCache('/api/v1/dashboard/weather', 'cache_weather')
                        .then(data => {
                            const weatherData = data.data || data;
                            if (weatherData) {
                                // Build weather summary
                                this.weatherSummary = {
                                    temp: weatherData.temp ?? '-',
                                    label: weatherData.label || 'Cerah',
                                    humidity: weatherData.humidity ?? '-',
                                    wind_speed: weatherData.wind_speed ?? '-',
                                    wind_dir: weatherData.wind_dir ?? '',
                                    rain: weatherData.rain ?? 0,
                                    light_pct: weatherData.light_pct,
                                    tcc: weatherData.tcc,
                                    icon: weatherData.icon || null,
                                    time: weatherData.time || new Date().toLocaleTimeString('id-ID', {
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    })
                                };

                                // Update metrics from weather data
                                if (weatherData.temp != null) this.updateMetric('temp', parseFloat(weatherData.temp), 'now');
                                if (weatherData.humidity != null) this.updateMetric('humidity', parseFloat(weatherData.humidity), 'BMKG');
                                if (weatherData.light_pct != null) this.updateMetric('light', Math.round(weatherData.light_pct), 'BMKG');
                                if (weatherData.wind_speed != null) this.updateMetric('wind', parseFloat(weatherData.wind_speed), weatherData.wind_dir || '');
                                if (weatherData.rain != null) this.updateMetric('rain', parseFloat(weatherData.rain),
                                    weatherData.rain > 0 ? (weatherData.rain > 5 ? 'lebat' : 'ringan') : 'tidak ada'
                                );

                                this.computeTopMetrics();
                            }
                        })
                        .catch(error => {
                            console.warn('Weather API not available', error);
                        });

                    // ALWAYS load BMKG forecast data for weekly view
                    const bmkgPromise = this.loadBMKGDirect();

                    await Promise.allSettled([weatherPromise, bmkgPromise]);
                } finally {
                    this.loadingWeather = false;
                }
            },
            async loadBMKGDirect() {
                console.log('🌤️ Loading BMKG forecast data...');
                
                try {
                    const data = await this.fetchWithCache('/api/bmkg/forecast', 'cache_bmkg', 600000); // 10 minutes cache

                    
                    console.log('✅ BMKG proxy API response received');
                    let first = null;
                    let entries = [];
                    if (Array.isArray(data) && data.length) entries = data;
                    else if (data && Array.isArray(data.entries)) entries = data.entries;
                    if (entries.length) {
                        this.processForecast(entries);
                        first = entries[0];
                        if (first) this.applyWeatherEntry(first);
                    }
                } catch (error) {
                    console.log('⚠️ BMKG proxy failed, trying direct BMKG API...');
                    try {
                        const response = await fetch('https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4=32.08.10.2001');
                        const raw = await response.json();
                        
                        console.log('✅ Direct BMKG API response received');
                        const blocks = raw?.data?.[0]?.cuaca;
                        if (Array.isArray(blocks)) {
                            const flat = [];
                            blocks.forEach(block => Array.isArray(block) && block.forEach(entry => flat.push(entry)));
                            flat.sort((a, b) => new Date(a.local_datetime) - new Date(b.local_datetime));
                            if (flat.length) {
                                this.processForecast(flat);
                                this.applyWeatherEntry(flat[0]);
                            }
                        }
                    } catch (e) {
                        console.warn('weather parse', e);
                    }
                }
            },
            processForecast(list) {
                console.log('📊 Processing forecast data, entries count:', list?.length);
                
                // Normalize & store
                this.forecastEntries = list.map(entry => ({
                    local_datetime: entry.local_datetime || entry.datetime || null,
                    temp: entry.t ?? entry.temperature_c,
                    humidity: entry.humidity ?? entry.hu ?? entry.h,
                    rain: entry.rain ?? entry.tp ?? null,
                    label: this.translateWeather(entry.weather_desc || entry.weather_desc_en || entry
                        .weather_desc_id ||
                        entry.weather),
                    icon: entry.weather_icon || entry.image || null,
                    wind_speed: entry.wind_speed_ms ?? entry.ws ?? null,
                    wind_dir: entry.wind_dir_cardinal || entry.wd || null,
                    tcc: entry.tcc ?? null
                })).filter(entry => entry.local_datetime);
                // 24h slice
                const now = Date.now();
                this.forecast24h = this.forecastEntries.filter(entry => new Date(entry.local_datetime) - now < 24 *
                        3600 * 1000)
                    .slice(0, 12).map(entry => ({
                        ...entry,
                        hour: new Date(entry.local_datetime).toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        })
                    }));
                // Weekly group (by date)
                const map = {};
                this.forecastEntries.forEach(entry => {
                    const date = new Date(entry.local_datetime);
                    const key = date.toISOString().substring(0, 10);
                    if (!map[key]) map[key] = {
                        temps: [],
                        rains: [],
                        icons: [],
                        labels: [],
                        date: key
                    };
                    map[key].temps.push(entry.temp);
                    if (entry.rain != null) map[key].rains.push(entry.rain);
                    if (entry.icon) map[key].icons.push(entry.icon);
                    if (entry.label) map[key].labels.push(entry.label);
                });
                this.forecastWeekly = Object.values(map).slice(0, 7).map(group => {
                    const dateTime = new Date(group.date + 'T00:00:00');
                    return {
                        date: group.date,
                        day: dateTime.toLocaleDateString('id-ID', {
                            weekday: 'long'
                        }),
                        min: Math.min(...group.temps),
                        max: Math.max(...group.temps),
                        rain: group.rains.length ? (Math.round((group.rains.reduce((accumulator, current) =>
                            accumulator + current, 0)) * 10) / 10) : null,
                        icon: group.icons[0] || null,
                        label: group.labels[0] || ''
                    };
                });
                // Build summary for today
                if (this.forecastEntries.length) {
                    const today = new Date().toISOString().substring(0, 10);
                    const todayEntries = this.forecastEntries.filter(e => e.local_datetime.startsWith(today));
                    const temps = todayEntries.map(e => e.temp).filter(v => v != null);
                    const first = this.forecastEntries[0];

                    // Calculate light_pct with fallback
                    let lightPct = null;
                    if (first?.tcc != null) {
                        lightPct = Math.max(0, Math.min(100, 100 - first.tcc));
                    } else {
                        // Time-based fallback
                        const hour = new Date().getHours();
                        if (hour >= 6 && hour <= 18) {
                            const progress = (hour - 6) / 12;
                            lightPct = Math.sin(progress * Math.PI) * 75 + 25;
                        } else {
                            lightPct = 5;
                        }
                    }

                    this.weatherSummary = {
                        temp: first?.temp ?? '-',
                        label: first?.label || '-',
                        humidity: first?.humidity ?? '-',
                        wind_speed: first?.wind_speed ?? '-',
                        wind_dir: first?.wind_dir ?? '',
                        rain: first?.rain ?? 0,
                        light_pct: lightPct,
                        tcc: first?.tcc,
                        icon: first?.icon || null,
                        time: first?.local_datetime ? new Date(first.local_datetime).toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : '',
                        temp_min: temps.length ? Math.min(...temps) : null,
                        temp_max: temps.length ? Math.max(...temps) : null
                    };

                    console.log('Weather summary built:', {
                        temp: this.weatherSummary.temp,
                        rain: this.weatherSummary.rain,
                        light_pct: this.weatherSummary.light_pct
                    });
                }
                this.buildCalendar();
                this.buildWeekView();
                this.buildTasks();
            },
            buildCalendar() {
                const year = this.calendarBase.getFullYear();
                const month = this.calendarBase.getMonth();
                const firstDay = new Date(year, month, 1);
                const startWeekDay = (firstDay.getDay() + 6) % 7; // make Monday index 0
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const prevMonthDays = startWeekDay;
                const totalCells = Math.ceil((prevMonthDays + daysInMonth) / 7) * 7;
                const result = [];
                for (let i = 0; i < totalCells; i++) {
                    const dayNum = i - prevMonthDays + 1;
                    const date = new Date(year, month, dayNum);
                    const isCurrentMonth = dayNum >= 1 && dayNum <= daysInMonth;
                    const iso = date.toISOString().substring(0, 10);
                    const fEntries = this.forecastEntries.filter(entry => entry.local_datetime.startsWith(iso));
                    const temps = fEntries.map(entry => entry.temp).filter(value => value != null);
                    const rainValues = fEntries.map(entry => entry.rain).filter(value => value != null);
                    const rainSum = rainValues.length ? Math.round(rainValues.reduce((accumulator, current) =>
                        accumulator + current, 0) * 10) / 10 : null;
                    const icon = fEntries.find(entry => entry.icon)?.icon || null;
                    const label = fEntries.find(entry => entry.label)?.label || '';
                    const usageForDay = this.usage.find(usage => usage.date === iso || usage.day === iso);
                    result.push({
                        key: iso,
                        date: iso,
                        day: date.getDate(),
                        isCurrentMonth,
                        icon,
                        label,
                        tempRange: temps.length ? (Math.min(...temps) + '/' + Math.max(...temps)) : '',
                        rain: rainSum,
                        usage_l: usageForDay ? parseFloat(usageForDay.total_l || usageForDay.volume_l) : null,
                        entries: fEntries.length
                    });
                }
                this.calendarDays = result;
                this.calendarMonthLabel = firstDay.toLocaleDateString('id-ID', {
                    month: 'long',
                    year: 'numeric'
                });
            },
            buildWeekView() {
                console.log('📅 Building week view, forecastEntries count:', this.forecastEntries?.length);
                
                const start = new Date();
                const monday = new Date(start.setDate(start.getDate() - ((start.getDay() + 6) % 7) + this.weekOffset *
                    7));
                const days = [];
                for (let i = 0; i < 7; i++) {
                    const date = new Date(monday.getFullYear(), monday.getMonth(), monday.getDate() + i);
                    const iso = date.toISOString().substring(0, 10);
                    const fEntries = this.forecastEntries.filter(entry => entry.local_datetime.startsWith(iso));
                    let avgTemp = '-';
                    let forecastIcon = null;
                    let forecastLabel = '';
                    let category = 'idle';
                    let style = this.categoryStyles['idle'];
                    if (fEntries.length) {
                        const temps = fEntries.map(entry => entry.temp).filter(value => value != null);
                        if (temps.length) avgTemp = Math.round(temps.reduce((accumulator, current) => accumulator +
                            current, 0) / temps.length);
                        // Pilih entri mendekati tengah hari (12:00) sebagai ikon; fallback 11/13; lalu pertama.
                        const midday = fEntries.find(entry => /T12:00:00/.test(entry.local_datetime)) || fEntries.find(
                            entry =>
                            /T11:00:00|T13:00:00/.test(entry.local_datetime)) || fEntries[0];
                        forecastIcon = midday?.icon || midday?.weather_icon || null;
                        forecastLabel = midday?.label || this.translateWeather(midday?.weather_desc) || '';
                        // Hitung curah hujan total untuk kategorisasi.
                        const rainValues = fEntries.map(entry => entry.rain).filter(value => value != null);
                        const rainSum = rainValues.length ? rainValues.reduce((accumulator, current) => accumulator +
                            current, 0) : 0;
                        const cfg = this.categoryConfig;
                        if (rainSum >= (cfg.shipment?.minRain ?? 5)) category = 'ship';
                        else if (avgTemp !== '-' && rainSum <= (cfg.fertilization?.maxRain ?? 2) && avgTemp >= (cfg
                                .fertilization?.minTemp ?? 30)) category = 'fert';
                        else if (avgTemp !== '-' && rainSum <= (cfg.plowing?.maxRain ?? 2) && avgTemp < (cfg.plowing
                                ?.maxTemp ?? 30)) category = 'plowing';
                        style = this.categoryStyles[category] || this.categoryStyles['idle'];
                    }
                    const dayObject = {
                        date: iso,
                        day: date.getDate(),
                        temp: avgTemp === '-' ? '-' : avgTemp + '°',
                        weekdayShort: date.toLocaleDateString('id-ID', {
                            weekday: 'short'
                        }),
                        category,
                        categoryBg: style.bg,
                        icon: forecastIcon, // only show real BMKG icon if available
                        label: forecastLabel || (avgTemp === '-' ? '' : forecastLabel),
                        active: false
                    };
                    const todayIso = new Date().toISOString().substring(0, 10);
                    if (iso === todayIso) dayObject.active = true;
                    days.push(dayObject);
                }
                // Fallback untuk hari minggu ini yang tidak punya data BMKG: gunakan hari terdekat yang punya data
                // (utamakan mundur ke belakang, jika tidak ada ambil yang di depan). Tandai dengan estimated flag.
                const todayIso = new Date().toISOString().substring(0, 10);
                for (let i = 0; i < days.length; i++) {
                    const currentDay = days[i];
                    if (currentDay.temp === '-' && currentDay.date <= todayIso) {
                        let sourceDay = null;
                        for (let backwardIndex = i - 1; backwardIndex >= 0; backwardIndex--) {
                            if (days[backwardIndex].temp !== '-') {
                                sourceDay = days[backwardIndex];
                                break;
                            }
                        }
                        if (!sourceDay) {
                            for (let forwardIndex = i + 1; forwardIndex < days.length; forwardIndex++) {
                                if (days[forwardIndex].temp !== '-') {
                                    sourceDay = days[forwardIndex];
                                    break;
                                }
                            }
                        }
                        if (!sourceDay && this.weatherSummary && this.weatherSummary.temp) {
                            sourceDay = {
                                temp: Math.round(this.weatherSummary.temp) + '°',
                                icon: this.weatherSummary.icon,
                                label: this.weatherSummary.label,
                                category: 'idle',
                                categoryBg: this.categoryStyles['idle'].bg
                            };
                        }
                        if (sourceDay) {
                            currentDay.temp = sourceDay.temp;
                            currentDay.icon = currentDay.icon || sourceDay.icon;
                            currentDay.label = currentDay.label || sourceDay.label;
                            currentDay.categoryBg = currentDay.categoryBg == this.categoryStyles['idle'].bg ? sourceDay
                                .categoryBg || currentDay
                                .categoryBg : currentDay.categoryBg;
                            currentDay.estimated = true;
                        }
                    }
                }
                this.weekViewDays = days;
                console.log('✅ Week view built with', days.length, 'days:', this.weekViewDays);
            },
            shiftWeek(delta) {
                this.weekOffset += delta;
                this.buildWeekView();
            },
            selectWeekDay(day) {
                this.weekViewDays.forEach(d => d.active = d.date === day.date);
            },
            refreshTasks() {
                this.buildTasks();
            },
            buildTasks() {
                // Placeholder task derivation from irrigation plan & usage summary
                const tasks = [];
                if (this.plan && this.plan.adjusted_total_l) {
                    const diff = this.plan.adjusted_total_l - (this.deviceSessionsSummary?.total_actual_l || 0);
                    if (diff > 0) {
                        tasks.push({
                            id: 'water-deficit',
                            title: 'Penjadwalan Penyiraman',
                            desc: `Masih kurang <b>${Math.round(diff)} L</b> dari target hari ini`,
                            badgeValue: 'Kini',
                            badgeLabel: 'butuh',
                            color: 'bg-red-500',
                            tag: 'Irigasi',
                            tagColor: 'bg-red-100 text-red-700'
                        });
                    }
                }
                if (this.weatherSummary && this.weatherSummary.rain != null && this.weatherSummary.rain > 5) {
                    tasks.push({
                        id: 'rain-adjust',
                        title: 'Curah Hujan Tinggi',
                        desc: 'Pertimbangkan pengurangan sesi irigasi.',
                        badgeValue: '6j',
                        badgeLabel: 'ke depan',
                        color: 'bg-green-600',
                        tag: 'Cuaca',
                        tagColor: 'bg-green-100 text-green-700'
                    });
                }
                this.currentTasks = tasks;
            },
            prevMonth() {
                this.calendarBase = new Date(this.calendarBase.getFullYear(), this.calendarBase.getMonth() - 1, 1);
                this.buildCalendar();
            },
            nextMonth() {
                this.calendarBase = new Date(this.calendarBase.getFullYear(), this.calendarBase.getMonth() + 1, 1);
                this.buildCalendar();
            },
            selectDay(d) {
                this.selectedDate = d.date;
                this.calendarDetails = {
                    date: d.date,
                    dateHuman: new Date(d.date + 'T00:00:00').toLocaleDateString('id-ID', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long'
                    }),
                    min: d.tempRange ? d.tempRange.split('/')[0] : '-',
                    max: d.tempRange ? d.tempRange.split('/')[1] : '-',
                    rain: d.rain,
                    usage_l: d.usage_l != null ? d.usage_l.toFixed(1) : null,
                    entries: d.entries
                };
            },
            applyWeatherEntry(entry) {
                if (!entry) return;
                const desc = entry.weather_desc || entry.weather_desc_id || entry.weather || '';
                const temp = entry.t;
                const hum = entry.humidity ?? entry.hu ?? entry.h;
                // If only numeric code present, map it
                const code = entry.weather_code ?? entry.weather;
                const codeMap = {
                    0: 'Cerah',
                    1: 'Cerah',
                    2: 'Cerah Berawan',
                    3: 'Berawan',
                    4: 'Berawan',
                    5: 'Udara Kabur',
                    10: 'Asap',
                    45: 'Kabut',
                    60: 'Hujan Ringan',
                    61: 'Hujan',
                    63: 'Hujan Lebat',
                    80: 'Hujan Lokal',
                    95: 'Badai Petir'
                };
                let label = this.translateWeather(desc);
                if ((!label || label === '-') && typeof code === 'number' && codeMap[code]) label = codeMap[code];
                if ((!label || label === '-') && entry.weather_desc_en) label = this.translateWeather(entry
                    .weather_desc_en);
                if (!label || label === '-') {
                    console.warn('Weather description missing/raw entry:', entry);
                }
                // update metrics directly
                if (temp != null) this.updateMetric('temp', parseFloat(temp), 'now');
                if (hum != null) this.updateMetric('humidity', parseFloat(hum), 'BMKG');
                // Keep icon reference (for future use)
                this.weatherIcon = entry.weather_icon || entry.image || null;
                // Wind
                const ws = entry.wind_speed_ms ?? entry.ws;
                if (ws != null) {
                    const wsNum = parseFloat(ws);
                    if (!isNaN(wsNum)) this.updateMetric('wind', (Math.round(wsNum * 10) / 10), entry
                        .wind_dir_cardinal || entry.wd || '');
                }
                // Light estimation: tcc already 0-100 (cloudiness). Light% = 100 - tcc.
                if (entry.tcc != null) {
                    const tcc = parseFloat(entry.tcc);
                    if (!isNaN(tcc)) {
                        const lightPct = Math.max(0, Math.min(100, 100 - tcc));
                        this.updateMetric('light', Math.round(lightPct), 'estimasi');
                    }
                }
                this.computeTopMetrics();
            },
            translateWeather(code) {
                const weatherCode = (code || '').toString().toLowerCase();
                if (weatherCode.includes('cerah') || weatherCode.includes('sun')) return 'Cerah';
                if (weatherCode.includes('berawan') || weatherCode.includes('cloud')) return 'Berawan';
                if (weatherCode.includes('mendung') || weatherCode.includes('overcast')) return 'Mendung';
                if (weatherCode.includes('hujan') || weatherCode.includes('rain')) return 'Hujan';
                if (weatherCode.includes('malam') || weatherCode.includes('night')) return 'Malam';
                return code || '-';
            },

            renderUsageChart24h() {
                const el = document.getElementById('usageChart24h');
                if (!el) return;

                try {
                    // ✅ Destroy existing chart first to prevent memory leaks and loops
                    if (this.usageChart24h) {
                        this.usageChart24h.destroy();
                        this.usageChart24h = null;
                    }

                    // ✅ Convert to plain arrays (deep clone to avoid Proxy)
                    const plainData = JSON.parse(JSON.stringify(this.usage24h || []));
                    const labels = plainData.map(r => (r.hour || '00') + ':00');
                    const data = plainData.map(r => parseFloat(r.total_l) || 0);

                    if (!labels.length || !data.length) {
                        console.log('No 24h data to render');
                        return;
                    }
                    const watermark = {
                        id: 'sisWatermark24h',
                        afterDraw(chart, args, opts) {
                            const {
                                ctx,
                                chartArea: {
                                    left,
                                    top,
                                    width,
                                    height
                                }
                            } = chart;
                            ctx.save();
                            ctx.globalAlpha = 0.06;
                            ctx.translate(left + width / 2, top + height / 2);
                            ctx.scale(3, 3);
                            ctx.strokeStyle = '#3b82f6';
                            ctx.lineWidth = 0.8;
                            ctx.lineCap = 'round';
                            ctx.beginPath();
                            // clock-like shape
                            ctx.arc(0, 0, 3, 0, 2 * Math.PI);
                            ctx.moveTo(0, 0);
                            ctx.lineTo(0, -2);
                            ctx.moveTo(0, 0);
                            ctx.lineTo(1.5, 0);
                            ctx.stroke();
                            ctx.restore();
                        }
                    };
                    this.usageChart24h = new Chart(el.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Liter/Jam',
                                data: data,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59,130,246,0.2)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: false, // Disable animation to prevent loops
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0,0,0,0.1)'
                                    }
                                }
                            }
                        },
                        plugins: [watermark]
                    });
                } catch (error) {
                    console.error('Chart 24h render error:', error);
                    // ✅ Destroy broken chart instance
                    if (this.usageChart24h) {
                        this.usageChart24h.destroy();
                        this.usageChart24h = null;
                    }
                }
            },
            renderUsageChart30d() {
                const el = document.getElementById('usageChart30d');
                if (!el) {
                    console.log('Element usageChart30d not found');
                    return;
                }

                try {
                    console.log('30d data:', this.usage);

                    // ✅ Destroy existing chart first to prevent memory leaks and loops
                    if (this.usageChart) {
                        this.usageChart.destroy();
                        this.usageChart = null;
                    }

                    // ✅ Convert to plain arrays (deep clone to avoid Proxy)
                    const plainData = JSON.parse(JSON.stringify(this.usage || []));
                    const labels = plainData.map(r => {
                        const date = new Date(r.date || r.usage_date);
                        if (isNaN(date)) return r.date || r.usage_date;
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = date.getFullYear();
                        return `${day}-${month}-${year}`;
                    });
                    const data = plainData.map(r => parseFloat(r.total_l) || 0);
                    console.log('30d labels:', labels, 'data:', data);

                    if (!labels.length || !data.length) {
                        console.log('No 30d data to render');
                        return;
                    }
                    const watermark = {
                        id: 'sisWatermark30d',
                        afterDraw(chart, args, opts) {
                            const {
                                ctx,
                                chartArea: {
                                    left,
                                    top,
                                    width,
                                    height
                                }
                            } = chart;
                            ctx.save();
                            ctx.globalAlpha = 0.06;
                            ctx.translate(left + width / 2, top + height / 2);
                            ctx.scale(4, 4);
                            ctx.strokeStyle = '#16a34a';
                            ctx.lineWidth = 0.8;
                            ctx.lineCap = 'round';
                            ctx.beginPath();
                            // simple leaf-like shape
                            ctx.moveTo(0, 3);
                            ctx.quadraticCurveTo(4, 2, 5, -2);
                            ctx.quadraticCurveTo(1, -3, 0, -6);
                            ctx.quadraticCurveTo(-1, -3, -5, -2);
                            ctx.quadraticCurveTo(-4, 2, 0, 3);
                            ctx.stroke();
                            ctx.restore();
                        }
                    };
                    this.usageChart = new Chart(el.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Liter',
                                data: data,
                                tension: .3,
                                fill: true,
                                borderColor: '#16a34a',
                                backgroundColor: 'rgba(22,163,74,0.15)',
                                pointRadius: 2,
                                pointBackgroundColor: '#16a34a',
                                pointBorderColor: '#16a34a'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 0 // Completely disable animations to prevent circular refs
                            },
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: true,
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 8,
                                    displayColors: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        display: true,
                                        color: 'rgba(0,0,0,0.1)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        color: '#666'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: '#666',
                                        maxRotation: 45,
                                        minRotation: 45
                                    }
                                }
                            }
                        },
                        plugins: [watermark]
                    });
                } catch (error) {
                    console.error('Chart 30d render error:', error);
                    // ✅ Destroy broken chart instance
                    if (this.usageChart) {
                        this.usageChart.destroy();
                        this.usageChart = null;
                    }
                }
            },
            totalUsage() {
                if (!this.usage || !this.usage.length) return '0.0';
                return this.usage.reduce((accumulator, item) => accumulator + (parseFloat(item.total_l) || 0), 0)
                    .toFixed(1);
            },
            totalUsage24h() {
                if (!this.usage24h || !this.usage24h.length) return '0.0';
                return this.usage24h.reduce((accumulator, item) => accumulator + (parseFloat(item.total_l) || 0), 0)
                    .toFixed(1);
            },
            avgUsage() {
                if (!this.usage.length) return '0.0';
                return (this.totalUsage() / this.usage.length).toFixed(1);
            },
            avgUsage24h() {
                if (!this.usage24h.length) return '0.0';
                return (this.totalUsage24h() / this.usage24h.length).toFixed(1);
            },
            peakDay() {
                if (!this.usage.length) return '-';
                const peak = this.usage.reduce((max, curr) => curr.total_l > max.total_l ? curr : max);
                return `${peak.usage_date} (${peak.total_l}L)`;
            },
            lowDay() {
                if (!this.usage.length) return '-';
                const low = this.usage.reduce((min, curr) => curr.total_l < min.total_l ? curr : min);
                return `${low.usage_date} (${low.total_l}L)`;
            },
            peakHour24h() {
                if (!this.usage24h.length) return '-';
                const peak = this.usage24h.reduce((max, curr) => curr.total_l > max.total_l ? curr : max);
                return `${peak.hour}:00 (${peak.total_l}L)`;
            },
            lowHour24h() {
                if (!this.usage24h.length) return '-';
                const low = this.usage24h.reduce((min, curr) => curr.total_l < min.total_l ? curr : min);
                return `${low.hour}:00 (${low.total_l}L)`;
            },
            fmt(value, suffix = '') {
                if (value == null) return '-';
                const number = parseFloat(value);
                return isNaN(number) ? '-' : number.toFixed(1) + suffix;
            },
            batteryDisplay(device) {
                if (!device || device.battery_voltage_v == null || device.battery_voltage_v === undefined) return '-';
                const voltage = parseFloat(device.battery_voltage_v);
                if (isNaN(voltage) || voltage <= 0) return '-';
                // Li-Ion 1S: 3.3V (0%) - 4.2V (100%)
                const percentage = Math.max(0, Math.min(100, ((voltage - 3.3) / (4.2 - 3.3)) * 100));
                return voltage.toFixed(2) + 'V (' + percentage.toFixed(0) + '%)';
            },
            batteryDisplayShort(device) {
                if (!device || device.battery_voltage_v == null || device.battery_voltage_v === undefined) return '-';
                const voltage = parseFloat(device.battery_voltage_v);
                if (isNaN(voltage) || voltage <= 0) return '-';
                // Li-Ion 1S: 3.3V (0%) - 4.2V (100%)
                const percentage = Math.max(0, Math.min(100, ((voltage - 3.3) / (4.2 - 3.3)) * 100));
                return percentage.toFixed(0) + '%';
            },
            tankFillColor() {
                const percentage = this.tank?.percentage || 0;
                if (percentage < 25) return '#dc2626';
                if (percentage < 50) return '#f59e0b';
                if (percentage < 75) return '#3b82f6';
                return '#16a34a';
            },
            tankFillStyle() {
                const color = this.tankFillColor();
                return `background: linear-gradient(180deg, ${color}cc 0%, ${color}ee 60%, ${color} 100%); box-shadow: inset 0 2px 4px rgba(0,0,0,0.25);`;
            },
            tankStatusClass() {
                const status = (this.tank?.status || '').toLowerCase();
                if (status.includes('krit') || status === 'low') return 'text-red-600';
                if (status.includes('warning') || status.includes('wasp')) return 'text-amber-600';
                return 'text-green-600';
            },
            tankLabelClass() {
                const percentage = this.tank?.percentage || 0;
                if (percentage < 25) return 'bg-red-600/70 text-white';
                if (percentage < 50) return 'bg-amber-500/70 text-white';
                if (percentage < 75) return 'bg-blue-600/70 text-white';
                return 'bg-green-600/70 text-white';
            },
            deviceUsageToday(deviceId) {
                const device = this.devices.find(dev => dev.device_id === deviceId || dev.id === deviceId);
                if (!device || device.water_usage_today_l == null) return '-';
                return device.water_usage_today_l.toFixed(0) + 'L';
            },
            timeAgo(timestamp) {
                if (!timestamp) return '-';
                const date = new Date(timestamp);
                const diff = (Date.now() - date) / 60000;
                if (diff < 1) return 'baru';
                if (diff < 60) return Math.round(diff) + 'm';
                const hours = diff / 60;
                if (hours < 24) return hours.toFixed(1) + 'j';
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            },
            deviceBadgeClass() {
                return 'bg-gray-100 text-gray-600';
            },
            statusShort(statusText) {
                return statusText?.substring(0, 6) || 'ok';
            },
            sessionColor(sessionStatus) {
                return sessionStatus === 'completed' ? 'text-green-600' : sessionStatus === 'pending' ?
                    'text-gray-500' : 'text-yellow-600';
            },
            init() {
                // Wait for Alpine and DOM to be fully ready
                this.$nextTick(() => {
                    // Initialize charts first
                    setTimeout(() => {
                        console.log('🔧 Initializing environmental charts...');
                        this.initEnvironmentalCharts();
                    }, 300);

                    // Initialize leaflet
                    setTimeout(() => this.initLeaflet(), 600);

                    // Load data after charts ready
                    setTimeout(() => {
                        console.log('📊 Loading chart data...');
                        this.loadAll();
                        this.loadTank();
                        this.loadPlan();
                        this.loadUsage();
                        this.loadUsageDaily();
                    }, 800);
                });

                // Pseudo-realtime polling every 15 seconds (optimized for professional IoT dashboards)
                // This is extremely optimized: the API endpoints use Cache::remember()
                // meaning 90% of requests hit RAM/File Cache instead of MySQL database.
                // We also check document.hidden so we don't poll when the tab is inactive!
                setInterval(() => {
                    // Only poll if tab is active and not currently loading
                    if (!document.hidden && !this.loadingAll && !this.loadingDevices) {
                        this.loadDevices(); // Update perangkat
                        this.loadTank(); // Update data tangki
                    }
                }, 15000);

                // Update charts and tasks every 2 minutes
                setInterval(() => {
                    // Only poll if tab is active
                    if (!document.hidden && !this.loadingAll) {
                        this.loadWeather();
                        this.loadUsage();
                        this.loadUsageDaily();
                    }
                }, 120000);

                // Clock tick
                this.tickClock();
                setInterval(() => this.tickClock(), 1000);
            },
            tickClock() {
                const now = new Date();
                const pad = number => number.toString().padStart(2, '0');
                this.clock.time = pad(now.getHours()) + ':' + pad(now.getMinutes());
                this.clock.seconds = ':' + pad(now.getSeconds());
                this.clock.dateLong = now.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                this.clock.dateShort = now.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
                this.clock.day = now.getDate();
                this.clock.month = now.toLocaleDateString('id-ID', {
                    month: 'short'
                });
                this.clock.year = now.getFullYear();
            }
        }
    }
</script>
