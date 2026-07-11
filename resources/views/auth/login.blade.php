<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - AgriNex</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-neuBg text-darkText min-h-screen flex items-center justify-center p-4 selection:bg-brand/20">

    <div class="w-full max-w-md bg-neuBg rounded-[2rem] p-8 md:p-10 shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-neuBg shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] mb-6">
                <img src="{{ asset('AgrinexLogo.jpg') }}" alt="AgriNex Logo" class="w-12 h-12 object-contain rounded-lg">
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-darkText mb-1">Welcome Back</h1>
            <p class="text-sm font-medium text-lightText">AgriNex IoT System</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-600 text-sm border border-red-200">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
            @csrf
            
            <!-- Username/Email -->
            <div class="space-y-2">
                <label class="block text-sm font-bold text-lightText ml-1">Username or Email</label>
                <div class="relative">
                    <input type="text" name="username" required autofocus
                        class="w-full bg-neuBg text-darkText px-5 py-3.5 rounded-xl outline-none
                        shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]
                        focus:shadow-[inset_6px_6px_12px_#a3b1c6,inset_-6px_-6px_12px_#ffffff]
                        transition-shadow duration-200">
                </div>
            </div>

            <!-- Password -->
            <div class="space-y-2">
                <label class="block text-sm font-bold text-lightText ml-1">Password</label>
                <div class="relative">
                    <input type="password" name="password" required
                        class="w-full bg-neuBg text-darkText px-5 py-3.5 rounded-xl outline-none
                        shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]
                        focus:shadow-[inset_6px_6px_12px_#a3b1c6,inset_-6px_-6px_12px_#ffffff]
                        transition-shadow duration-200">
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center ml-1">
                <div class="relative flex items-center justify-center w-5 h-5 bg-neuBg rounded 
                    shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff]">
                    <input type="checkbox" name="remember" id="remember" class="peer opacity-0 absolute inset-0 cursor-pointer w-full h-full">
                    <svg class="w-3.5 h-3.5 text-brand opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <label for="remember" class="ml-3 text-sm font-semibold text-lightText cursor-pointer">Remember me</label>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                class="w-full py-4 rounded-xl font-bold text-brand bg-neuBg
                shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]
                active:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]
                transition-all duration-200 active:scale-95">
                Sign In
            </button>
        </form>

        <div class="flex items-center my-8">
            <div class="flex-grow border-t border-[#a3b1c6]/30"></div>
            <span class="px-4 text-xs font-semibold tracking-wider text-lightText uppercase">Or continue with</span>
            <div class="flex-grow border-t border-[#a3b1c6]/30"></div>
        </div>

        <!-- Google Login -->
        <a href="{{ route('google.login') }}" 
            class="flex items-center justify-center gap-3 w-full py-3.5 rounded-xl font-bold text-darkText bg-neuBg
            shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]
            active:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff]
            transition-all duration-200 active:scale-95">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Sign in with Google
        </a>

    </div>

</body>
</html>
