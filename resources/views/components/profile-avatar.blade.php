{{-- Profile Avatar Component
     Reusable avatar display with fallback to initials
     Best practice: single source of truth for avatar rendering
--}}

@php
    use Illuminate\Support\Str;

    $user = $user ?? Auth::user();
    $size = $size ?? 'md';
    $showBorder = $showBorder ?? true;
    $borderSize = $borderSize ?? '4';
    $borderColor = $borderColor ?? 'border-white/30';
    $showShadow = $showShadow ?? true;
    
    // Size classes mapping
    $sizes = [
        'sm' => ['container' => 'w-10 h-10 text-base', 'initials' => 'text-lg'],
        'md' => ['container' => 'w-16 h-16 text-lg', 'initials' => 'text-xl'],
        'lg' => ['container' => 'w-24 h-24 text-xl', 'initials' => 'text-3xl'],
        'xl' => ['container' => 'w-32 h-32 text-2xl', 'initials' => 'text-4xl'],
    ];
    
    $sizeClass = $sizes[$size]['container'] ?? $sizes['md']['container'];
    $textClass = $sizes[$size]['initials'] ?? $sizes['md']['initials'];
    
    // Get avatar URL via accessor
    $avatarUrl = $user->avatar_url;
    
    // Determine if we should show initials
    $showInitials = !$avatarUrl;
    
    // Generate initials from name or email
    $initials = '';
    if ($showInitials) {
        $name = $user->full_name ?? $user->name ?? $user->email ?? 'U';
        $parts = explode(' ', $name);
        foreach ($parts as $part) {
            if (strlen($part) > 0) {
                $initials .= strtoupper(substr($part, 0, 1));
                if (strlen($initials) >= 2) break;
            }
        }
        $initials = $initials ?: strtoupper(substr($name, 0, 1));
    }
@endphp

<div class="{{ $sizeClass }} rounded-full overflow-hidden flex items-center justify-center relative
    {{ $showBorder ? 'border-'. $borderSize .' ' . $borderColor : '' }}
    {{ $showShadow ? 'shadow-[6px_6px_12px_#a3b1c6,-6px_-6px_12px_#ffffff]' : '' }}">

    @if($avatarUrl)
        {{-- Avatar Image --}}
        <img src="{{ $avatarUrl }}" 
            alt="{{ $user->full_name ?? $user->name ?? 'User' }}" 
            class="w-full h-full object-cover"
            loading="lazy"
            onerror="this.style.display='none'; this.parentElement.classList.add('bg-gradient-to-br', 'from-brand/20', 'to-brand/5'); this.parentElement.innerHTML='<span class=\'{{ $textClass }} font-bold text-brand\'>{{ addslashes($initials) }}</span>'">
    @else
        {{-- Initials Fallback --}}
        <div class="w-full h-full bg-gradient-to-br from-brand/20 to-brand/5 flex items-center justify-center">
            <span class="{{ $textClass }} font-bold text-brand">{{ $initials }}</span>
        </div>
    @endif

</div>
