<!DOCTYPE html>
<html lang="id" class="h-full" style="background-color: #E0E5EC;">
<head>
    @include('partials.head')
    @include('partials.dashboard-scripts')
</head>
<body x-data="lahanPantauPage()" x-init="init()" 
    class="h-full min-h-screen w-full bg-neuBg text-darkText font-sans antialiased">

    <div class="min-h-screen w-full bg-neuBg font-sans text-darkText">
        <div class="relative z-10 flex h-full min-h-screen" x-cloak>
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
                    
                    <div class="bg-neuBg rounded-[2.5rem] p-6 md:p-8 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
                        
                        {{-- Header --}}
                        <div class="flex justify-between items-center mb-8 border-b border-[#a3b1c6]/30 pb-6">
                            <div>
                                <h2 class="text-xl md:text-2xl font-extrabold tracking-tight text-darkText">Lahan Pantau</h2>
                                <p class="text-lightText text-xs md:text-sm mt-1">Kelola dan monitor area lahan pertanian Anda</p>
                            </div>
                            <button @click="openCreateModal" 
                                class="px-6 py-3 rounded-xl bg-brand text-white font-semibold shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_rgba(0,0,0,0.1)] transition-all">
                                + Tambah Lahan
                            </button>
                        </div>

                        @include('lahan-pantau.partials.list')
                        
                    </div>
                </main>
            </div>
        </div>
        
        {{-- Modal harus di dalam scope x-data --}}
        @include('lahan-pantau.partials.modal')
    </div>

    @include('lahan-pantau.partials.script')

</body>
</html>
