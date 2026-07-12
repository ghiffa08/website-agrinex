<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    @include('partials.head')
    
    <!-- Alpine JS Admin Layout State -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminLayout', () => ({
                sidebarOpen: false,
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                }
            }));
        });
    </script>
</head>

<body x-data="adminLayout()" 
    class="h-full bg-[#f4f6f9] text-slate-800 transition-colors duration-300 font-sans antialiased">

    {{-- Global Splash Screen --}}
    @include('components.splash')

    <div class="flex h-screen overflow-hidden">
        
        {{-- Sidebar Component --}}
        @include('components.admin.sidebar')

        {{-- Main Content Area --}}
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            
            {{-- Navbar Component --}}
            @include('components.admin.navbar')

            {{-- Main Page Content --}}
            <main class="w-full grow p-6 lg:p-8 max-w-7xl mx-auto">
                
                {{-- Page Header (Title) --}}
                @hasSection('header')
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">@yield('header')</h1>
                    
                    {{-- Breadcrumbs (optional) --}}
                    @hasSection('breadcrumbs')
                    <nav class="flex text-sm text-slate-500 font-medium" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            @yield('breadcrumbs')
                        </ol>
                    </nav>
                    @endif
                </div>
                @endif

                {{-- Actual Content --}}
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="mt-auto py-6 px-6 lg:px-8 border-t border-slate-200 flex justify-between items-center text-sm text-slate-500">
                <div>
                    Copyright &copy; {{ date('Y') }} <span class="font-semibold text-emerald-600">AgriNex</span>.
                </div>
                <div>
                    Made with Tailwind & Alpine
                </div>
            </footer>
        </div>
    </div>
    
    {{-- Any additional scripts for admin --}}
    @stack('scripts')
</body>
</html>
