@extends('layout.master')

@section('header')
    <x-header1></x-header1>
@endsection

@section('content')
    <main class="h-full py-10 md:py-20 px-4 md:px-6">
        <div class="w-full max-w-250 mx-auto space-y-8 md:space-y-10">

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h1 class="text-secondary text-3xl md:text-6xl font-extrabold leading-tight">Budget history<span class="text-accent">.</span></h1>
                <a href="{{ route("auto.index", ["budget" => $budget->id]) }}" class="text-butter bg-secondary px-5 py-2 font-bold rounded-2xl inline-block hover:text-secondary hover:bg-butter transition duration-700 shrink-0">Back to Budget</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                @foreach ($budgetSnap as $month => $budget)
                    <article
                        class="bg-butter/40 backdrop-blur-md border border-butter/30 rounded-2xl p-5 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">

                        <!-- Header -->
                        <div class="mb-5">
                            <h2 class="text-2xl font-bold text-secondary leading-tight">{{ $month }}</h2>
                            <div class="mt-1.5">
                                <span class="text-xs font-semibold uppercase tracking-widest text-secondary/70">Budget:
                                    {{ $budget->first()->budget_amount }} {{ $budget->first()->currency }}</span>
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="flex flex-col gap-2">

                            @foreach ($budget as $b)

                                @foreach ($b->autoBudgetFieldSnapshots as $field)
                                    <div class="bg-lightbutter/50 border border-butter/40 rounded-xl overflow-hidden">
                                        <button onclick="toggleCategory(this)" type="button" aria-expanded="false"
                                            class="w-full flex justify-between items-center px-4 py-3 text-left hover:bg-butter/40 transition-colors duration-150">
                                            <span
                                                class="font-semibold text-secondary text-sm">{{ $field->field_name }}</span>
                                            <span class="flex items-center gap-2">
                                                <span
                                                    class="text-xs font-semibold text-secondary bg-butter/70 rounded-full px-3 py-0.5">{{ $field->field_amount }}
                                                    {{ $budget->first()->currency }}</span>
                                                <svg class="category-arrow" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="#004d40" stroke-width="2.5"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="6 9 12 15 18 9" />
                                                </svg>
                                            </span>
                                        </button>
                                        <div class="items-panel">
                                            @foreach ($field->autoBudgetItemSnapshots as $item)
                                                        <div
                                                            class="flex justify-between items-center px-4 py-2.5 bg-lightbutter/60 border-t border-butter/30">
                                                            <span
                                                                class="text-sm text-secondary/80 font-medium">{{ $item->item_name }}</span>
                                                            <span
                                                                class="text-sm font-semibold text-secondary">{{ $item->item_amount }}
                                                                {{ $budget->first()->currency }}</span>
                                                        </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </article>
                @endforeach

            </div>
    </main>

    <script>
function toggleCategory(btn) {
  const panel = btn.nextElementSibling;
  const arrow = btn.querySelector('.category-arrow');
  const isOpen = panel.classList.contains('open');
  panel.classList.toggle('open', !isOpen);
  arrow.classList.toggle('open', !isOpen);
  btn.setAttribute('aria-expanded', String(!isOpen));
}
</script>
@endsection
