@extends('layout.master')

@section('header')
    <x-header1></x-header1>
@endsection

@section('content')
    <main class="md:h-screen h-full py-20 px-6">
        <div class="w-full max-w-250 mx-auto space-y-10">
            <h1 class="text-secondary text-6xl font-extrabold">Type the amount of your budget<span
                    class="text-accent">.</span>
            </h1>

            <x-budget-type-toggle current="auto" />

            @include('partials.auto-change-form', [
                'currentType' => 'auto',
                'autoFormAction' => route('auto.edit', ['budget' => $id]),
                'currencies' => $currencies,
            ])

            @include('partials.custom-change-form', [
                'currentType' => 'auto',
                'currencies' => $currencies,
                'fields' => collect(),
                'id' => null,
                'budgetAmount' => null,
            ])
        </div>
    </main>

    <script src="{{ asset('js/budget-type-toggle.js') }}"></script>
    <script src="{{ asset('js/custom-budget.js') }}"></script>
@endsection
