@if (session('leftover_allocation'))
    @php $leftover = session('leftover_allocation'); @endphp
    <div id="leftoverAllocationPopup" class="fixed top-0 left-0 z-[60] flex h-dvh w-screen items-center justify-center p-4">
        <div id="leftoverAllocationBackdrop" class="absolute inset-0 bg-secondary/50"></div>
        <div class="relative w-full max-w-md bg-butter rounded-2xl shadow-2xl p-6 space-y-4" role="dialog" aria-modal="true" aria-labelledby="leftoverAllocationTitle">
            <h2 id="leftoverAllocationTitle" class="text-secondary text-xl font-extrabold">Unallocated money</h2>
            <p class="text-secondary text-sm font-semibold">
                You have <span class="font-extrabold">{{ money($leftover['amount'], $leftover['currency']) }}</span>
                that you haven't added anywhere. Where would you like to add it?
            </p>
            <div class="space-y-2">
                <label for="leftoverSectionSelect" class="text-secondary text-sm font-bold">Add to section</label>
                <select id="leftoverSectionSelect"
                    class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary outline-none"
                    required>
                    <option value="">Select a section</option>
                    @foreach ($leftover['sections'] as $number => $name)
                        <option value="{{ $number }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="button" id="leftoverAllocationBack"
                    class="flex-1 px-4 py-2 rounded-xl border-2 border-secondary text-secondary font-bold cursor-pointer">
                    Go back
                </button>
                <button type="button" id="leftoverAllocationConfirm"
                    class="flex-1 px-4 py-2 rounded-xl bg-secondary text-butter font-bold cursor-pointer">
                    Add there
                </button>
            </div>
        </div>
    </div>
@endif
