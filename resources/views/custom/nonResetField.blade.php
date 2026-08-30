@extends('layout.master')

@section('header')
    <x-header1></x-header1>
@endsection

@section('content')
    <main class="h-full py-10 md:py-20 px-4 md:px-6">
        <div class="w-full max-w-250 mx-auto space-y-8 md:space-y-10">
            <h1 class="text-secondary text-3xl md:text-6xl font-extrabold leading-tight">Choose which field you DON'T want to have resetable items<span class="text-accent">.</span>
            </h1>
            <form method="POST" action="{{ route("custom.nonResetable") }}"
                class="w-full  flex flex-col items-center space-y-10 bg-[#a8c5a0] py-8 md:py-10 px-5 md:px-8 text-butter text-xl font-bold rounded-2xl shadow-secondar shadow-2xl">
                @csrf
                <div class="w-full relative">
                    <select name='field'
                        class='w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md text-secondary focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500'>
                        <option value='none'>None</option>
                        @foreach ($fields as $field)
                            <option value="{{ $field->id }}">{{ $field->field_name }}</option>
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
@endsection
