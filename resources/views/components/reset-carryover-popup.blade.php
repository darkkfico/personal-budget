@props(['prompt', 'action'])

@if ($prompt)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-secondary/50"></div>
        <div class="relative w-full max-w-md bg-butter rounded-2xl shadow-2xl p-6 space-y-4" role="dialog" aria-modal="true" aria-labelledby="resetCarryoverTitle">
            <h2 id="resetCarryoverTitle" class="text-secondary text-xl font-extrabold">Today is your reset date</h2>
            <p class="text-secondary text-sm font-semibold">
                Do you want to subtract <span class="text-secondary">{{ $prompt['field_name'] }}</span>
                ({{ $prompt['amount'] }} {{ $prompt['currency'] }}) from this month's total budget
                of {{ $prompt['budget_amount'] }} {{ $prompt['currency'] }}?
            </p>
            <form method="POST" action="{{ $action }}" class="flex gap-2">
                @csrf
                <button type="submit" name="apply" value="0"
                    class="flex-1 px-4 py-2 rounded-xl border-2 border-secondary text-secondary font-bold cursor-pointer">No</button>
                <button type="submit" name="apply" value="1"
                    class="flex-1 px-4 py-2 rounded-xl bg-secondary text-butter font-bold cursor-pointer">Yes, subtract</button>
            </form>
        </div>
    </div>
@endif
