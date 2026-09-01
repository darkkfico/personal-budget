@props(['current' => 'custom'])

<div class="flex flex-col items-start w-full space-y-4 text-secondary text-xl font-bold">
    <p class="text-secondary/80 text-base font-semibold">Change type of budget</p>
    <div class="flex flex-wrap gap-6">
        <div class="flex items-center gap-3">
            <input type="radio" id="typeAuto" name="budget_type_ui" value="auto"
                class="size-4 accent-secondary cursor-pointer"
                {{ $current === 'auto' ? 'checked' : '' }}>
            <label for="typeAuto" class="cursor-pointer">Auto</label>
            <div class="relative group">
                <i class="fa-solid fa-circle-info text-base text-secondary/70 cursor-help"></i>
                <div
                    class="hidden absolute bg-butter text-secondary text-sm font-light left-0 md:left-7 top-8 md:-top-4 w-[min(18rem,calc(100vw-3rem))] px-5 py-3 rounded-xl group-hover:block shadow-lg z-10">
                    <p><b>Let us make your monthly plan</b>. We divide your budget into <b>50% Groceries</b>,
                        <b>30% Wishes</b>, and <b>20% Savings</b>.
                    </p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <input type="radio" id="typeCustom" name="budget_type_ui" value="custom"
                class="size-4 accent-secondary cursor-pointer"
                {{ $current === 'custom' ? 'checked' : '' }}>
            <label for="typeCustom" class="cursor-pointer">Custom</label>
            <div class="relative group">
                <i class="fa-solid fa-circle-info text-base text-secondary/70 cursor-help"></i>
                <div
                    class="hidden absolute bg-butter text-secondary text-sm font-light left-0 md:left-7 top-8 md:-top-4 w-[min(18rem,calc(100vw-3rem))] px-5 py-3 rounded-xl group-hover:block shadow-lg z-10">
                    <p>Make your own monthly plan <b>(divide your budget as you want)</b>.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
<div id="customSectionsPopup" class="hidden fixed top-0 left-0 z-50 flex h-dvh w-screen items-center justify-center p-4" data-show-on-load="{{ $current === 'custom' ? '1' : '0' }}">
    <div id="customSectionsBackdrop" class="absolute inset-0 bg-secondary/50"></div>
    <div class="relative w-full max-w-md bg-butter rounded-2xl shadow-2xl p-6 space-y-4" role="dialog" aria-modal="true" aria-labelledby="customSectionsTitle">
        @if ($current === 'auto')
            <h2 id="customSectionsTitle" class="text-secondary text-xl font-extrabold">Keep or lose items</h2>
            <p class="text-secondary text-sm font-semibold">
                If you keep the <span class="font-extrabold">same section names in the same order</span>
                as your Auto budget (Groceries, Wishes, Savings), those items will move to your Custom budget.
            </p>
            <p class="text-secondary text-sm font-semibold">
                If you change the names, reorder them, or replace the sections, <span class="font-extrabold">the items from those sections will be lost</span>.
            </p>
        @else
            <h2 id="customSectionsTitle" class="text-secondary text-xl font-extrabold">Keep your items</h2>
            <p class="text-secondary text-sm font-semibold">
                If you want to keep your current items, <span class="font-extrabold">rename the existing sections</span>.
                Items already in those sections will stay.
            </p>
            <p class="text-secondary text-sm font-semibold">
                If you <span class="font-extrabold">delete all sections and add new ones</span>, the items in the old sections will be deleted.
            </p>
        @endif
        <button type="button" id="customSectionsGotIt" class="w-full px-4 py-2 rounded-xl bg-secondary text-butter font-bold cursor-pointer">
            Got it
        </button>
    </div>
</div>

@if ($current === 'custom')
    <div id="autoConvertPopup" class="hidden fixed top-0 left-0 z-50 flex h-dvh w-screen items-center justify-center p-4">
        <div id="autoConvertBackdrop" class="absolute inset-0 bg-secondary/50"></div>
        <div class="relative w-full max-w-md bg-butter rounded-2xl shadow-2xl p-6 space-y-4" role="dialog" aria-modal="true" aria-labelledby="autoConvertTitle">
            <h2 id="autoConvertTitle" class="text-secondary text-xl font-extrabold">Items will not transfer</h2>
            <p class="text-secondary text-sm font-semibold">
                Switching to Auto does not copy your Custom items.
                If you want to keep the same data, <span class="font-extrabold">take screenshots of your budget first</span>.
            </p>
            <p class="text-secondary text-sm font-semibold">
                After you change to Auto, you will need to <span class="font-extrabold">enter the items manually</span>.
            </p>
            <button type="button" id="autoConvertGotIt" class="w-full px-4 py-2 rounded-xl bg-secondary text-butter font-bold cursor-pointer">
                Got it
            </button>
        </div>
    </div>
@endif
@endpush
