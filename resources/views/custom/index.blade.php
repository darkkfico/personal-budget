@extends('layout.master')

@section('header')
    <x-header1></x-header1>
@endsection

@section('content')
    <main class='py-6 md:py-10 px-4 md:px-6'>
        <div class="w-full max-w-350 mx-auto py-6 md:py-10 flex flex-col justify-center space-y-8 md:space-y-12">
            <div class='flex flex-col md:flex-row justify-between items-start md:items-center gap-6 w-full'>
                
                <div class="flex flex-col items-start space-y-3 min-w-0">
                    <h1 class='text-butter text-2xl md:text-4xl font-extrabold break-words'>Budget: {{ money($budget->budget_amount, $budget->currency) }}</h1>
                    <span class='text-lightbutter text-2xl md:text-4xl font-extrabold break-words'>Money left: {{ money($budget->budget_amount - ($items->sum('item_amount') ?? 0), $budget->currency) }}</span>
                    <span class='text-secondary text-2xl md:text-4xl font-extrabold break-words'>Left for today: {{ money($sub1, $budget->currency) }}</span>
                </div>
                
                <div class="flex flex-col items-stretch md:items-start space-y-3 w-full md:w-auto">
                    <a href="{{ route("custom.change") }}" class="text-butter bg-secondary px-5 py-2 font-bold rounded-2xl inline-block text-center hover:text-secondary hover:bg-butter transition duration-700">Change my Budget</a>
                    <a href="{{ route("custom.history", ["budget" => $budget->id]) }}" class="text-butter bg-secondary px-5 py-2 font-bold rounded-2xl inline-block text-center hover:text-secondary hover:bg-butter transition duration-700">Budget History</a>
                </div>
            </div>
            <div class='h-full w-full flex flex-col md:flex-row md:flex-wrap items-start gap-6 md:gap-10 px-0 md:px-1'>

                @foreach($fields as $field)
                    <x-field field="{{ $field->field_name }}" budgetAmount="{{ $budget->budget_amount }}"
                        fieldAmount="{{ $field->field_amount }}" budgetCurrency="{{ $budget->currency }}"
                        budgetType="custom"
                        :items="$items ?? collect()"></x-field>
                @endforeach

            </div>
        </div>
    </main>
    <x-item-adjust-popup></x-item-adjust-popup>
    <x-item-delete-popup></x-item-delete-popup>
    <x-daily-allowence-popup :show="$warnDaily && ! $resetCarryover" :amount="$sub1" :currency="$budget->currency" />
    <x-reset-carryover-popup :prompt="$resetCarryover" :action="route('custom.resetCarryover')" />
    <script src="{{ asset('js/item-adjust.js') }}"></script>
    <script src="{{ asset('js/item-delete.js') }}"></script>
@endsection
