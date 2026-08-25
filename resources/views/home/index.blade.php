@extends('layout.master')


@section('header')
    <header class="md:h-screen h-full md:py-0 relative">
        <nav class="w-full flex justify-between items-center mx-auto py-8 px-10 shadow-b-secondary shadow-xl">
            <div id="logo">
                <h1 class="text-butter font-extrabold text-3xl">Budgetly<span class="text-accent">.</span></h1>
            </div>
            <div id="nav-bar" class="ml-[100px] hidden md:block">
                <ul class="flex justify-between items-center space-x-8 text-butter text-xl">
                    <li
                        class="relative hover:scale-105 before:absolute before:h-1 before:w-0 hover:before:w-full before:bg-butter before:-bottom-1 transition before:duration-700">
                        <a href="">Home</a>
                    </li>
                    <li
                        class="relative hover:scale-105 before:absolute before:h-1 before:w-0 hover:before:w-full before:bg-butter before:-bottom-1 transition before:duration-700">
                        <a href="#main-section">Why Budgeting?</a>
                    </li>
                    <li
                        class="relative hover:scale-105 before:absolute before:h-1 before:w-0 hover:before:w-full before:bg-butter before:-bottom-1 transition before:duration-700">
                        <a href="#contact-section">Contact</a>
                    </li>
                </ul>
            </div>
            <div class="space-x-3 flex justify-between items-center">
                {{-- @if (Auth::check())
                    <a href='{{ route("home") }}'
                    class='relative text-butter bg-secondary px-5 py-2 font-bold rounded-2xl inline-block hover:text-secondary  hover:bg-butter transition duration-700'>My
                    budget</a>
                    <a href='{{ route("auth.logout") }}'
                    class='relative text-butter bg-secondary px-5 py-2 font-bold rounded-2xl inline-block hover:text-secondary hover:bg-butter transition duration-700'>Log
                    Out</a>
                @else --}}
                    <a href='{{ route("auth.register") }}'
                        class='relative text-butter bg-secondary px-5 py-2 font-bold rounded-2xl inline-block hover:text-secondary  hover:bg-butter transition duration-700'>Register
                        Now</a>
                    <a href='{{ route("auth.login") }}'
                        class='relative text-secondary bg-butter px-5 py-2 font-bold rounded-2xl inline-block hover:text-butter  hover:bg-secondary transition duration-700'>Log
                        In</a>
                {{-- @endif --}}
                <div class="md:hidden inline-block">
                    <i class="fa-solid fa-bars text-2xl font-bold text-butter"></i>
                    <div id="menu" class="hidden max-w-150 h-screen bg-secondary fixed z-100 top-0 right-0 py-10 px-6 space-y-10 transition duration-500">
                        <i class="fa-solid fa-x text-2xl font-bold text-butter"></i>
                        <ul class="flex flex-col items-start space-y-2 text-butter text-xl">
                            <li
                                class="relative hover:scale-105 before:absolute before:h-1 before:w-0 hover:before:w-full before:bg-butter before:-bottom-1 transition before:duration-700">
                                <a href="">Home</a>
                            </li>
                            <li
                                class="relative hover:scale-105 before:absolute before:h-1 before:w-0 hover:before:w-full before:bg-butter before:-bottom-1 transition before:duration-700">
                                <a href="#main-section">Why Budgeting?</a>
                            </li>
                            <li
                                class="relative hover:scale-105 before:absolute before:h-1 before:w-0 hover:before:w-full before:bg-butter before:-bottom-1 transition before:duration-700">
                                <a href="#contact-section">Contact</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <script>
                let bars = document.querySelector('.fa-bars');
                let x = document.querySelector('.fa-x');
                let menu = document.querySelector('#menu');

                bars.addEventListener("click", () => {
                    menu.classList.remove("hidden");
                    menu.classList.add()
                })

                x.addEventListener("click", () => {
                    menu.classList.add("hidden");
                })
            </script>
        </nav>
        <section
            class="w-full px-6 h-full flex flex-col md:flex-row space-y-10 justify-between items-center max-w-250 mx-auto md:-mt-10 py-10 md:py-0">
            <div id="left" class="md:w-[50%] w-full space-y-14">
                <h2
                    class="top-0 left-2 text-secondary md:inline-block border-[1px] hidden border-butter bg-butter rounded-full px-3 py-1 font-bold">
                    Make a resolution</h2>
                <h1 class="md:text-7xl text-6xl text-butter font-extrabold">Organize Your Monthly Budget And Save Money</h1>
                <p class="md:text-xl text-secondary">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae ullam
                    amet
                    quia officia, repellat totam veniam dolor nemo ut! Vero nesciunt incidunt placeat eos accusantium ex
                    magni voluptatum omnis! Maiores.</p>
                <div class="flex flex-col-reverse items-start space-y-5">
                    <a href="{{ route('auth.register') }}"
                        class="text-xl text-butter bg-secondary px-7 py-3 font-bold rounded-2xl inline-block hover:text-secondary  hover:bg-butter transition duration-700">Register
                        Now</a>
                    <h2
                        class="top-0 left-2 md:hidden my-4 inline-block text-secondary border-[1px] border-butter bg-butter rounded-full px-3 py-1 inline-block font-bold">
                        Make a resolution</h2>
                </div>
            </div>
            <div id="right" class="md:w-1/2 w-full flex justify-end items-center">
                <img src="{{ asset('images/head-img.png') }}" alt="" class="md:w-[90%] w-full">
            </div>
        </section>
    </header>
@endsection

