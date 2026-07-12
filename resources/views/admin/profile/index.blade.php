@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-neuBg pt-6 pb-10">
    <div class="max-w-4xl mx-auto px-4 md:px-6">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-darkText">Admin Profile</h1>
            <p class="text-lightText">Manage system admin configuration</p>
        </div>

        <div class="bg-neuBg rounded-2xl shadow-[6px_6px_12px_#a3b1c6,-6px_-6px_12px_#ffffff] p-8 border border-white/50">
            <form action="{{ route('admin.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Left Col --}}
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-darkText mb-2">System Name</label>
                            <input type="text" name="name" value="{{ Auth::user()->name }}" 
                                class="w-full px-4 py-3 rounded-lg bg-neuBg border-2 border-transparent focus:border-brand shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff]">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-darkText mb-2">Admin Email</label>
                            <input type="email" name="email" value="{{ Auth::user()->email }}" 
                                class="w-full px-4 py-3 rounded-lg bg-neuBg border-2 border-transparent focus:border-brand shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff]">
                        </div>
                    </div>
                    {{-- Right Col --}}
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-darkText mb-2">System Phone</label>
                            <input type="text" name="phone" value="{{ Auth::user()->phone ?? '' }}" 
                                class="w-full px-4 py-3 rounded-lg bg-neuBg border-2 border-transparent focus:border-brand shadow-[inset_2px_2px_4px_#a3b1c6,inset_-2px_-2px_4px_#ffffff]">
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" 
                        class="px-8 py-3 rounded-lg bg-brand text-white font-bold shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#a3b1c6,inset_-4px_-4px_8px_#ffffff] transition-all">
                        Update System Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
