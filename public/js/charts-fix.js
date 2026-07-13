/**
 * AgriNex Chart Engine - Independent chart init & data loading.
 * Replaces inline chart-fix.blade.php. No console.log in production.
 */
if (window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
    window.console.log = function(){};
}

window.CHART_FIX_ENABLED = true;
window.envCharts = { light: null, water: null, soil: null, temp: null, humidity: null };

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        initCharts();
        loadChartData();
        setInterval(loadChartData, 600000);
    }, 1200);
});

function initCharts() {
    const opts = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                mode: 'index', intersect: false,
                backgroundColor: 'rgba(0,0,0,0.8)', titleColor: '#fff', bodyColor: '#fff',
                borderWidth: 1, padding: 10,
            },
        },
        scales: {
            x: {
                display: true,
                grid: { display: true, color: 'rgba(0,0,0,0.05)', drawBorder: true },
                ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8, color: '#6b7280', font: { size: 11 } },
            },
            y: {
                display: true, beginAtZero: true,
                grid: { display: true, color: 'rgba(0,0,0,0.05)', drawBorder: true },
                ticks: { color: '#6b7280', font: { size: 11 } },
            },
        },
        interaction: { mode: 'nearest', axis: 'x', intersect: false },
    };

    const el = (id) => document.getElementById(id);
    const colors = ['#3b82f6','#a855f7','#f97316','#eab308','#84cc16','#ef4444','#ec4899','#22d3ee'];

    if (el('lightIntensityChart')) {
        window.envCharts.light = new Chart(el('lightIntensityChart'), {
            type: 'line',
            data: { labels: [], datasets: [
                { label: 'LI2', data: [], borderColor: '#22d3ee', backgroundColor: 'rgba(34,211,238,0.1)', borderWidth: 2, tension: 0.4, pointRadius: 3, fill: true },
                { label: 'LI1', data: [], borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.1)', borderWidth: 2, tension: 0.4, pointRadius: 3, fill: true },
            ]},
            options: opts,
        });
    }
    if (el('waterLevelChart')) {
        window.envCharts.water = new Chart(el('waterLevelChart'), {
            type: 'line',
            data: { labels: [], datasets: [{ label: 'Water Level', data: [], borderColor: '#84cc16', backgroundColor: 'rgba(132,204,22,0.1)', borderWidth: 2, tension: 0.4, pointRadius: 3, fill: true }] },
            options: opts,
        });
    }
    if (el('soilMoistureChart')) {
        const ds = colors.map((c, i) => ({
            label: `SM${i+1}`, data: [],
            borderColor: c, backgroundColor: `rgba(${parseInt(c.slice(1,3),16)},${parseInt(c.slice(3,5),16)},${parseInt(c.slice(5,7),16)},0.1)`,
            borderWidth: 2, tension: 0.3, pointRadius: 2, fill: false,
        }));
        window.envCharts.soil = new Chart(el('soilMoistureChart'), { type: 'line', data: { labels: [], datasets: ds }, options: opts });
    }
    if (el('temperatureChart')) {
        window.envCharts.temp = new Chart(el('temperatureChart'), {
            type: 'line',
            data: { labels: [], datasets: [
                { label: 'T1', data: [], borderColor: '#a855f7', backgroundColor: 'rgba(168,85,247,0.1)', borderWidth: 2, tension: 0.4, pointRadius: 3, fill: true },
                { label: 'T2', data: [], borderColor: '#06b6d4', backgroundColor: 'rgba(6,182,212,0.1)', borderWidth: 2, tension: 0.4, pointRadius: 3, fill: true },
            ]},
            options: opts,
        });
    }
    if (el('humidityChart')) {
        window.envCharts.humidity = new Chart(el('humidityChart'), {
            type: 'line',
            data: { labels: [], datasets: [
                { label: 'H2', data: [], borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)', borderWidth: 2, tension: 0.4, pointRadius: 3, fill: true },
                { label: 'H1', data: [], borderColor: '#f97316', backgroundColor: 'rgba(249,115,22,0.1)', borderWidth: 2, tension: 0.4, pointRadius: 3, fill: true },
            ]},
            options: opts,
        });
    }
}

async function loadChartData() {
    try {
        const resp = await fetch('/api/v1/dashboard/charts?type=all&days=7');
        if (!resp.ok) throw new Error(`API ${resp.status}`);
        const json = await resp.json();
        const d = json.data || json;
        
        updateChart('light', d.light, (item) => [item.time, [parseFloat(item.radiation)||0, (parseFloat(item.radiation)||0)*0.9]], 'lightChartBadge');
        updateChart('water', d.water, (item) => [item.time, [parseFloat(item.level)||0]], 'waterChartBadge');
        updateChart('temp', d.temperature, (item) => [item.time, [parseFloat(item.temperature)||0]], 'tempChartBadge');
        updateChart('humidity', d.humidity, (item) => [item.time, [parseFloat(item.humidity)||0]], 'humidityChartBadge');
        updateSoilChart(d.soil, 'soilChartBadge');
    } catch (err) {
        console.error("Error loading charts:", err);
        ['lightChartBadge', 'waterChartBadge', 'tempChartBadge', 'humidityChartBadge', 'soilChartBadge'].forEach(id => {
            const badge = document.getElementById(id);
            if (badge) {
                badge.textContent = 'Error';
                badge.className = 'text-[10px] px-3 py-1 rounded-xl bg-red-100 text-red-600 font-semibold';
            }
        });
    }
}

function updateChart(type, data, mapper, badgeId) {
    const chart = window.envCharts[type];
    const badge = document.getElementById(badgeId);
    if (!chart) return;
    
    const labels = [], ds = [];
    if (data && data.length) {
        data.forEach(item => {
            const [label, values] = mapper(item);
            labels.push(label);
            values.forEach((v, i) => { if (!ds[i]) ds[i] = []; ds[i].push(v); });
        });
        
        if (badge) {
            badge.textContent = 'Online';
            badge.className = 'text-[10px] px-3 py-1 rounded-xl bg-emerald-100 text-emerald-600 font-semibold';
        }
    } else {
        if (badge) {
            badge.textContent = 'No Data';
            badge.className = 'text-[10px] px-3 py-1 rounded-xl bg-slate-100 text-slate-500 font-semibold';
        }
    }
    
    chart.data.labels = labels;
    chart.data.datasets.forEach((dataset, i) => { dataset.data = ds[i] || []; });
    chart.update();
}

function updateSoilChart(data, badgeId) {
    const chart = window.envCharts.soil;
    const badge = document.getElementById(badgeId);
    if (!chart) return;
    
    const labels = [], ds = [[],[],[],[],[],[],[],[]];
    if (data && data.length) {
        data.forEach(item => {
            labels.push(item.time);
            const avg = parseFloat(item.average) || 0;
            for (let i = 0; i < 8; i++) {
                ds[i].push(avg); 
            }
        });
        
        if (badge) {
            badge.textContent = 'Online';
            badge.className = 'text-[10px] px-3 py-1 rounded-xl bg-emerald-100 text-emerald-600 font-semibold';
        }
    } else {
        if (badge) {
            badge.textContent = 'No Data';
            badge.className = 'text-[10px] px-3 py-1 rounded-xl bg-slate-100 text-slate-500 font-semibold';
        }
    }
    
    chart.data.labels = labels;
    chart.data.datasets.forEach((d, i) => { d.data = ds[i] || []; });
    chart.update();
}
