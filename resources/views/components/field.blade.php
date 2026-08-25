<div id='card'
    class='bg-butter/40 rounded-2xl animate-myanimation px-8 py-10 space-y-4 hover:-translate-y-1 hover:scale-[102%] transition-all w-full max-w-[48%] mx-auto md:mx-0'
    style=''>
    <div class="flex justify-between items-center">
        <h1 class='text-secondary text-3xl font-extrabEold'>{{ $field }} </h1>
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
                <div class="flex flex-col items-end">
                    <span class="text-lightbutter text-md font-semibold">BUDGET: {{ $fieldAmount }} {{ $budgetCurrency}}</span>
                    <span class='text-butter text-xl font-extrabold inline-block'>Left: {{ intval($fieldAmount) - $sum }}{{ $budgetCurrency }}</span>
                </div>
        </div>
    </div>


    <form action="{{ session('type') == 'auto' ? route('auto.item.create') : route('custom.item.create')  }}" method="POST"
        class="w-full flex flex-col items-center space-y-5 p-3 bg-butter/40 rounded-2xl relative">
        <input type="hidden" value="{{ $field }}" name="field">
        <div class="flex justify-center items-center gap-4 w-full">
            <div clas="flex flex-col items-center">
                <span class="text-lightbutter uppercase text-sm font-semibold">item name</span>
                <input type="text" name="item" placeholder="eg. Food, Drink..."
                    class='w-full bg-butter/60 border-2 border-lightbutter px-3 py-3 text-md text-secondary outline-none focus:outline-none focus:border-secondary focus:transtition focus:duration-500 placeholder:text-secondary/70 rounded-2xl'>
            </div>
            <div class="flex flex-col items-start space-y-1">
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

    <div class="w-full h-full space-y-6">


        <div class='flex flex-col items-start py-4 space-y-3 '>
            @if($items)
                @foreach($items as $item)
                    @if($item->field_name == $field)
                        <div class='flex justify-between items-center space-x-3 w-full bg-butter/40 p-3 rounded-2xl'>
                            <p class='text-secondary text-lg font-bold'>{{ $item->item_name }} <span class="text-xs">Created: {{ $item->created_at->format("d-m-Y") }}</span></p>
                            <div class="flex justify-center items-center gap-3 w-[45%]">
                                <form method='POST' action="{{ session('type') == 'auto' ? route('auto.item.edit', ["item" => $item->id]) : route('custom.item.edit', ['item' => $item->id]) }}"
                                    class='flex justify-between items-center'>
                                    @csrf
                                    @method("PATCH")
                                    <div class="bg-butter/60 flex justify-between items-center px-2 py-1 rounded-xl">
                                        <input type='text' name='item_amount' placeholder='Amount' value="{{ $item->item_amount }}"
                                            class='text-md text-secondary w-full mx-4 focus:outline-none placeholder:text-secondary/70'>
                                        <button type="submit" class='px-3 py-1 text-md rounded-xl bg-secondary text-butter cursor-pointer'>Edit</button>
                                    </div>
                                </form>
                                <form action="{{ session('type') == 'auto' ? route('auto.item.delete', ["item" => $item->id]) : route('custom.item.delete', ["item" => $item->id])  }}" method="POST" class="">
                                    @csrf
                                    @method("DELETE")
                                    <button class=' text-3xl px-3 py-2 rounded-xl text-secondary cursor-pointer hover:transtion hover:duration-300 hover:rotate-10'><i class="fa-regular fa-trash-can"></i></button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endforeach 
            @endif
        </div>
    </div>
</div>
