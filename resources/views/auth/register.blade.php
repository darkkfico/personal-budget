@extends('layout.master')

@section('title', 'Register')

@section('content')
    <main class="min-h-screen flex items-center justify-center bg-linear-to-br from-secondary to-butter px-6 py-16">
        <div
            class="flex flex-col items-center w-full max-w-xl bg-butter/60 hover:bg-butter shadow-2xl shadow-butter rounded-2xl animate-myanimation px-8 md:px-16 py-14 space-y-14 hover:-translate-y-1 hover:shadow-3xl transition-all">
            <h2 class="text-secondary font-bold text-4xl">Register</h2>


            <form action="{{ route('auth.store') }}" method="POST" class="w-full flex flex-col items-center space-y-6">
                @csrf

                <div class="w-full relative">
                    <input type="text" name="name" placeholder="Name" value="{{ old('name', session('name')) }}"
                        class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary">
                    @error('name')
                        <span class="px-2 py-0.5 text-md text-red-600 absolute top-12 left-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="w-full relative">
                    <input type="text" name="lastname" placeholder="Last Name"
                        value="{{ old('lastname', session('lastname')) }}"
                        class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary">
                    @error('lastname')
                        <span class="px-2 py-0.5 text-md text-red-600 absolute top-12 left-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="w-full relative">
                    <input type="text" name="email" placeholder="example@gmail.com"
                        value="{{ old('email', session('email')) }}"
                        class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary">
                    @error('email')
                        <span class="px-2 py-0.5 text-md text-red-600 absolute top-12 left-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="w-full relative">

                    <input type="password" name="password" id="password" placeholder="Password"
                        class="w-full bg-transparent border-b-2 border-secondary px-3 py-3 text-md focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary">
                    <i class="fa-solid fa-eye-slash absolute top-5 right-2 text-md text-secondary cursor-pointer"
                        id="passwordSee"></i>

                    @error('password')
                        <span class="px-2 py-0.5 text-md text-red-600 absolute top-12 left-1">{{ $message }}</span>
                    @enderror

                    <input type="password" name="password_confirmation" id="passwordC" placeholder="Confirm Password"
                        class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md focus:scale-[102%] mt-6 outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:secondary">
                    <i class="fa-solid fa-eye-slash absolute top-23 right-2 text-md text-secondary cursor-pointer"
                        id="passwordCSee"></i>

                    @error('password_confirmation')
                        <span class="px-2 py-0.5 text-md text-red-600 absolute top-full left-1">{{ $message }}</span>
                    @enderror

                </div>

                <button
                    class="text-butter text-lg bg-secondary border-secondary border-2 px-5 py-2 mt-10 font-bold rounded-2xl inline-block hover:border-2 hover:border-secondary hover:bg-butter hover:text-secondary transition duration-700">
                    Register
                </button>
            </form>

            <div class="w-full flex justify-between items-center">
                <p class="text-secondary text-lg font-semibold">Already have an account?</p>
                <a href="{{ route('auth.login') }}"
                    class=" text-butter text-md bg-secondary border-secondary border-2 px-4 py-2 font-bold rounded-2xl inline-block hover:border-2 hover:border-secondary hover:bg-butter hover:text-secondary transition duration-700">Log
                    In</a>
            </div>
        </div>
    </main>

    <script src="{{ asset("js/register.js") }}"></script>
@endsection
