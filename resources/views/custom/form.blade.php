@extends('layout.master')

@section('header')
    <x-header1></x-header1>
@endsection

@section('content')
    <main class="h-full py-10 md:py-20 px-4 md:px-6">
        <div class="w-full max-w-250 mx-auto space-y-8 md:space-y-10">
            <h1 class="text-secondary text-3xl md:text-6xl font-extrabold leading-tight">Type the amount of your budget<span class="text-accent">.</span>
            </h1>
            <form method="POST" action="{{ route("custom.store") }}"
                class="w-full  flex flex-col items-center space-y-10 bg-[#a8c5a0] py-8 md:py-10 px-5 md:px-8 text-butter text-xl font-bold rounded-2xl shadow-secondar shadow-2xl">
                @csrf
                <div class="w-full relative">
                    <input type="number" placeholder="Amount of budget" name="budget" value="{{ old("budget") }}"
                        class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary/70 placeholder:font-semibold">
                    @error("budget")
                        <span class="px-2 py-0.5 text-sm text-red-600 absolute top-13 left-1">{{ $message }}</span>
                    @enderror
                    @error('exists')
                    <span class="px-2 py-0.5 text-sm text-red-600 absolute top-13 left-1">{{ $message }}</span>
                    @enderror
                </div>
                <div id="sections" class='w-full space-y-10'>
                    <div class="w-full relative">
                        <span class="text-red-500 absolute top-0 left-0">*</span>
                        <input type='text' name='custom-field1' value="{{ old("custom-field1") }}"
                            placeholder='Name of section 1, eg Groceries...'
                            class='w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary font-semiboldfocus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary/70 placeholder:font-semibold'>
                        @error("custom-field1")
                            <span class="px-2 py-0.5 text-sm text-red-600 absolute top-13 left-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class='relative inline-block w-full'>
                        <input type='number' name='custom-field1-amount' min='0' max='100'
                            placeholder='Percentage of section 1' value="{{ old("custom-field1-amount") }}"
                            class='w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary/60 placeholder:font-semiboldpercentInput'>
                        <span class='pointer-events-none absolute right-10 top-1/2 -translate-y-1/2 text-secondary/60'>
                            %
                        </span>
                        @error("custom-field1-amount")
                            <span class="px-2 py-0.5 text-sm text-red-600 absolute top-13 left-1">{{ $message }}</span>
                        @enderror
                        @error("sum")
                            <span class="px-2 py-0.5 text-sm text-red-600 absolute top-13 left-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <button type="button" id="addSection" class="p-4 text-secondary text-2xl cursor-pointer">+ Add section</button>
                <x-reset-date-field :value="old('reset_date')"
                    class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500placeholder:text-secondary placeholder:font-semibold">
                    @error("reset_date")
                        <span class="px-2 py-0.5 text-sm text-red-600 absolute top-13 left-1">{{ $message }}</span>
                    @enderror
                </x-reset-date-field>
                <div class="w-full relative">
                    <select name='currency'
                        class='w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500'>
                        <option value='select'>Select currency</option>
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency['code'] }}">{{ $currency['name'] }} ({{ $currency['code'] }})</option>
                        @endforeach
                    </select>
                    @error("currency")
                        <span class="px-2 py-0.5 text-sm text-red-600 absolute top-13 left-1">{{ $message }}</span>
                    @enderror
                    
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

    <script src="{{ asset('js/custom-budget.js') }}"></script>
    <script src="{{ asset('js/budget-type-info.js') }}"></script>
@endsection
