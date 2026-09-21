@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'flex items-center gap-3 p-3.5 rounded-xl bg-emerald-50/90 border border-emerald-200/80 text-emerald-800 text-sm shadow-sm backdrop-blur-sm animate-fade-in']) }}>
        <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <p class="font-medium text-emerald-900 leading-snug">{{ $status }}</p>
    </div>
@endif
