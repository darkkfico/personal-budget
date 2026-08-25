@extends('layout.master')

@section('header')
    <x-header1></x-header1>
@endsection

@section('content')
    <main class='py-10 px-6'>
        <div class="w-full max-w-350 mx-auto py-10 flex flex-col justify-center space-y-12">
            <div class='flex justify-between items-center md:space-x-4 space-y-4 w-full'>
                
                <div class="flex flex-col items-start space-y-3">
                    <h1 class='text-butter text-4xl font-extrabold'>Budget: {{ $budget->budget_amount }} {{ $budget->currency }}</h1>
                    <span class='text-lightbutter text-4xl font-extrabold'>Money left: {{ $budget->budget_amount - ($items->sum('item_amount') ?? 0) }} {{ $budget->currency }}</span>
                    <span class='text-secondary text-4xl font-extrabold'>Left for today: {{ $sub1 }}{{ $budget->currency }}</span>
                </div>
                
                <div class="flex flex-col items-end gap-6">
                    <a href="{{ route("auto.change", ["id" => $budget->id]) }}" class="text-butter bg-secondary px-5 py-2 font-bold rounded-2xl inline-block hover:text-secondary hover:bg-butter transition duration-700">Change my Budget</a>
                    <a href="{{ route("auto.history", ["budget" => $budget->id]) }}" class="text-butter bg-secondary px-5 py-2 font-bold rounded-2xl inline-block hover:text-secondary hover:bg-butter transition duration-700">Budget History</a>
                    
                </div>
            </div>
            <div class='h-full w-full flex flex-wrap gap-10 px-1'>
                @foreach($fields as $field)
                    <x-field field="{{ $field->field_name }}" budgetAmount="{{ $budget->budget_amount }}"
                        fieldAmount="{{ $field->field_amount }}" budgetCurrency="{{ $budget->currency }}"
                        :items="$items ?? collect()"></x-field>
                @endforeach
            </div>
        </div>
    </main>
@endsection
