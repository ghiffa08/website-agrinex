/**
 * AgriNex Dashboard Alpine.js Component - Clean production build
 * No console.log, no dead code, minimal surface area
 */
function dashboard() {
    return {
        // --- State ---
        currentLang: localStorage.getItem('sis_lang') || 'id',
        darkMode: localStorage.getItem('sis_dark') === '1',
        sidebarOpen: false,

        loadingAll: true,
        loadingDevices: false,
        loadingWeather: true,
        loadingTank: true,
        loadingUsage: true,
        loadingSchedule: true,
        loadingCharts: true,
        fetchError: false,
        lastUpdated: null,

        devices: [],
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

        clock: { time: '--:--', seconds: '', dateLong: '', dateShort: '', day: '', month: '', year: '' },

        weekOffset: 0,
        weekViewDays: [],
        currentTasks: [],

        tank: {},
        tankUpdatedAt: null,
        plan: {},
        usage: [],
        usage24h: [],
        usageChart: null,
        usageChart24h: null,

        showDeviceModal: false,
        selectedDevice: null,
        deviceSessions: [],
        deviceSessionsSummary: null,
        deviceUsageHistory: [],
        loadingDeviceDetail: false,

        // --- Translations ---
        translations: {
            id: {
                appTitle: 'Irigasi Pintar', appSubtitle: 'Monitoring & otomasi penyiraman', switchLang: 'Ganti ke Bahasa Inggris',
                refresh: 'Refresh', loading: 'Memuat', admin: 'Admin', login: 'Masuk',
                currentTime: 'Waktu Sekarang', currentDate: 'Tanggal Hari Ini', currentWeather: 'Cuaca Saat Ini',
                forecast: 'Prakiraan', next24h: '24 Jam', next7d: 'Minggu',
                day: 'Tanggal', month: 'Bulan', year: 'Tahun', humidity: 'Kelembapan',
                windSpeed: 'Kecepatan Angin', pressure: 'Tekanan', lightPercent: 'cahaya',
                activities: 'Aktivitas / Peringatan', weeklyTasks: 'Tugas Minggu Ini', upcomingWeek: 'Minggu Ini',
                noTasks: 'Tidak ada tugas terjadwal minggu ini', prevWeek: '‹ Minggu Lalu', nextWeek: 'Minggu Depan ›',
                today: 'Kini butuh', daysShort: ['Min','Sen','Sel','Rab','Kam','Jum','Sab'],
                environmentSummary: 'Ringkasan Lingkungan', lightIntensity: 'Intensitas Cahaya', waterLevel: 'Ketinggian Air',
                soilMoisture: 'Kelembapan Tanah', temperature: 'Suhu', airHumidity: 'Kelembapan Udara', time: 'Waktu',
                temp: 'Suhu', hum: 'Kelembapan Udara', soil: 'Kelembapan Tanah', light: 'Cahaya', rain: 'Hujan', water: 'Ketinggian Air',
                devices: 'Perangkat', allDevices: 'Semua Perangkat', noDevices: 'Tidak ada data perangkat',
                viewDetails: 'Detail', battery: 'Baterai', waterUsageToday: 'Pemakaian Hari Ini', lastUpdate: 'Terakhir',
                waterTank: 'Tangki Air', capacity: 'Kapasitas', currentLevel: 'Level Saat Ini', status: 'Status',
                lastUpdated: 'Terakhir Diperbarui', todaySchedule: 'Jadwal Hari Ini', noSchedule: 'Tidak ada jadwal',
                waterUsageHistory: 'Riwayat Penggunaan Air', last30Days: '30 Hari Terakhir', last24Hours: '24 Jam Terakhir',
                dailyData30: 'Data harian dalam 30 hari terakhir', hourlyData24: 'Data per jam dalam 24 jam terakhir',
                totalUsage: 'Total', avgUsage: 'Rata-rata', peakUsage: 'Puncak', lowUsage: 'Terendah', days: 'hari', noDataYet: 'Belum ada data',
                location: 'Lokasi', streetView: 'Street View', villageMap: 'Peta Desa', close: 'Tutup', viewFullMap: 'Lihat Peta Lengkap',
                deviceDetails: 'Detail Perangkat', sessions: 'Sesi', usageHistory: 'Riwayat Pemakaian', noData: 'Tidak ada data',
                celsius: '°C', percent: '%', lux: 'lux', mm: 'mm', cm: 'cm', liter: 'L', kmh: 'km/j', hPa: 'hPa',
            },
            en: {
                appTitle: 'Smart Irrigation', appSubtitle: 'Monitoring & irrigation automation', switchLang: 'Switch to Indonesian',
                refresh: 'Refresh', loading: 'Loading', admin: 'Admin', login: 'Login',
                currentTime: 'Current Time', currentDate: 'Today\'s Date', currentWeather: 'Current Weather',
                forecast: 'Forecast', next24h: '24 Hours', next7d: 'Week',
                day: 'Day', month: 'Month', year: 'Year', humidity: 'Humidity',
                windSpeed: 'Wind Speed', pressure: 'Pressure', lightPercent: 'light',
                activities: 'Activities / Alerts', weeklyTasks: 'This Week\'s Tasks', upcomingWeek: 'This Week',
                noTasks: 'No scheduled tasks this week', prevWeek: '‹ Previous Week', nextWeek: 'Next Week ›',
                today: 'Today need', daysShort: ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
                environmentSummary: 'Environmental Summary', lightIntensity: 'Light Intensity', waterLevel: 'Water Level',
                soilMoisture: 'Soil Moisture', temperature: 'Temperature', airHumidity: 'Air Humidity', time: 'Time',
                temp: 'Temperature', hum: 'Air Humidity', soil: 'Soil Moisture', light: 'Light', rain: 'Rain', water: 'Water Level',
                devices: 'Devices', allDevices: 'All Devices', noDevices: 'No device data',
                viewDetails: 'Details', battery: 'Battery', waterUsageToday: 'Today\'s Usage', lastUpdate: 'Last',
                waterTank: 'Water Tank', capacity: 'Capacity', currentLevel: 'Current Level', status: 'Status',
                lastUpdated: 'Last Updated', todaySchedule: 'Today\'s Schedule', noSchedule: 'No schedule',
                waterUsageHistory: 'Water Usage History', last30Days: 'Last 30 Days', last24Hours: 'Last 24 Hours',
                dailyData30: 'Daily data for the last 30 days', hourlyData24: 'Hourly data for the last 24 hours',
                totalUsage: 'Total', avgUsage: 'Average', peakUsage: 'Peak', lowUsage: 'Lowest', days: 'days', noDataYet: 'No data yet',
                location: 'Location', streetView: 'Street View', villageMap: 'Village Map', close: 'Close', viewFullMap: 'View Full Map',
                deviceDetails: 'Device Details', sessions: 'Sessions', usageHistory: 'Usage History', noData: 'No data',
                celsius: '°C', percent: '%', lux: 'lux', mm: 'mm', cm: 'cm', liter: 'L', kmh: 'km/h', hPa: 'hPa',
            },
        },

        // --- Init ---
        init() {
            this.applyPersistedTheme();
            document.title = this.t('appTitle');
            this.startClock();
            this.loadEssential();
            this.startPolling();
            
            // Initialize street map after DOM is ready
            this.$nextTick(() => {
                setTimeout(() => {
                    this.initLeaflet();
                }, 500);
            });

            if (window.Echo) {
                window.Echo.channel('dashboard-channel')
                    .listen('.TelemetryReceived', (e) => {
                        // Telemetry received via WebSocket
                        if (e.deviceData && e.deviceData.device_id) {
                            let index = this.devices.findIndex(d => d.device_id === e.deviceData.device_id);
                            if (index !== -1) {
                                this.devices[index] = e.deviceData;
                            } else {
                                this.devices.push(e.deviceData);
                            }
                            this.computeTopMetrics();
                        }
                    })
                    .listen('.IrrigationStatusUpdated', (e) => {
                        // Irrigation status updated via WebSocket
                        this.refreshIrrigationData();
                    });
            }
        },

        // Alias for backward compatibility
        initData() {
            return this.init();
        },

        // --- Persistence ---
        applyPersistedTheme() {
            document.documentElement.classList.toggle('dark', this.darkMode);
        },
        persistDark() {
            localStorage.setItem('sis_dark', this.darkMode ? '1' : '0');
            this.applyPersistedTheme();
        },
        toggleDark() { this.darkMode = !this.darkMode; this.persistDark(); },

        // --- Language ---
        t(key) { return this.translations[this.currentLang][key] || key; },
        toggleLanguage() {
            this.currentLang = this.currentLang === 'id' ? 'en' : 'id';
            localStorage.setItem('sis_lang', this.currentLang);
            document.title = this.t('appTitle');
        },

        // --- Clock ---
        startClock() {
            const tick = () => {
                const now = new Date();
                this.clock.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                this.clock.seconds = now.toLocaleTimeString('id-ID', { second: '2-digit' });
                this.clock.dateLong = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                this.clock.dateShort = now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit' });
                this.clock.day = now.toLocaleDateString('id-ID', { weekday: 'long' });
                this.clock.month = now.toLocaleDateString('id-ID', { month: 'long' });
                this.clock.year = now.getFullYear();
            };
            tick();
            setInterval(tick, 1000);
        },

        // --- Polling ---
        startPolling() {
            // Fast poll (devices + env) dimatikan, diganti WebSockets
            // setInterval(() => this.loadEssential(), 30000);
            
            // Slow poll (usage, schedule) every 5min
            setInterval(() => this.loadSecondary(), 300000);
        },

        // --- Core Data Loading ---
        async loadEssential() {
            try {
                await Promise.all([this.loadDevices(), this.loadEnvStats()]);
                this.computeTopMetrics();
                this.lastUpdated = new Date();
                this.loadingAll = false;
                this.loadingCharts = false;
            } catch (_) { this.fetchError = true; }
        },

        async loadSecondary() {
            await Promise.allSettled([this.loadUsage(), this.loadUsageDaily(), this.loadPlan(), this.loadTank()]);
        },

        async fetchJson(url, cacheKey, ttl = 30000) {
            const cached = sessionStorage.getItem(cacheKey);
            if (cached) {
                try {
                    const { ts, payload } = JSON.parse(cached);
                    if (Date.now() - ts < ttl) {
                        // Return stale but revalidate in background
                        setTimeout(() => this.revalidate(url, cacheKey), 100);
                        return payload;
                    }
                } catch (_) {}
            }
            const res = await fetch(url);
            if (!res.ok) throw new Error('fetch failed');
            const json = await res.json();
            sessionStorage.setItem(cacheKey, JSON.stringify({ ts: Date.now(), payload: json }));
            return json;
        },

        async revalidate(url, cacheKey) {
            try {
                const res = await fetch(url);
                if (res.ok) {
                    const json = await res.json();
                    sessionStorage.setItem(cacheKey, JSON.stringify({ ts: Date.now(), payload: json }));
                }
            } catch (_) {}
        },

        // --- Device Loading ---
        async loadDevices() {
            this.loadingDevices = true;
            try {
                const json = await this.fetchJson('/api/v1/dashboard/devices', 'cache_devices');
                this.devices = (json.data || json || []).map(d => ({
                    id: d.device_id,
                    device_id: d.device_id,
                    name: d.device_name || `Node ${d.plot_number}`,
                    plot_number: d.plot_number,
                    location: d.location || '',
                    treatment_description: d.treatment_description || 'Perlakuan optimal',
                    treatment_type: d.treatment_type,
                    treatment_code: d.treatment_code,
                    fc_target: d.fc_target ? parseFloat(d.fc_target) : null,
                    threshold: d.threshold ? parseFloat(d.threshold) : null,
                    soil_moisture_pct: d.soil_moisture_pct,
                    temperature_c: d.temperature_c,
                    soil_temp_c: d.soil_temp_c,
                    air_temp_c: d.air_temp_c,
                    air_humidity_pct: d.air_humidity_pct,
                    light_lux: d.light_lux,
                    water_height_cm: d.water_height_cm,
                    battery_voltage_v: d.battery_voltage_v || d.battery_voltage,
                    battery_percentage: d.battery_percentage,
                    signal_strength_rssi: d.signal_strength_rssi,
                    signal_strength_pct: d.signal_strength_pct,
                    connection_state: d.connection_state || d.connection_status || 'offline',
                    connection_status: d.connection_status || d.connection_state || 'offline',
                    valve_state: d.valve_state || d.valve_status || 'closed',
                    valve_status: d.valve_status || d.valve_state || 'closed',
                    is_active: d.is_active,
                    status: d.status || 'normal',
                    water_usage_today_l: d.water_usage_today_l ? parseFloat(d.water_usage_today_l) : null,
                    recorded_at: d.recorded_at || d.last_seen,
                    last_seen: d.last_seen || d.recorded_at,
                    last_updated: d.last_seen ? new Date(d.last_seen).toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit'}) : 'Baru saja',
                    lahan_pantau_id: d.lahan_pantau_id,
                    lahan_pantau_name: d.lahan_pantau_name,
                }));
                this.computeTopMetrics();
            } catch (_) { this.fetchError = true; }
            finally { this.loadingDevices = false; }
        },

        get groupedDevices() {
            const groups = {};
            this.devices.forEach(d => {
                const k = d.lahan_pantau_name || 'Unassigned';
                if (!groups[k]) groups[k] = [];
                groups[k].push(d);
            });
            return groups;
        },

        // --- Tank ---
        async loadTank() {
            this.loadingTank = true;
            try {
                const json = await this.fetchJson('/api/v1/dashboard/tank', 'cache_tank');
                const d = json.data || json;
                if (d) {
                    this.tank = {
                        id: d.id,
                        name: d.name || d.tank_name || 'Tangki Air',
                        current_volume_liters: parseFloat(d.water_level_cm || d.current_volume_liters || 0),
                        capacity_liters: parseFloat(d.capacity || d.capacity_liters || 200),
                        percentage: parseFloat(d.percentage || 0),
                        water_level_cm: parseFloat(d.water_level_cm || 0),
                        status: d.status || 'normal',
                    };
                    this.tankUpdatedAt = new Date().toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit'});
                    this.computeTopMetrics();
                }
            } catch (_) {}
            finally { this.loadingTank = false; }
        },

        // --- Schedule / Plan ---
        async loadPlan() {
            this.loadingSchedule = true;
            try {
                const json = await this.fetchJson('/api/v1/dashboard/schedule', 'cache_schedule');
                this.plan = json.data || json;
            } catch (_) {}
            finally { this.loadingSchedule = false; }
        },

        // --- Usage (30 days) ---
        async loadUsage() {
            this.loadingUsage = true;
            try {
                const json = await this.fetchJson('/api/v1/dashboard/usage', 'cache_usage');
                this.usage = (json.data || json || []).map(i => ({
                    date: i.date || i.usage_date,
                    total_l: parseFloat(i.liters || i.total_l) || 0,
                }));
                this.renderUsageChart30d();
            } catch (_) {
                this.usage = [];
                this.renderUsageChart30d();
            }
            finally { this.loadingUsage = false; }
        },

        // --- Usage (24h) ---
        async loadUsageDaily() {
            try {
                const json = await this.fetchJson('/api/v1/dashboard/usage/daily', 'cache_usage_daily');
                this.usage24h = (json.data || json || []).map(i => ({
                    hour: i.hour,
                    total_l: parseFloat(i.liters || i.total_l) || 0,
                }));
                this.renderUsageChart24h();
            } catch (_) {
                this.usage24h = [];
                this.renderUsageChart24h();
            }
        },

        // --- Weather / Env ---
        async loadEnvStats() {
            this.loadingWeather = true;
            try {
                await Promise.allSettled([
                    this.fetchJson('/api/v1/dashboard/weather', 'cache_weather').then(d => {
                        const w = d.data || d;
                        if (w) {
                            this.weatherSummary = {
                                temp: w.temp ?? '-',
                                label: w.label || 'Cerah',
                                humidity: w.humidity ?? '-',
                                wind_speed: w.wind_speed ?? '-',
                                wind_dir: w.wind_dir ?? '',
                                rain: w.rain ?? 0,
                                light_pct: w.light_pct,
                                tcc: w.tcc,
                                icon: w.icon || null,
                                time: w.time || new Date().toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit'}),
                            };
                            if (w.temp != null) this.updateMetric('temp', parseFloat(w.temp), 'now');
                            if (w.humidity != null) this.updateMetric('humidity', parseFloat(w.humidity), 'BMKG');
                            if (w.light_pct != null) this.updateMetric('light', Math.round(w.light_pct), 'BMKG');
                            if (w.wind_speed != null) this.updateMetric('wind', parseFloat(w.wind_speed), w.wind_dir || '');
                            if (w.rain != null) {
                                const desc = w.rain > 5 ? 'lebat' : w.rain > 0 ? 'ringan' : 'tidak ada';
                                this.updateMetric('rain', parseFloat(w.rain), desc);
                            }
                        }
                    }),
                    this.loadBMKGDirect(),
                ]);
            } catch (_) {}
            finally { this.loadingWeather = false; }
        },

        async loadBMKGDirect() {
            try {
                const data = await this.fetchJson('/api/bmkg/forecast', 'cache_bmkg', 600000);
                let entries = Array.isArray(data) ? data : (data?.entries || []);
                if (entries.length) {
                    this.processForecast(entries);
                }
            } catch (_) {
                try {
                    const res = await fetch('https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4=32.08.10.2001');
                    const raw = await res.json();
                    const flat = (raw?.data?.[0]?.cuaca || []).flat().sort((a,b) => new Date(a.local_datetime) - new Date(b.local_datetime));
                    if (flat.length) this.processForecast(flat);
                } catch (_) {}
            }
        },

        translateWeather(desc) {
            if (!desc) return '-';
            const map = {
                'Cerah': 'Cerah', 'Cerah Berawan': 'Cerah Berawan', 'Berawan': 'Berawan',
                'Hujan Ringan': 'Hujan Ringan', 'Hujan Sedang': 'Hujan Sedang', 'Hujan Lebat': 'Hujan Lebat',
                'Badai Petir': 'Badai Petir', 'Kabut': 'Kabut', 'Salju': 'Salju',
            };
            return map[desc] || desc;
        },

        processForecast(list) {
            this.forecastEntries = list.map(e => ({
                local_datetime: e.local_datetime || e.datetime,
                temp: e.t ?? e.temperature_c,
                humidity: e.humidity ?? e.hu ?? e.h,
                rain: e.rain ?? e.tp ?? null,
                label: this.translateWeather(e.weather_desc || e.weather_desc_en || e.weather_desc_id || e.weather),
                icon: e.weather_icon || e.image || null,
                wind_speed: e.wind_speed_ms ?? e.ws ?? null,
                wind_dir: e.wind_dir_cardinal || e.wd || null,
                tcc: e.tcc ?? null,
            })).filter(e => e.local_datetime);

            const now = Date.now();
            this.forecast24h = this.forecastEntries
                .filter(e => new Date(e.local_datetime) - now < 24*3600*1000)
                .slice(0, 12)
                .map(e => ({ ...e, hour: new Date(e.local_datetime).toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit'}) }));

            const map = {};
            this.forecastEntries.forEach(e => {
                const key = new Date(e.local_datetime).toISOString().substring(0,10);
                if (!map[key]) map[key] = { temps: [], rains: [], icons: [], labels: [], date: key };
                if (e.temp != null) map[key].temps.push(e.temp);
                if (e.rain != null) map[key].rains.push(e.rain);
                if (e.icon) map[key].icons.push(e.icon);
                if (e.label) map[key].labels.push(e.label);
            });
            this.forecastWeekly = Object.values(map).slice(0,7).map(g => ({
                date: g.date,
                day: new Date(g.date+'T00:00:00').toLocaleDateString('id-ID', {weekday:'long'}),
                min: Math.min(...g.temps), max: Math.max(...g.temps),
                rain: g.rains.length ? Math.round(g.rains.reduce((a,b)=>a+b,0)*10)/10 : null,
                icon: g.icons[0] || null,
                label: g.labels[0] || '',
            }));

            // Weather summary
            if (this.forecastEntries.length) {
                const today = new Date().toISOString().substring(0,10);
                const todayEntries = this.forecastEntries.filter(e => e.local_datetime.startsWith(today));
                const temps = todayEntries.map(e => e.temp).filter(v => v != null);
                const first = this.forecastEntries[0];
                let lightPct = null;
                if (first?.tcc != null) {
                    lightPct = Math.max(0, Math.min(100, 100 - first.tcc));
                } else {
                    const hour = new Date().getHours();
                    if (hour >= 6 && hour <= 18) {
                        const p = (hour - 6) / 12;
                        lightPct = Math.sin(p * Math.PI) * 75 + 25;
                    } else {
                        lightPct = 5;
                    }
                }
                this.weatherSummary = {
                    temp: first?.temp ?? '-', label: first?.label || '-', humidity: first?.humidity ?? '-',
                    wind_speed: first?.wind_speed ?? '-', wind_dir: first?.wind_dir ?? '', rain: first?.rain ?? 0,
                    light_pct: lightPct, tcc: first?.tcc, icon: first?.icon || null,
                    time: first?.local_datetime ? new Date(first.local_datetime).toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit'}) : '',
                    temp_min: temps.length ? Math.min(...temps) : null, temp_max: temps.length ? Math.max(...temps) : null,
                };
            }
            this.buildCalendar();
            this.buildWeekView();
            this.buildTasks();
        },

        // --- Calendar ---
        buildCalendar() {
            const y = this.calendarBase.getFullYear();
            const m = this.calendarBase.getMonth();
            const first = new Date(y, m, 1);
            const startWeek = (first.getDay() + 6) % 7;
            const daysInMonth = new Date(y, m+1, 0).getDate();
            const total = Math.ceil((startWeek + daysInMonth) / 7) * 7;
            const res = [];
            for (let i = 0; i < total; i++) {
                const dayNum = i - startWeek + 1;
                const date = new Date(y, m, dayNum);
                const isCurrent = dayNum >= 1 && dayNum <= daysInMonth;
                const iso = date.toISOString().substring(0,10);
                const f = this.forecastEntries.filter(e => e.local_datetime.startsWith(iso));
                const temps = f.map(e => e.temp).filter(v => v != null);
                const rains = f.map(e => e.rain).filter(v => v != null);
                const rainSum = rains.length ? Math.round(rains.reduce((a,b)=>a+b,0)*10)/10 : null;
                const usage = this.usage.find(u => u.date === iso || u.day === iso);
                res.push({
                    key: iso, date: iso, day: date.getDate(), isCurrentMonth: isCurrent,
                    icon: f.find(e => e.icon)?.icon || null, label: f.find(e => e.label)?.label || '',
                    tempRange: temps.length ? `${Math.min(...temps)}/${Math.max(...temps)}` : '',
                    rain: rainSum, usage_l: usage ? parseFloat(usage.total_l || usage.volume_l) : null,
                    entries: f.length,
                });
            }
            this.calendarDays = res;
            this.calendarMonthLabel = first.toLocaleDateString('id-ID', {month:'long',year:'numeric'});
        },

        prevMonth() { this.calendarBase = new Date(this.calendarBase.getFullYear(), this.calendarBase.getMonth()-1, 1); this.buildCalendar(); },
        nextMonth() { this.calendarBase = new Date(this.calendarBase.getFullYear(), this.calendarBase.getMonth()+1, 1); this.buildCalendar(); },

        selectDay(d) {
            this.selectedDate = d.date;
            this.calendarDetails = { date: d.date, dateHuman: new Date(d.date+'T00:00:00').toLocaleDateString('id-ID', {weekday:'long',day:'numeric',month:'long'}) };
        },

        // --- Week View ---
        buildWeekView() {
            const start = new Date();
            const monday = new Date(start.setDate(start.getDate() - ((start.getDay()+6)%7) + this.weekOffset*7));
            const days = [];
            for (let i = 0; i < 7; i++) {
                const date = new Date(monday.getFullYear(), monday.getMonth(), monday.getDate()+i);
                const iso = date.toISOString().substring(0,10);
                const f = this.forecastEntries.filter(e => e.local_datetime.startsWith(iso));
                let avgTemp = '-', icon = null, label = '', cat = 'idle', style = this.categoryStyles.idle;
                if (f.length) {
                    const temps = f.map(e => e.temp).filter(v => v != null);
                    if (temps.length) avgTemp = Math.round(temps.reduce((a,b)=>a+b,0)/temps.length);
                    const mid = f.find(e => /T12:00:00/.test(e.local_datetime)) || f.find(e => /T11:00:00|T13:00:00/.test(e.local_datetime)) || f[0];
                    icon = mid?.icon || null; label = mid?.label || this.translateWeather(mid?.weather_desc) || '';
                    const rainSum = f.map(e => e.rain).filter(v => v != null).reduce((a,b)=>a+b,0);
                    if (rainSum >= (this.categoryConfig.shipment?.minRain ?? 5)) cat = 'ship';
                    else if (avgTemp !== '-' && rainSum <= 2 && avgTemp >= 30) cat = 'fert';
                    else if (avgTemp !== '-' && rainSum <= 2 && avgTemp < 30) cat = 'plowing';
                    style = this.categoryStyles[cat] || this.categoryStyles.idle;
                }
                days.push({
                    date: iso, day: date.getDate(), temp: avgTemp === '-' ? '-' : avgTemp + '°',
                    weekdayShort: date.toLocaleDateString('id-ID', {weekday:'short'}), category: cat,
                    categoryBg: style.bg, icon: icon, label: label, active: iso === new Date().toISOString().substring(0,10),
                });
            }
            // Fill empty past days from nearest known
            const todayIso = new Date().toISOString().substring(0,10);
            for (let i = 0; i < days.length; i++) {
                if (days[i].temp === '-' && days[i].date <= todayIso) {
                    let src = null;
                    for (let b = i-1; b >= 0; b--) if (days[b].temp !== '-') { src = days[b]; break; }
                    if (!src) for (let f = i+1; f < days.length; f++) if (days[f].temp !== '-') { src = days[f]; break; }
                    if (!src && this.weatherSummary?.temp) src = { temp: Math.round(this.weatherSummary.temp)+'°', icon: this.weatherSummary.icon, label: this.weatherSummary.label, categoryBg: this.categoryStyles.idle.bg };
                    if (src) { days[i].temp = src.temp; days[i].icon = days[i].icon || src.icon; days[i].label = days[i].label || src.label; days[i].categoryBg = src.categoryBg; days[i].estimated = true; }
                }
            }
            this.weekViewDays = days;
        },

        shiftWeek(d) { this.weekOffset += d; this.buildWeekView(); },

        categoryConfig: { plowing: {maxRain:2, maxTemp:30}, fertilization: {maxRain:2, minTemp:30}, shipment: {minRain:5} },
        categoryStyles: {
            plowing: { bg: 'bg-gradient-to-b from-amber-500 to-amber-600 text-white', icon: '🚜' },
            fert:    { bg: 'bg-gradient-to-b from-green-500 to-green-700 text-white', icon: '🧪' },
            ship:    { bg: 'bg-gradient-to-b from-yellow-300 to-yellow-500 text-gray-800', icon: '🚚' },
            idle:    { bg: 'bg-gradient-to-b from-gray-50 to-gray-100 text-gray-700 border border-gray-200', icon: '➖' },
        },

        // --- Tasks ---
        buildTasks() {
            const tasks = [];
            if (this.plan?.adjusted_total_l && this.deviceSessionsSummary?.total_actual_l != null) {
                const diff = this.plan.adjusted_total_l - this.deviceSessionsSummary.total_actual_l;
                if (diff > 0) tasks.push({
                    id: 'water-deficit', title: 'Penjadwalan Penyiraman', desc: `Masih kurang <b>${Math.round(diff)} L</b> dari target hari ini`,
                    badgeValue: 'Kini', badgeLabel: 'butuh', color: 'bg-red-500', tag: 'Irigasi', tagColor: 'bg-red-100 text-red-700',
                });
            }
            if (this.weatherSummary?.rain != null && this.weatherSummary.rain > 5) {
                tasks.push({
                    id: 'rain-adjust', title: 'Curah Hujan Tinggi', desc: 'Pertimbangkan pengurangan sesi irigasi.',
                    badgeValue: '6j', badgeLabel: 'ke depan', color: 'bg-green-600', tag: 'Cuaca', tagColor: 'bg-green-100 text-green-700',
                });
            }
            this.currentTasks = tasks;
        },

        // --- Metrics ---
        topMetricCards: [
            { key: 'temp', label: 'SUHU', type: 'gauge', min: 10, max: 45, unit: '°C', value: null, display: '-', pct: 0, color: '#16a34a' },
            { key: 'humidity', label: 'KELEMBAPAN', type: 'gauge', min: 0, max: 100, unit: '%', value: null, display: '-', pct: 0, color: '#16a34a' },
            { key: 'light', label: 'CAHAYA', type: 'gauge', min: 0, max: 100, unit: '%', value: null, display: '-', pct: 0, color: '#16a34a' },
            { key: 'wind', label: 'ANGIN', type: 'gauge', min: 0, max: 15, unit: 'm/s', value: null, display: '-', pct: 0, color: '#16a34a' },
            { key: 'rain', label: 'HUJAN', type: 'plain', min: 0, max: 50, unit: 'mm', value: null, display: '0.0mm', pct: 0, color: '#6366f1' },
            { key: 'battery', label: 'BATERAI', type: 'linear', min: 0, max: 100, unit: '%', value: null, display: '-', pct: 0, color: '#16a34a' },
            { key: 'devices', label: 'DEVICE', type: 'plain', min: 0, max: 50, unit: '', value: null, display: '-', pct: 0, color: '#16a34a' },
        ],

        metricBy(k) { return this.topMetricCards.find(m => m.key === k); },

        updateMetric(key, val, desc = '') {
            const m = this.metricBy(key);
            if (!m) return;
            if (val == null || isNaN(parseFloat(val))) return;
            const nv = parseFloat(val);
            if (m.value === nv && m.desc === desc) return;
            m.value = nv;
            m.desc = desc;
            if (m.type === 'plain') { m.display = Math.round(nv).toString(); }
            else { m.display = (m.type==='gauge' && m.unit==='%') ? Math.round(nv)+m.unit : (nv.toFixed ? nv.toFixed((m.unit==='%'||m.max<=20)?0:1) : nv) + m.unit; }
            m.pct = Math.max(0, Math.min(100, ((nv - m.min) / (m.max - m.min)) * 100));
            m.color = `hsl(${(m.pct * 120)/100}, 70%, 45%)`;
        },

        normalizePct(v, mn, mx) { if (v == null) return 0; const c = Math.max(mn, Math.min(mx, v)); return ((c-mn)/(mx-mn))*100; },
        gaugeStyle(m) { return `background: conic-gradient(${m.color} 0% ${m.pct}%, #e5e7eb ${m.pct}% 100%);` },
        metricIcon(k) { return ''; },

        computeTopMetrics() {
            // Temp - from weather or average devices
            let t = this.weatherSummary?.temp;
            if ((t == null || t === '-') && this.devices.length) {
                const vals = this.devices.map(d => d.temperature_c).filter(v => v != null);
                if (vals.length) t = vals.reduce((a,b)=>a+b,0)/vals.length;
            }
            if (t != null && t !== '-') this.updateMetric('temp', parseFloat(t), 'now');

            // Humidity - from weather
            const h = this.weatherSummary?.humidity;
            if (h != null && h !== '-') this.updateMetric('humidity', parseFloat(h), 'BMKG');

            // Light - from weather (light_pct), then devices
            let light = this.weatherSummary?.light_pct;
            if ((light == null || light === '-') && this.devices.length) {
                const lux = this.devices.map(d => d.light_lux).filter(v => v != null && v > 0);
                if (lux.length) light = Math.min(100, (lux.reduce((a,b)=>a+b,0)/lux.length / 12000) * 100);
            }
            if (light != null) this.updateMetric('light', Math.round(light), 'estimasi');

            // Wind - from weather
            const ws = this.weatherSummary?.wind_speed;
            if (ws != null && ws !== '-') this.updateMetric('wind', parseFloat(ws), this.weatherSummary?.wind_dir || '');

            // Rain - from weather
            const r = this.weatherSummary?.rain;
            if (r != null) this.updateMetric('rain', parseFloat(r), r > 5 ? 'lebat' : r > 0 ? 'ringan' : 'tidak ada');
            else this.updateMetric('rain', 0, 'tidak ada');

            // Tank
            if (this.tank?.percentage != null) this.updateMetric('tank', parseFloat(this.tank.percentage), 'level');

            // Battery avg
            if (this.devices.length) {
                const bat = this.devices.map(d => d.battery_voltage_v != null ? Math.max(0,Math.min(100,((d.battery_voltage_v-3.3)/(4.2-3.3))*100)) : null).filter(v => v != null);
                if (bat.length) this.updateMetric('battery', bat.reduce((a,b)=>a+b,0)/bat.length, bat.length+' node');
            }

            // Device count
            this.updateMetric('devices', this.devices.length, 'online');
        },

        // --- Usage Charts ---
        renderUsageChart30d() {
            const ctx = document.getElementById('usageChart');
            if (!ctx) return;
            if (this.usageChart) this.usageChart.destroy();
            const labels = this.usage.map(u => u.date);
            const data = this.usage.map(u => u.total_l);
            this.usageChart = new Chart(ctx, {
                type: 'bar', data: { labels, datasets: [{ label: 'Liter', data, backgroundColor: 'rgba(16,185,129,0.6)', borderColor: '#10b981', borderWidth: 1, borderRadius: 4 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true } } },
            });
        },

        renderUsageChart24h() {
            const ctx = document.getElementById('usageChart24h');
            if (!ctx) return;
            if (this.usageChart24h) this.usageChart24h.destroy();
            const labels = this.usage24h.map(u => u.hour);
            const data = this.usage24h.map(u => u.total_l);
            this.usageChart24h = new Chart(ctx, {
                type: 'line', data: { labels, datasets: [{ label: 'L/jam', data, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.2)', borderWidth: 2, tension: 0.4, fill: true, pointRadius: 3 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true } } },
            });
        },

        // --- Device Modal ---
        async loadDeviceDetail(id) {
            this.loadingDeviceDetail = true;
            this.deviceSessions = []; this.deviceUsageHistory = [];
            try {
                const [s, u] = await Promise.allSettled([
                    fetch(`/api/devices/${id}/irrigation/sessions`).then(r => r.ok ? r.json() : {}),
                    fetch(`/api/devices/${id}/usage-history`).then(r => r.ok ? r.json() : {}),
                ]);
                if (s.status === 'fulfilled') { this.deviceSessions = s.value.sessions || []; this.deviceSessionsSummary = s.value.summary || null; this.buildTasks(); }
                if (u.status === 'fulfilled') { this.deviceUsageHistory = u.value.history || []; }
            } catch (_) {}
            finally { this.loadingDeviceDetail = false; }
        },

        openDeviceModal(d) { this.selectedDevice = d; this.showDeviceModal = true; this.loadDeviceDetail(d.id || d.device_id); },
        closeDeviceModal() { this.showDeviceModal = false; this.selectedDevice = null; this.deviceSessions = []; this.deviceUsageHistory = []; },

        // --- Map ---
        showFullMap: false,
        googleMapsLink: 'https://maps.google.com/?q=-6.9891469,108.6086561',
        villageCenter: { lat: -6.9891469, lng: 108.6086561 },
        villagePolygon: [[-6.9869,108.6029],[-6.9878,108.6065],[-6.9889,108.6094],[-6.9903,108.6110],[-6.9920,108.6100],[-6.9910,108.6068],[-6.9898,108.6035]],
        leafletInited: false, leafletFullInited: false,

        openFullMap() { this.showFullMap = true; this.$nextTick(() => this.initLeafletFull()); },
        closeFullMap() { this.showFullMap = false; },
        initLeaflet() { if (this.leafletInited || !window.L) return; const map = L.map('leafletMap', {zoomControl:true,attributionControl:false}).setView([this.villageCenter.lat,this.villageCenter.lng],15); L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map); L.polygon(this.villagePolygon,{color:'#16a34a',weight:2,fillOpacity:0.08}).addTo(map); L.marker([this.villageCenter.lat,this.villageCenter.lng],{title:'Lokasi'}).addTo(map); this.leafletInited = true; },
        initLeafletFull() { if (this.leafletFullInited || !window.L) return; const map = L.map('leafletMapFull',{zoomControl:true}).setView([this.villageCenter.lat,this.villageCenter.lng],15); L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map); L.polygon(this.villagePolygon,{color:'#15803d',weight:2,fillOpacity:0.1}).addTo(map); L.marker([this.villageCenter.lat,this.villageCenter.lng]).bindPopup('Pusat Lahan').addTo(map); this.leafletFullInited = true; },

        // --- Refresh ---
        fetchDevices() { this.loadDevices(); this.loadEnvStats() },

        async refreshTasks() {
            // Refresh plan, usage, and rebuild tasks
            await Promise.allSettled([this.loadPlan(), this.loadUsage(), this.loadUsageDaily()]);
            this.buildTasks();
        },

        // --- Irrigation Refresh ---
        async refreshIrrigationData() {
            // Refresh usage, schedule, and tank data after irrigation event
            await Promise.allSettled([
                this.loadUsage(),
                this.loadUsageDaily(),
                this.loadPlan(),
                this.loadTank()
            ]);
        },

        // --- Helper Functions ---
        fmt(val, unit) {
            if (val == null || val === '') return '-';
            return parseFloat(val).toFixed(1) + (unit || '');
        },

        batteryDisplay(device) {
            if (!device) return '-';
            const v = device.battery_voltage_v;
            if (v == null) return '-';
            const pct = Math.max(0, Math.min(100, ((v - 3.3) / (4.2 - 3.3)) * 100));
            return pct.toFixed(0) + '%';
        },

        avgUsage() {
            if (!this.usage.length) return '0';
            const avg = this.usage.reduce((sum, u) => sum + (u.total_l || 0), 0) / this.usage.length;
            return avg.toFixed(1);
        },

        avgUsage24h() {
            if (!this.usage24h.length) return '0';
            const avg = this.usage24h.reduce((sum, u) => sum + (u.total_l || 0), 0) / this.usage24h.length;
            return avg.toFixed(1);
        },

        // Computed properties
        get soilMoistureSensors() {
            return this.devices.filter(d => d.soil_moisture_pct != null);
        },

        get weekLegend() {
            // Return legend items based on categoryStyles for the week view
            return [
                { key: 'plowing', bg: 'bg-amber-500', label: 'Olah Lahan' },
                { key: 'fert', bg: 'bg-green-500', label: 'Pemupukan' },
                { key: 'ship', bg: 'bg-yellow-400', label: 'Pengiriman' },
                { key: 'idle', bg: 'bg-gray-100', label: 'Normal' },
            ];
        },
    };
}