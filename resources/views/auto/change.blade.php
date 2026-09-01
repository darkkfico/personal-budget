@extends('layout.master')

@section('header')
    <x-header1></x-header1>
@endsection

@section('content')
    <main class="h-full py-10 md:py-20 px-4 md:px-6">
        <div class="w-full max-w-250 mx-auto space-y-8 md:space-y-10">

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h1 class="text-secondary text-3xl md:text-6xl font-extrabold leading-tight">Type the amount of your budget<span class="text-accent">.</span>
                </h1>
                <a href="{{ route('auto.index') }}" class="text-butter bg-secondary px-5 py-2 font-bold rounded-2xl inline-block hover:text-secondary hover:bg-butter transition duration-700 shrink-0">Back to Budget</a>
            </div>

            <x-budget-type-toggle current="auto" />

            @php
                $customFormSubmitted = collect(old())->keys()->contains(
                    fn ($key) => str_starts_with((string) $key, 'custom-field')
                );
            @endphp

            <form method="POST" action="{{ route('auto.edit', ['budget' => $id]) }}" id="autoChangeForm"
                class="w-full flex flex-col items-center space-y-10 bg-[#a8c5a0] py-8 md:py-10 px-5 md:px-8 text-butter text-xl font-bold rounded-2xl shadow-secondar shadow-2xl">
                @csrf
                @method('PATCH')
                @if ($errors->any() && ! $customFormSubmitted)
                    <ul class="w-full space-y-1 text-sm font-semibold text-red-600">
                        @foreach ($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif
                <div class="relative w-full">
                    <input type="number" placeholder="Your budget" name="budget"
                        value="{{ old('budget', $budget->budget_amount) }}"
                        class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary focus:p-4 outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary/70 placeholder:font-semibold">
                </div>
                <div class="relative w-full">
                    <input type="number" placeholder="Reset date" name="reset_date"
                        value="{{ old('reset_date', $budget->reset_date) }}"
                        class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary focus:p-4 outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary/70 placeholder:font-semibold">
                </div>
                <div class="relative w-full">
                    <select name="currency"
                        class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500">
                        <option value="select">Select currency</option>
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency['code'] }}" @selected(old('currency', $budget->currency) === $currency['code'])>{{ $currency['name'] }} ({{ $currency['code'] }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-center items-center">
                    <div class="group inline-block space-x-3">
                        <button
                            class="text-butter bg-linear-to-r inline-block cursor-pointer from-butter to-secondary font-bold text-2xl group-hover:text-secondary px-7 py-4 rounded-xl group-hover:translate-x-2 transition duration-300">Submit</button>
                        <i
                            class="fa-solid fa-angles-right inline-block text-secondary -translate-x-7 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-200"></i>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ route('custom.convert') }}" id="customChangeForm"
                class="hidden w-full flex flex-col items-center space-y-10 bg-[#a8c5a0] py-8 md:py-10 px-5 md:px-8 text-butter text-xl font-bold rounded-2xl shadow-secondar shadow-2xl">
                @csrf
                @method('PATCH')
                @if ($errors->any() && $customFormSubmitted)
                    <ul class="w-full space-y-1 text-sm font-semibold text-red-600">
                        @foreach ($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif
                <div class="w-full relative">
                    <input type="number" placeholder="Your budget" name="budget"
                        value="{{ old('budget', $budget->budget_amount) }}"
                        class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary/70 placeholder:font-semibold">
                </div>

                <div id="sections" class="w-full space-y-10">
                    @php $i = 1; @endphp
                    @foreach ($fields as $field)
                        <div class="budget-section w-full space-y-6" data-section>
                            <div class="w-full relative">
                                <input type="text" name="custom-field{{ $i }}" data-role="name"
                                    placeholder="Name of section {{ $i }}"
                                    value="{{ $field->field_name }}"
                                    class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary font-semiboldfocus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary placeholder:font-semibold">
                            </div>
                            <div class="relative inline-block w-full">
                                <input type="number" name="custom-field{{ $i }}-amount" data-role="amount" min="0" max="100"
                                    placeholder="Percentage of section {{ $i }}"
                                    value="{{ $budgetAmount ? round(($field->field_amount / $budgetAmount) * 100) : '' }}"
                                    class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary/60 placeholder:font-semiboldpercentInput">
                                <span class="pointer-events-none absolute right-10 top-1/2 -translate-y-1/2 text-secondary/60">%</span>
                            </div>
                            <button type="button"
                                class="remove-section {{ $fields->count() <= 1 ? 'hidden' : 'inline-flex' }} items-center gap-2 rounded-xl border-2 border-accent bg-accent/15 px-4 py-2 text-base font-bold text-accent cursor-pointer hover:bg-accent hover:text-butter transition">
                                <i class="fa-solid fa-trash-can text-sm pointer-events-none"></i>
                                <span class="pointer-events-none">Remove section</span>
                            </button>
                        </div>
                        @php $i++; @endphp
                    @endforeach
                </div>
                <button type="button" id="addSection" class="p-4 text-secondary text-2xl cursor-pointer">+ Add section</button>

                <div class="w-full relative">
                    <input type="number" name="reset_date" placeholder="Reset Date"
                        value="{{ old('reset_date', $budget->reset_date) }}"
                        class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500placeholder:text-secondary placeholder:font-semibold">
                </div>
                <div class="w-full relative">
                    <select name="currency"
                        class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500">
                        <option value="select">Select currency</option>
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency['code'] }}" @selected(old('currency', $budget->currency) === $currency['code'])>{{ $currency['name'] }} ({{ $currency['code'] }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-center items-center">
                    <div class="group inline-block space-x-3">
                        <button
                            class="text-butter bg-linear-to-r inline-block cursor-pointer from-butter to-secondary font-bold text-2xl group-hover:text-secondary px-7 py-4 rounded-xl group-hover:translate-x-2 transition duration-300">Submit</button>
                        <i
                            class="fa-solid fa-angles-right inline-block text-secondary -translate-x-7 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-200"></i>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <x-budget-change-popups current="auto" />

    <script src="{{ asset('js/budget-type-toggle.js') }}"></script>
    <script src="{{ asset('js/custom-budget.js') }}"></script>
@endsection
