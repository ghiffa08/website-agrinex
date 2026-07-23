<!DOCTYPE html>
<html lang="id" class="h-full" style="background-color: #E0E5EC;">
<head>
    @include('partials.head')
    @include('partials.dashboard-scripts')
</head>
<body class="h-full min-h-screen w-full bg-neuBg text-darkText font-sans antialiased">

    <div class="min-h-screen w-full bg-neuBg font-sans text-darkText">
        <div class="relative z-10 flex h-full min-h-screen">
            {{-- Sidebar --}}
            <div class="hidden md:flex md:flex-shrink-0">
                @include('components.sidebar')
            </div>
            <div class="md:hidden">
                @include('components.sidebar')
            </div>

            {{-- Main column --}}
            <div class="flex-1 min-w-0 flex flex-col min-h-screen overflow-x-hidden">
                {{-- Header --}}
                <div class="sticky top-0 z-30 w-full bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]">
                    <div class="px-4 md:px-6 xl:px-8">
                        @include('components.header')
                    </div>
                </div>

                {{-- Page content --}}
                <main class="flex-1 px-4 md:px-6 xl:px-8 pt-6 pb-28 md:pb-10 w-full max-w-[1400px] mx-auto space-y-6">
                    
                    {{-- Back Button --}}
                    <div>
                        <a href="{{ route('lahan-pantau.index') }}" 
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] transition-all text-darkText font-semibold">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Kembali
                        </a>
                    </div>

                    {{-- Lahan Detail --}}
                    <div class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
                        
                        <div class="flex items-start justify-between mb-6 pb-6 border-b border-[#a3b1c6]/30">
                            <div class="flex-1">
                                <h1 class="text-2xl md:text-3xl font-extrabold text-darkText mb-2">{{ $lahan->nama_lahan }}</h1>
                                <p class="text-lightText flex items-center gap-2">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $lahan->lokasi ?? 'Lokasi belum diatur' }}
                                </p>
                            </div>
                        </div>

                        {{-- Info Cards --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            {{-- Deskripsi --}}
                            <div class="bg-neuBg rounded-2xl p-6 shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]">
                                <h3 class="text-sm font-semibold text-lightText mb-2">Deskripsi</h3>
                                <p class="text-darkText">{{ $lahan->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                            </div>

                            {{-- Stats --}}
                            <div class="bg-neuBg rounded-2xl p-6 shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]">
                                <h3 class="text-sm font-semibold text-lightText mb-4">Statistik</h3>
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] flex items-center justify-center">
                                        <svg class="h-6 w-6 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-darkText">{{ $devices->count() }}</p>
                                        <p class="text-sm text-lightText">Device Terhubung</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Devices List --}}
                        <div class="bg-neuBg rounded-2xl p-6 shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]">
                            <h3 class="text-lg font-bold text-darkText mb-4">Perangkat di Lahan Ini</h3>
                            
                            @if($devices->isEmpty())
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-lightText mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-lightText">Belum ada perangkat yang terhubung ke lahan ini</p>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($devices as $device)
                                        <a href="{{ route('agrinex.node-detail', $device->id) }}" 
                                            class="bg-neuBg rounded-xl p-4 shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] transition-all">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-lg bg-neuBg shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff] flex items-center justify-center">
                                                    <svg class="h-5 w-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-semibold text-darkText">{{ $device->device_name }}</p>
                                                    <p class="text-xs text-lightText">{{ $device->device_id }}</p>
                                                </div>
                                                <div class="w-2 h-2 rounded-full {{ $device->status === 'online' ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                    </div>
                </main>

                {{-- Bottom Navigation (Mobile) --}}
                @include('components.bottom-nav')
            </div>
        </div>
    </div>

</body>
</html>
