@props(['months' => []])

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @foreach ($months as $month)
        <article
            class="bg-butter/40 backdrop-blur-md border border-butter/30 rounded-2xl p-5 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
            <div class="mb-5">
                <h2 class="text-2xl font-bold text-secondary leading-tight">{{ $month['label'] }}</h2>
                <div class="mt-1.5 flex flex-col gap-1">
                    <span class="text-xs font-semibold uppercase tracking-widest text-secondary/70">Budget:
                        {{ $month['budget'] }} {{ $month['currency'] }}</span>
                    <span class="text-xs font-semibold uppercase tracking-widest text-secondary/70">Total spent:
                        {{ $month['spent'] }} {{ $month['currency'] }}</span>
                    <span class="text-xs font-semibold uppercase tracking-widest text-secondary/70">Left at month end:
                        {{ $month['left'] }} {{ $month['currency'] }}</span>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                @foreach ($month['sections'] as $section)
                    <div class="bg-lightbutter/50 border border-butter/40 rounded-xl overflow-hidden">
                        <button type="button" aria-expanded="false" data-history-toggle
                            class="w-full flex justify-between items-center px-4 py-3 text-left hover:bg-butter/40 transition-colors duration-150">
                            <span class="font-semibold text-secondary text-sm">{{ $section['name'] }}</span>
                            <span class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-secondary bg-butter/70 rounded-full px-3 py-0.5">{{ $section['allocated'] }}
                                    {{ $month['currency'] }}</span>
                                <svg class="category-arrow" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="#004d40" stroke-width="2.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </span>
                        </button>
                        <div class="items-panel">
                            @forelse ($section['items'] as $item)
                                <div class="flex justify-between items-center px-4 py-2.5 bg-lightbutter/60 border-t border-butter/30">
                                    <span class="text-sm text-secondary/80 font-medium">{{ $item['name'] }}</span>
                                    <span class="text-sm font-semibold text-secondary">{{ $item['amount'] }}
                                        {{ $month['currency'] }}</span>
                                </div>
                            @empty
                                <p class="px-4 py-2.5 text-sm text-secondary/70 border-t border-butter/30">No items</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </article>
    @endforeach
</div>
