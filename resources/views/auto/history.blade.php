@extends('layout.master')

@section('header')
    <x-header1></x-header1>
@endsection

@section('content')
    <main class="h-full py-10 md:py-20 px-4 md:px-6">
        <div class="w-full max-w-250 mx-auto space-y-8 md:space-y-10">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h1 class="text-secondary text-3xl md:text-6xl font-extrabold leading-tight">Budget history<span class="text-accent">.</span></h1>
                <a href="{{ route("auto.index", ["budget" => $budget->id]) }}" class="text-butter bg-secondary px-5 py-2 font-bold rounded-2xl inline-block hover:text-secondary hover:bg-butter transition duration-700 shrink-0">Back to Budget</a>
            </div>

            <x-history-pie-chart :chartMonths="$chartMonths" />
            <x-history-month-cards :months="$months" :itemsUrl="route('auto.history.items', $budget)" />
        </div>
    </main>

    <script src="{{ asset('js/history.js') }}" defer></script>
@endsection
