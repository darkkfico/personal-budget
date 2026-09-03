@extends('layout.master')

@section('header')
    <x-header1></x-header1>
@endsection


@section('content')
    <main class='md:h-screen h-full py-10 md:py-20'>
        <div class="w-full max-w-5xl mx-auto py-6 md:py-10 px-4 md:px-6 space-y-8 md:space-y-10">
            <h1 class="text-secondary text-3xl md:text-6xl font-extrabold leading-tight">Select Your Type of budget<span class="text-accent">.</span></h1>
            <form method="POST" action="{{ route('start.choose') }}"
                class="w-full flex flex-col items-center space-y-10 bg-[#a8c5a0] py-10 md:py-14 px-5 md:px-12 text-secondary text-lg md:text-xl font-bold rounded-2xl shadow-secondary shadow-2xl">
                @csrf
                <div class="flex-col items-start w-full space-y-7 ">
                    <div class="flex justify-start items-center space-x-5">
                        <div class="" id="autoEl">
                            <input type="radio" id="auto" name="type" value="auto" class="">
                            <label for='auto' id="autoLabel">Auto</label>
                        </div>
                        <div class="relative group">
                            <i class="fa-solid fa-circle-info cursor-pointer" data-info-icon id="autoInfoIcon" role="button" tabindex="0" aria-expanded="false" aria-controls="autoInfo"></i>
                            <div class="hidden absolute bg-butter text-secondary text-sm font-light left-0 md:left-12.5 top-8 md:-top-5 w-[min(18rem,calc(100vw-3rem))] px-6 py-4 rounded-xl max-md:group-hover:inline-block animate-myanimation z-20"
                                id="autoInfo">
                                <p><b>Let us make your monthly plan</b>. We will devide you budget in 3 sections. The first
                                    section
                                    will be <b>50%</b> of your budget that will be named <b>"Groceries"</b>, the second
                                    section will
                                    be <b>30%</b> of your budget that will be named <b>"Wishes"</b> and the third section
                                    will be
                                    <b>20%</b> of your budget and will be named <b>"Savings"</b>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-start items-center space-x-5">
                        <div class="animate-myanimation1">
                            <input type="radio" id="custom" name="type" value="custom" class="radio">
                            <label for="custom">Custom</label>
                        </div>
                        <div class="relative group">
                            <i class="fa-solid fa-circle-info cursor-pointer" data-info-icon id="customInfoIcon" role="button" tabindex="0" aria-expanded="false" aria-controls="customInfo"></i>
                            <div class="hidden absolute bg-butter text-secondary text-sm font-light left-0 md:left-12.5 top-8 md:-top-5 w-[min(18rem,calc(100vw-3rem))] px-6 py-4 rounded-xl max-md:group-hover:inline-block animate-myanimation z-20"
                                id="customInfo">
                                <p>We will let you make your own monthly plan <b>(devide your budget as you want)</b>, but
                                    be
                                    careful how you devide it since we use the way the finance managers tell you.</p>
                            </div>
                        </div>
                    </div>
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

    <script src="{{ asset('js/budget-type-info.js') }}"></script>
@endsection
