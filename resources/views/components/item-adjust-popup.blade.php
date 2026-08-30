<div id="itemAdjustPopup" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div id="itemAdjustBackdrop" class="absolute inset-0 bg-secondary/50"></div>
    <div class="relative w-full max-w-sm bg-butter rounded-2xl shadow-2xl p-6 space-y-4" role="dialog" aria-modal="true" aria-labelledby="itemAdjustTitle">
        <h2 id="itemAdjustTitle" class="text-secondary text-xl font-extrabold"></h2>
        <p id="itemAdjustHint" class="text-secondary text-sm font-semibold"></p>
        <form id="itemAdjustForm" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            <input type="hidden" name="adjust" id="itemAdjustValue">
            <label class="flex flex-col items-start gap-1">
                <span id="itemAdjustLabel" class="text-secondary text-sm font-semibold uppercase"></span>
                <input type="number" id="itemAdjustAmount" min="1" step="1" placeholder="0" required
                    class="w-full bg-butter/60 border-2 border-lightbutter px-3 py-3 text-md text-secondary outline-none focus:border-secondary rounded-2xl placeholder:text-secondary/70">
            </label>
            <div class="flex gap-2">
                <button type="button" id="itemAdjustCancel" class="flex-1 px-4 py-2 rounded-xl border-2 border-secondary text-secondary font-bold cursor-pointer">Cancel</button>
                <button type="submit" id="itemAdjustSubmit" class="flex-1 px-4 py-2 rounded-xl bg-secondary text-butter font-bold cursor-pointer"></button>
            </div>
        </form>
    </div>
</div>
