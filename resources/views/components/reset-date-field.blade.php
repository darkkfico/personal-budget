@props([
    'value' => null,
])

@php
    $panelId = 'resetDateInfo'.str_replace('.', '', uniqid('', true));
@endphp

<div class="relative w-full">
    <div class="flex items-center gap-3 w-full">
        <input type="number" name="reset_date" placeholder="Reset Date, eg 1, 24, 3"
            value="{{ $value }}"
            {{ $attributes->class('min-w-0 flex-1') }}>
        <div class="relative group shrink-0">
            <i class="fa-solid fa-circle-info cursor-pointer text-secondary" data-info-icon
                id="{{ $panelId }}Icon" role="button" tabindex="0" aria-expanded="false"
                aria-controls="{{ $panelId }}" aria-label="Reset date info"></i>
            <div class="hidden absolute bg-butter text-secondary text-sm font-light right-0 md:right-full md:mr-3 top-8 md:-top-5 w-[min(18rem,calc(100vw-3rem))] px-6 py-4 rounded-xl max-md:group-hover:inline-block animate-myanimation z-20 shadow-lg"
                id="{{ $panelId }}">
                <p>This is the <b>day of the month</b> (1–31) when your budget cycle starts over. On that day, spending in your sections is reset for a new month, and your daily amount is counted from then until the next reset.</p>
            </div>
        </div>
    </div>
    {{ $slot }}
</div>
