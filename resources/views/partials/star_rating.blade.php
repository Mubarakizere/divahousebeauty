{{-- Star Rating Component --}}
@php
    $rating = $rating ?? 0;
    $size = $size ?? 'md'; // sm, md, lg
    $interactive = $interactive ?? false;
    
    $sizeClasses = [
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-xl',
    ];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div class="inline-flex items-center gap-0.5 {{ $sizeClass }}">
    @for ($i = 1; $i <= 5; $i++)
        @php
            $filled = $i <= floor($rating);
            $half = !$filled && $i <= ceil($rating);
        @endphp
        
        @if ($interactive)
            <button type="button" 
                    class="star-btn text-slate-300 hover:text-yellow-400 transition-colors"
                    data-rating="{{ $i }}"
                    @if(isset($onChange)) onclick="{{ $onChange }}({{ $i }})" @endif>
                <i class="la {{ $filled ? 'la-star' : ($half ? 'la-star-half-alt' : 'la-star-o') }}"></i>
            </button>
        @else
            <i class="la {{ $filled ? 'la-star' : ($half ? 'la-star-half-alt' : 'la-star-o') }} {{ $filled || $half ? 'text-yellow-400' : 'text-slate-300' }}"></i>
        @endif
    @endfor
    
    @if (isset($showCount) && $showCount)
        <span class="ml-1.5 text-sm text-slate-600">({{ number_format($rating, 1) }})</span>
    @endif
</div>
