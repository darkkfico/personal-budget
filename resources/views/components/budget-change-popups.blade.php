@props(['current' => 'custom'])

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
