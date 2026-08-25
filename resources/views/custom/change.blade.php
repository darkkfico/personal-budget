@extends('layout.master')

@section('header')
    <x-header1></x-header1>
@endsection

@section('content')
    <main class="h-full py-20 px-6">
        <div class="w-full max-w-250 mx-auto space-y-10">
            <h1 class="text-secondary text-6xl font-extrabold">Type the amount of your budget<span
                    class="text-accent">.</span>
            </h1>

            <x-budget-type-toggle current="custom" />

            @include('partials.auto-change-form', [
                'currentType' => 'custom',
                'autoFormAction' => route('auto.convert'),
                'currencies' => $currencies,
            ])

            @include('partials.custom-change-form', [
                'currentType' => 'custom',
                'currencies' => $currencies,
                'fields' => $fields,
                'id' => $id,
            ])
        </div>
    </main>

    <script src="{{ asset('js/budget-type-toggle.js') }}"></script>
@endsection