@section('content')
    <main id="main-section">
        <section class="h-full bg-secondary my-auto pb-28 px-6 md:px-0">
            <div class="w-full max-w-250 flex flex-col md:flex-row md:justify-between items-center mx-auto space-y-16">

                <div id="left" class="relative md:w-1/2 w-full flex items-center justify-center">
                    <div id="title" class="space-y-4">
                        <h1 class="text-butter text-4xl font-bold">Can't Organize Your Monthly Budget</h1>
                        <p class="text-butter text-xl">Try this app and see the results</p>
                    </div>
                    <div class="absolute border-2 border-white w-[400px] -mt-[50px] md:-ml-[350px] -ml-[150px] z-20 overflow-hidden">
                        <img src="{{ asset('images/main-img.jpg') }}" alt="" class="w-full h-full object-cover">
                    </div>
                    <img src="{{ asset('images/main2-img.webp') }}" alt=""
                        class="relative border-2 border-white w-[400px] mt-[300px] z-10 ml-[100px] object-cover">
                </div>
                <div id="right" class="md:w-1/2 w-full md:px-20 space-y-14 flex py-10">
                    <div
                        class="relative before:absolute before:bg-butter before:w-1 before:h-[80%] md:before:-left-[62px] before:-left-[37px] space-y-10 ml-15 px-6">

                        @for ($i = 1; $i <= 4; $i++)
                            @if ($i % 2 == 0)
                                <div class='relative bg-secondary rounded-2xl shadow-md shadow-yellow-100 px-5 py-2'>
                                    <div class='absolute -left-[88px] -top-5 '>
                                        <p
                                            class='border-2 border-butter bg-secondary px-5 py-3 rounded-full text-butter text-2xl'>
                                            {{ $i }} </p>
                                    </div>
                                    <h1 class='text-butter text-2xl font-bold'>Title {{ $i }}</h1>
                                    <p class='text-butter text-lg'>Lorem ipsum dolor sit amet consectetur adipisicing
                                        elit. Et, doloremque?</p>
                                </div>
                            @else
                                <div class='relative bg-butter rounded-2xl shadow-md shadow-yellow-100 px-5 py-2'>
                                    <div class='absolute -left-[88px] -top-5 '>
                                        <p
                                            class='border-2 border-secondary bg-butter px-5 py-3 rounded-full text-secondary text-2xl'>
                                            {{ $i }} </p>
                                    </div>
                                    <h1 class='text-secondary text-2xl font-bold'>Title {{ $i }}</h1>
                                    <p class='text-secondary text-lg'>Lorem ipsum dolor sit amet consectetur adipisicing
                                        elit. Et, doloremque?</p>
                                </div>
                            @endif
                        @endfor
                    </div>

                </div>
            </div>
        </section>
    </main>
@endsection

@section('footer')
    <footer class="">
        <section id="contact-section" class="md:py-10 py-20 w-full md:h-[500px] h-full  bg-gradient-to-br from-secondary to-butter px-6">
            <div
                class="flex flex-col md:flex-row justify-center items-center rounded-2xl shadow-yellow-100 shadow-2xl h-full w-full max-w-250 mx-auto">
                <div id="left" class="bg-secondary md:w-1/3 w-full md:rounded-l-2xl rounded-t-2xl px-8 py-5 h-full">
                    <div class="flex justify-center items-center gap-2">
                        <div class="h-[2px] bg-white w-1/30"></div>
                        <h1 class="text-butter md:text-2xl text-lg">Contact</h1>
                        <div class="h-[2px] bg-butter w-1/30"></div>
                    </div>
                    <div class="space-y-6 mt-5">
                        <div class="flex justify-start items-center gap-2">
                            <i class="fa-regular fa-envelope text-butter md:text-2xl text-lg"></i>
                            <p class="text-butter text-xl">example@gmail.com</p>
                        </div>
                        <div class="flex justify-start items-center gap-2">
                            <i class="fa-brands fa-facebook-f text-butter  md:text-2xl text-lg"></i>
                            <p class="text-butter text-xl">Budgetly</p>
                        </div>
                        <div class="flex justify-start items-center gap-2">
                            <i class="fa-brands fa-instagram text-butter md:text-2xl text-lg"></i>
                            <p class="text-butter text-xl">budgetly_monthly_budget</p>
                        </div>
                    </div>
                </div>
                <div id="right" class="md:w-2/3 w-full h-full px-8 py-5">
                    <div class="flex justify-center items-center gap-2 ">
                        <div class="h-[2px] bg-secondary w-1/30"></div>
                        <h1 class="text-secondary text-2xl">Feedback</h1>
                        <div class="h-[2px] bg-secondary w-1/30"></div>
                    </div>
                    <div class="mt-5">
                        <form action="" class="w-full space-y-5">
                            <input type="email" placeholder="example@gmail.com" name="emailF"
                                class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md focus:p-4 outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary">
                            <select name="rating" id=""
                                class="w-full border-b-2 border-secondary px-3 py-3 text-md text-secondary bg-transparent outline-none focus:outline-none">
                                <option value="">Select Rating</option>

                                @for ($i = 1; $i <= 10; $i++)
                                    {
                                    <option value'{{ $i }}'>{{ $i }}</option>
                                @endfor
                            </select>
                            <textarea name="message" id="" rows="6" placeholder="Message"
                                class="w-full bg-transparent border-b-2 outline-none focus:outline-none border-secondary px-3 py-3 text-md focus:p-4 focus:transtition focus:duration-500 placeholder:text-secondary"></textarea>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </footer>
@endsection
