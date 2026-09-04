@props(['prompt', 'action'])

@if ($prompt)
    <canvas id="resetConfetti" class="pointer-events-none fixed inset-0 z-[55]"></canvas>
    <div class="fixed top-0 left-0 z-[60] flex h-dvh w-screen items-center justify-center p-4">
        <div class="absolute inset-0 bg-secondary/50"></div>
        <div class="relative w-full max-w-md overflow-hidden rounded-3xl bg-butter shadow-2xl ring-4 ring-accent/70"
            role="dialog" aria-modal="true" aria-labelledby="resetCarryoverTitle">
            <div class="bg-linear-to-r from-accent to-primary px-6 py-5 text-center">
                <p class="text-4xl" aria-hidden="true">🎉</p>
                <h2 id="resetCarryoverTitle" class="mt-2 text-2xl font-extrabold text-butter">You saved money!</h2>
            </div>
            <div class="space-y-4 p-6">
                <p class="text-center text-secondary text-sm font-semibold">
                    Today is your reset date, and you have leftover money from last month.
                    That's a win — you spent less than you planned.
                </p>
                <p class="text-center text-secondary text-lg font-extrabold">
                    {{ money($prompt['amount'], $prompt['currency']) }} saved
                </p>
                <p class="text-center text-secondary text-sm font-semibold">
                    This leftover will be an <span class="font-extrabold">add-on</span> to this month's budget
                    ({{ money($prompt['budget_amount'], $prompt['currency']) }} → {{ money($prompt['new_budget'], $prompt['currency']) }}).
                    Choose which section should get it.
                </p>
                <form method="POST" action="{{ $action }}" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label for="resetLeftoverSection" class="text-secondary text-sm font-bold">Add to section</label>
                        <select id="resetLeftoverSection" name="section_id" required
                            class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary outline-none">
                            <option value="">Select a section</option>
                            @foreach ($prompt['sections'] as $section)
                                <option value="{{ $section['id'] }}">{{ $section['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="w-full rounded-xl bg-accent px-4 py-3 font-extrabold text-butter cursor-pointer hover:bg-secondary transition">
                        Add it to this month
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/reset-celebration.js') }}"></script>
@endif
