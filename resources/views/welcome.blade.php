<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" style="background-color: #E0E5EC;">

<head>
    @include('partials.head')
    @include('partials.dashboard-scripts')
</head>

<body x-data="dashboard()"
    class="h-full min-h-screen w-full bg-neuBg text-darkText font-sans antialiased selection:bg-brand selection:text-white relative overflow-x-hidden">

    <div class="min-h-screen w-full bg-neuBg font-sans text-darkText" x-data="{ currentView: window.location.hash === '#profile' ? 'profile' : 'dashboard' }" @hashchange.window="currentView = window.location.hash === '#profile' ? 'profile' : 'dashboard'">

    {{-- Global Splash Screen --}}
    @include('components.splash')

    <div class="min-h-screen w-full bg-neuBg font-sans text-darkText" x-data="{ currentView: window.location.hash === '#profile' ? 'profile' : 'dashboard' }" @hashchange.window="currentView = window.location.hash === '#profile' ? 'profile' : 'dashboard'">
    <div class="relative z-10 flex h-full min-h-screen">

        {{-- Sidebar — hidden on mobile, sticky on desktop --}}
        <div class="hidden md:flex md:flex-shrink-0">
            @include('components.sidebar')
        </div>

        {{-- Main column --}}
        <div class="flex-1 min-w-0 flex flex-col min-h-screen overflow-x-hidden">

            {{-- Sticky header --}}
            <div class="sticky top-0 z-30 w-full bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] transition-colors duration-300">
                <div class="px-4 md:px-6 xl:px-8">
                    @include('components.header')
                </div>
            </div>

            {{-- Dashboard View --}}
            <main x-show="currentView === 'dashboard'" x-transition.opacity
                class="flex-1 px-4 md:px-6 xl:px-8 pt-6 pb-28 md:pb-10 w-full max-w-[1400px] mx-auto space-y-6 md:space-y-8">

                {{-- Section 1: Weather + Devices --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 md:gap-8">
                    <div class="lg:col-span-4 flex flex-col">
                        @include('components.weather-summary')
                    </div>
                    <div class="lg:col-span-8 flex flex-col">
                        @include('components.devices-tank')
                    </div>
                </div>

                {{-- Section 2: Tank + Metrics --}}
                <div class="space-y-5 md:space-y-8">
                    @include('components.water-tank')
                    @include('components.metrics-cards')
                </div>

                {{-- Section 3: Analytics --}}
                <div class="space-y-5 md:space-y-8">
                    @include('components.environmental-charts')
                    @include('components.weekly-tasks')
                    @include('components.usage-charts')
                    @include('components.location-maps')
                </div>

                <footer class="text-center pt-10 pb-2 text-xs text-lightText font-medium tracking-wide">
                    &copy; {{ date('Y') }} AgriNex Smart Irrigation
                </footer>
            </main>

            {{-- Profile View --}}
            <main x-show="currentView === 'profile'" x-transition.opacity
                class="flex-1 px-4 md:px-6 xl:px-8 pt-6 pb-28 md:pb-10 w-full max-w-[800px] mx-auto space-y-8">
                
                {{-- Profile Header Card --}}
                <div class="bg-neuBg rounded-3xl shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] p-8">
                    <div class="flex flex-col md:flex-row items-center gap-8">
                        <div class="text-center md:text-left flex-1">
                            <h2 class="text-3xl font-extrabold text-darkText">{{ Auth::user()->full_name ?? Auth::user()->name }}</h2>
                            <p class="text-lightText font-medium mt-2">{{ Auth::user()->email }}</p>
                            <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-3">
                                <span class="px-4 py-1.5 rounded-full bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-brand text-xs font-bold uppercase tracking-wider">
                                    {{ Auth::user()->role }}
                                </span>
                                <span class="px-4 py-1.5 rounded-full bg-green-500/10 text-green-600 text-xs font-bold uppercase tracking-wider border border-green-500/20">
                                    Active Account
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Account Details Form --}}
                <div class="bg-neuBg rounded-3xl shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] p-8">
                    <h3 class="text-xl font-bold text-darkText mb-8 flex items-center gap-3">
                        <svg class="w-6 h-6 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Informasi Akun
                    </h3>
                    
                    <form id="account-info-form" action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-lightText ml-1">Nama Lengkap</label>
                                <input type="text" name="full_name" value="{{ Auth::user()->full_name ?? Auth::user()->name }}" required
                                    class="w-full px-5 py-4 rounded-2xl bg-neuBg border-none focus:ring-2 focus:ring-brand/20 shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-darkText font-medium transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-lightText ml-1">Email
                                    @if(Auth::user()->google_id)
                                        <span class="text-xs text-brand font-semibold">(OAuth)</span>
                                    @endif
                                </label>
                                <input type="email" name="email"
                                    value="{{ Auth::user()->email }}" 
                                    @if(Auth::user()->google_id) readonly @endif
                                    required
                                    class="w-full px-5 py-4 rounded-2xl bg-neuBg border-none shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-darkText font-medium @if(Auth::user()->google_id) bg-neuBg/50 text-lightText cursor-not-allowed @else focus:ring-2 focus:ring-brand/20 transition-all @endif">
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-sm font-bold text-lightText ml-1">Nomor Telepon</label>
                                <input type="tel" name="phone_number" value="{{ Auth::user()->phone_number ?? '' }}" placeholder="Belum diatur"
                                    class="w-full px-5 py-4 rounded-2xl bg-neuBg border-none focus:ring-2 focus:ring-brand/20 shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-darkText font-medium transition-all">
                            </div>
                        </div>
                        <div class="pt-4">
                            <button type="submit" class="w-full md:w-auto px-10 py-4 rounded-2xl bg-brand text-white font-bold shadow-[6px_6px_12px_#a3b1c6,-6px_-6px_12px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] active:scale-95 transition-all">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
                {{-- Security & Password --}}
                <div class="bg-neuBg rounded-3xl shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff] p-8">
                    <h3 class="text-xl font-bold text-darkText mb-8 flex items-center gap-3">
                        <svg class="w-6 h-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Keamanan & Sandi
                    </h3>

                    @if(Auth::user()->google_id && !Auth::user()->password_hash)
                        {{-- OAuth Account: No password --}}
                        <div class="bg-neuBg rounded-2xl shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] p-6 space-y-4 border border-white/20">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center flex-shrink-0 shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]">
                                    <svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-darkText text-lg">Akun OAuth (Google)</h4>
                                    <p class="text-sm text-lightText mt-2 leading-relaxed">
                                        Akun Anda terhubung dengan Google. Gunakan login Google untuk mengakses aplikasi. Tidak perlu mengatur sandi lokal.
                                    </p>
                                    <div class="mt-4 flex items-center gap-2 text-xs">
                                        <span class="px-3 py-1.5 rounded-full bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] text-brand font-bold">
                                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z"/>
                                            </svg>
                                            Google Connected
                                        </span>
                                        <span class="px-3 py-1.5 rounded-full bg-green-500/10 text-green-600 font-bold border border-green-500/20">
                                            Keamanan Tinggi
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Local Account or OAuth with local password --}}
                        <form id="password-form" action="{{ route('profile.password') }}" method="POST" class="space-y-6">
                            @csrf
                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-lightText ml-1">Sandi Saat Ini</label>
                                    <input type="password" name="current_password" required
                                        class="w-full px-5 py-4 rounded-2xl bg-neuBg border-none focus:ring-2 focus:ring-brand/20 shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-darkText transition-all">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-lightText ml-1">Sandi Baru</label>
                                        <input type="password" name="password" required
                                            @input="fetch('{{ route('profile.password-strength') }}?password=' + encodeURIComponent($el.value)).then(r => r.json()).then(d => Object.assign($el, { dataset: { strength: d.strength, level: d.level, feedback: JSON.stringify(d.feedback) } }))"
                                            class="w-full px-5 py-4 rounded-2xl bg-neuBg border-none focus:ring-2 focus:ring-brand/20 shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-darkText transition-all">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-lightText ml-1">Konfirmasi Sandi</label>
                                        <input type="password" name="password_confirmation" required
                                            class="w-full px-5 py-4 rounded-2xl bg-neuBg border-none focus:ring-2 focus:ring-brand/20 shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] text-darkText transition-all">
                                    </div>
                                </div>
                            </div>
                            <div class="pt-4 flex flex-col md:flex-row gap-4 items-center justify-between">
                                <button type="submit" class="w-full md:w-auto px-10 py-4 rounded-2xl bg-yellow-500 text-white font-bold shadow-[6px_6px_12px_#a3b1c6,-6px_-6px_12px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] active:scale-95 transition-all">
                                    Perbarui Sandi
                                </button>
                            </div>
                        </form>
                    @endif

                    {{-- Logout Button (Always show) --}}
                    <div class="mt-8 pt-8 border-t border-white/10">
                        <form action="{{ route('profile.logout') }}" method="POST" class="inline-block w-full md:w-auto">
                            @csrf
                            <button type="submit" class="w-full md:w-auto px-10 py-4 rounded-2xl bg-red-500 text-white font-bold shadow-[6px_6px_12px_#a3b1c6,-6px_-6px_12px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] active:scale-95 transition-all">
                                Keluar Sesi
                            </button>
                        </form>
                    </div>
                </div>

                <footer class="text-center pt-10 pb-12 text-xs text-lightText font-medium tracking-wide">
                    &copy; {{ date('Y') }} AgriNex Smart Irrigation • Built for Excellence
                </footer>
            </main>
        </div>
    </div>

    {{-- Mobile bottom nav --}}
    @include('components.bottom-nav')

    @include('components.modals')

    </div>

</body>
</html>
