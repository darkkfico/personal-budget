@props(['chartMonths' => []])

@if (count($chartMonths) > 0)
    <section class="bg-butter/40 backdrop-blur-md border border-butter/30 rounded-2xl p-5 md:p-8 shadow-md space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="text-secondary text-2xl md:text-3xl font-extrabold">Spending overview</h2>
            <label class="flex flex-col md:flex-row md:items-center gap-2 w-full md:w-auto">
                <span class="text-sm font-semibold uppercase tracking-widest text-secondary/70">Month</span>
                <select id="historyPieMonth"
                    class="w-full md:w-auto bg-butter border-2 border-secondary/30 rounded-xl px-4 py-2 text-secondary font-bold outline-none focus:border-secondary">
                    @foreach (array_keys($chartMonths) as $monthName)
                        <option value="{{ $monthName }}">{{ $monthName }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        <div class="flex flex-col xl:flex-row items-center gap-8">
            <div id="historyPieChart" class="w-64 h-64 md:w-80 md:h-80 shrink-0 mx-auto xl:mx-0 rounded-full border-4 border-butter" role="img" aria-label="Spending pie chart"></div>
            <div class="w-full space-y-4 min-w-0">
                <div id="historyPieSummary" class="flex flex-col gap-1 text-sm font-semibold uppercase tracking-widest text-secondary/80"></div>
                <ul id="historyPieLegend" class="space-y-2"></ul>
            </div>
        </div>
    </section>

    <script type="application/json" id="historyPieData">@json($chartMonths)</script>
    <script src="{{ asset('js/history-pie.js') }}" defer></script>
@endif
