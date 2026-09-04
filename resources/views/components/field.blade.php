@php
    $isAuto = ($budgetType ?? session('type')) === 'auto';
@endphp
<div id='card'
    class='bg-butter/40 rounded-2xl animate-myanimation px-4 md:px-8 py-8 md:py-10 space-y-4 hover:-translate-y-1 transition-all w-full xl:max-w-[48%] min-w-0 h-fit self-start'>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
        <h1 class='text-secondary text-2xl md:text-3xl font-extrabold break-words'>{{ $field }} </h1>
        <div class="relative">
                @php
                    $sum = 0;
                @endphp
                @foreach($items as $item)
                    @if($item->field_name == $field)
                        @php
                            $sum = $item->sum("item_amount");
                        @endphp
                    @endif
                @endforeach
                <div class="flex flex-col items-start md:items-end">
                    <span class="text-lightbutter text-sm md:text-md font-semibold">BUDGET: {{ money($fieldAmount, $budgetCurrency) }}</span>
                    <span class='text-butter text-lg md:text-xl font-extrabold inline-block'>Left: {{ money(intval($fieldAmount) - $sum, $budgetCurrency) }}</span>
                </div>
        </div>
    </div>


    <form action="{{ $isAuto ? route('auto.item.create') : route('custom.item.create')  }}" method="POST"
        class="w-full flex flex-col items-center space-y-5 p-3 bg-butter/40 rounded-2xl relative">
        <input type="hidden" value="{{ $field }}" name="field">
        <div class="flex flex-col md:flex-row justify-center items-stretch md:items-center gap-4 w-full">
            <div class="flex flex-col items-start w-full">
                <span class="text-lightbutter uppercase text-sm font-semibold">item name</span>
                <input type="text" name="item" placeholder="eg. Food, Drink..."
                    class='w-full bg-butter/60 border-2 border-lightbutter px-3 py-3 text-md text-secondary outline-none focus:outline-none focus:border-secondary focus:transtition focus:duration-500 placeholder:text-secondary/70 rounded-2xl'>
            </div>
            <div class="flex flex-col items-start space-y-1 w-full">
                <span class="text-lightbutter uppercase text-sm font-semibold">amount spent</span>
                <input type="number" name="amount" placeholder="0" 
                    class='w-full bg-butter/60 border-2 border-lightbutter px-3 py-3 text-md text-secondary outline-none focus:border-secondary focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary/70 rounded-2xl'>
            </div>
        </div>
        @error("item")
            <span class="px-2 py-0.5 text-sm text-red-600 absolute top-21 left-1">{{ $message }}</span> 
        @enderror 
  
        <button class="px-3 py-2 rounded-xl bg-secondary text-lightbutter w-full">+ Add expense</button>
        <p class="text-sm text-lightbutter">Type the item name and how much you spent, then tap "Add expense"</p>
    </form>

    <div class="w-full space-y-3 pt-2">
            @if($items)
                @foreach($items as $item)
                    @if($item->field_name == $field)
                        <div class="w-full min-w-0 bg-butter/40 rounded-2xl p-4 space-y-3">
                            <div class="min-w-0">
                                <p class="text-secondary text-base md:text-lg font-bold break-words">{{ $item->item_name }}</p>
                                <p class="text-xs text-secondary/70 mt-0.5">Created: {{ $item->created_at->format("d-m-Y") }} · Updated: {{ $item->updated_at->format("d-m-Y") }}</p>
                            </div>
                            <div class="flex items-center gap-2 min-w-0">
                                <form method="POST" action="{{ $isAuto ? route('auto.item.edit', ["item" => $item->id]) : route('custom.item.edit', ['item' => $item->id]) }}"
                                    class="flex items-center gap-2 min-w-0 flex-1">
                                    @csrf
                                    @method("PATCH")
                                    <input type="number" name="item_amount" placeholder="Amount" value="{{ $item->item_amount }}" min="0" step="1"
                                        class="min-w-0 flex-1 w-16 bg-butter/60 rounded-xl px-3 py-2 text-md text-secondary text-center outline-none focus:outline-none placeholder:text-secondary/70">
                                    <button type="submit" class="px-4 py-2 text-sm font-bold rounded-xl bg-secondary text-butter cursor-pointer whitespace-nowrap shrink-0">Edit</button>
                                </form>
                                <div class="flex items-center shrink-0">
                                    <button type="button"
                                        data-adjust-open
                                        data-direction="subtract"
                                        data-action="{{ $isAuto ? route('auto.item.edit', ['item' => $item->id]) : route('custom.item.edit', ['item' => $item->id]) }}"
                                        data-item-name="{{ $item->item_name }}"
                                        data-current="{{ $item->item_amount }}"
                                        data-currency="{{ $budgetCurrency }}"
                                        class="flex items-center justify-center size-10 rounded-l-xl bg-secondary text-butter text-xl font-bold cursor-pointer leading-none"
                                        aria-label="Subtract from amount">−</button>
                                    <button type="button"
                                        data-adjust-open
                                        data-direction="add"
                                        data-action="{{ $isAuto ? route('auto.item.edit', ['item' => $item->id]) : route('custom.item.edit', ['item' => $item->id]) }}"
                                        data-item-name="{{ $item->item_name }}"
                                        data-current="{{ $item->item_amount }}"
                                        data-currency="{{ $budgetCurrency }}"
                                        class="flex items-center justify-center size-10 rounded-r-xl bg-secondary text-butter text-xl font-bold cursor-pointer leading-none border-l border-butter/40"
                                        aria-label="Add to amount">+</button>
                                </div>
                                <button type="button"
                                    data-delete-open
                                    data-delete-action="{{ $isAuto ? route('auto.item.delete', ['item' => $item->id]) : route('custom.item.delete', ['item' => $item->id]) }}"
                                    data-item-name="{{ $item->item_name }}"
                                    class="flex items-center justify-center size-10 rounded-xl text-secondary cursor-pointer hover:bg-secondary/10 transition shrink-0"
                                    aria-label="Delete item">
                                    <i class="fa-regular fa-trash-can text-lg pointer-events-none"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
    </div>
</div>
