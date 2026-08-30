<div id="itemDeletePopup" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div id="itemDeleteBackdrop" class="absolute inset-0 bg-secondary/50"></div>
    <div class="relative w-full max-w-md bg-butter rounded-2xl shadow-2xl p-6 space-y-4" role="dialog" aria-modal="true" aria-labelledby="itemDeleteTitle">
        <h2 id="itemDeleteTitle" class="text-secondary text-xl font-extrabold">Delete item?</h2>
        <p id="itemDeleteName" class="text-secondary text-sm font-bold"></p>
        <p class="text-secondary text-sm font-semibold">
            Permanent deletion removes this expense from your budget <span class="font-extrabold">and</span> from budget history.
            It will not appear in this month's history or the pie chart.
        </p>
        <p class="text-secondary text-sm font-semibold">
            If you only remove it from the budget, it disappears here, but the record stays in history for this month.
        </p>
        <form id="itemDeleteForm" method="POST" class="space-y-2">
            @csrf
            @method('DELETE')
            <input type="hidden" name="permanent" id="itemDeletePermanent" value="0">
            <button type="submit" id="itemDeleteKeepHistory" class="w-full px-4 py-2 rounded-xl border-2 border-secondary text-secondary font-bold cursor-pointer">
                Remove from budget (keep in history)
            </button>
            <button type="submit" id="itemDeletePermanentBtn" class="w-full px-4 py-2 rounded-xl bg-secondary text-butter font-bold cursor-pointer">
                Delete permanently
            </button>
            <button type="button" id="itemDeleteCancel" class="w-full px-4 py-2 rounded-xl text-secondary font-bold cursor-pointer">
                Cancel
            </button>
        </form>
    </div>
</div>
