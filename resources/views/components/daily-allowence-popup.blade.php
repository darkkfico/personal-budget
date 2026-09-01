@props(['show' => false, 'amount' => 0, 'currency' => ''])

@if ($show)
    <div class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-secondary/50"></div>
        <div class="relative w-full max-w-md bg-butter rounded-2xl shadow-2xl p-6 space-y-4" role="dialog" aria-modal="true" aria-labelledby="dailyAllowenceTitle">
            <h2 id="dailyAllowenceTitle" class="text-secondary text-xl font-extrabold">Daily allowance reached</h2>
            <p class="text-secondary text-sm font-semibold">
                You have used today's daily allowance. Left for today is
                <span class="font-extrabold">{{ $amount }} {{ $currency }}</span>.
            </p>
            <p class="text-secondary text-sm font-semibold">
                You can still add expenses, but they will go beyond what was planned for today.
            </p>
            <form method="POST" action="{{ route('dailyAllowence.acknowledge') }}">
                @csrf
                <button type="submit" class="w-full px-4 py-2 rounded-xl bg-secondary text-butter font-bold cursor-pointer">
                    Got it
                </button>
            </form>
        </div>
    </div>
@endif
