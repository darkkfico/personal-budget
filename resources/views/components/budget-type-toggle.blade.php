@props(['current' => 'auto'])

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
                    class="hidden absolute bg-butter text-secondary text-sm font-light left-7 -top-4 w-72 px-5 py-3 rounded-xl group-hover:block shadow-lg z-10">
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
                    class="hidden absolute bg-butter text-secondary text-sm font-light left-7 -top-4 w-72 px-5 py-3 rounded-xl group-hover:block shadow-lg z-10">
                    <p>Make your own monthly plan <b>(divide your budget as you want)</b>.</p>
                </div>
            </div>
        </div>
    </div>
</div>
