<!-- Mobile sidebar backdrop -->
<div x-show="sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-slate-900/80 z-40 lg:hidden" 
     @click="sidebarOpen = false"
     style="display: none;"></div>

<!-- Sidebar -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl lg:shadow-sm border-r border-slate-200 transition-transform duration-300 ease-in-out lg:static lg:flex-shrink-0 flex flex-col h-full">
    
    <!-- Sidebar Header / Brand -->
    <div class="h-[70px] flex items-center justify-between px-6 border-b border-slate-200 flex-shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
            <div class="w-8 h-8 bg-emerald-500 text-white rounded flex items-center justify-center font-bold text-lg shadow-sm group-hover:bg-emerald-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <span class="text-xl font-bold tracking-tight text-slate-800">Agri<span class="text-emerald-500">Nex</span></span>
        </a>
        <!-- Close button for mobile -->
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-500 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

        <!-- Sidebar Menu -->
    <div class="flex-1 overflow-y-auto hide-scrollbar py-4 px-3 space-y-1">
        
        <div class="px-3 mb-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Dashboard</div>
        
        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-600 font-semibold' : 'text-slate-600 hover:bg-slate-50:bg-slate-700/50 hover:text-slate-900:text-slate-200' }} transition-colors group">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Overview
        </a>

        <div class="px-3 mt-6 mb-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Data Management</div>
        
        <a href="{{ route('admin.sensor-node-data.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.sensor-node-data.*') ? 'bg-emerald-50 text-emerald-600 font-semibold' : 'text-slate-600 hover:bg-slate-50:bg-slate-700/50 hover:text-slate-900:text-slate-200' }} transition-colors group">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.sensor-node-data.*') ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
            </svg>
            Sensor Node Data
        </a>

        <a href="{{ route('admin.weather-data.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.weather-data.*') ? 'bg-emerald-50 text-emerald-600 font-semibold' : 'text-slate-600 hover:bg-slate-50:bg-slate-700/50 hover:text-slate-900:text-slate-200' }} transition-colors group">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.weather-data.*') ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
            </svg>
            Weather Data
        </a>

        <a href="{{ route('admin.getdata-logs.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.getdata-logs.*') ? 'bg-emerald-50 text-emerald-600 font-semibold' : 'text-slate-600 hover:bg-slate-50:bg-slate-700/50 hover:text-slate-900:text-slate-200' }} transition-colors group">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.getdata-logs.*') ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Getdata Logs
        </a>

        <a href="{{ route('admin.irrigate-logs.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.irrigate-logs.*') ? 'bg-emerald-50 text-emerald-600 font-semibold' : 'text-slate-600 hover:bg-slate-50:bg-slate-700/50 hover:text-slate-900:text-slate-200' }} transition-colors group">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.irrigate-logs.*') ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
            Irrigate Logs
        </a>

        <a href="{{ route('admin.valve-logs.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.valve-logs.*') ? 'bg-emerald-50 text-emerald-600 font-semibold' : 'text-slate-600 hover:bg-slate-50:bg-slate-700/50 hover:text-slate-900:text-slate-200' }} transition-colors group">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.valve-logs.*') ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            Valve Logs
        </a>

        <a href="{{ route('admin.node-logs.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.node-logs.*') ? 'bg-emerald-50 text-emerald-600 font-semibold' : 'text-slate-600 hover:bg-slate-50:bg-slate-700/50 hover:text-slate-900:text-slate-200' }} transition-colors group">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.node-logs.*') ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Node Logs
        </a>

        <a href="{{ route('admin.json-backup.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.json-backup.*') ? 'bg-emerald-50 text-emerald-600 font-semibold' : 'text-slate-600 hover:bg-slate-50:bg-slate-700/50 hover:text-slate-900:text-slate-200' }} transition-colors group">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.json-backup.*') ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
            </svg>
            JSON Backup
        </a>

        <div class="px-3 mt-6 mb-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">System</div>
        
        <a href="{{ route('nodes.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('nodes.*') ? 'bg-emerald-50 text-emerald-600 font-semibold' : 'text-slate-600 hover:bg-slate-50:bg-slate-700/50 hover:text-slate-900:text-slate-200' }} transition-colors group">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('nodes.*') ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Sensor Nodes
        </a>

        <a href="{{ route('irrigation.index') }}" class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('irrigation.*') ? 'bg-emerald-50 text-emerald-600 font-semibold' : 'text-slate-600 hover:bg-slate-50:bg-slate-700/50 hover:text-slate-900:text-slate-200' }} transition-colors group">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('irrigation.*') ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
            Irrigation Control
        </a>
    </div>

    <!-- Sidebar Footer -->
    <div class="p-4 border-t border-slate-200">
        <div class="bg-emerald-50 rounded-xl p-4 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-emerald-100 rounded-full"></div>
            <div class="relative z-10">
                <h4 class="text-sm font-semibold text-emerald-800 mb-1">AgriNex v2.0</h4>
                <p class="text-xs text-emerald-600">System is fully operational</p>
            </div>
        </div>
    </div>
</aside>
