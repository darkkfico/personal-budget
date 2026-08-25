@extends('layout.master')

@section('title', 'Login')

@section('content')
    <main class="min-h-screen flex items-center justify-center bg-linear-to-br from-secondary to-butter px-6 py-16">
        <div
            class="flex flex-col items-center w-full max-w-xl bg-butter/60 hover:bg-butter shadow-2xl shadow-butter rounded-2xl animate-myanimation px-8 md:px-16 py-14 space-y-14 hover:-translate-y-1 hover:shadow-3xl transition-all">
            <h2 class="text-secondary font-bold text-4xl">Log in</h2>

            <form action="{{ route("login") }}" method="POST" class="w-full flex flex-col items-center space-y-6">
                @csrf
                <div class="relative w-full">
                    <input type="text" name="email" placeholder="example@gmail.com" value="{{ old('email', session('email')) }}"
                        class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary">
                    @error("email")
                    <span class="px-2 py-0.5 text-md text-red-600 absolute top-12 left-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="relative w-full">
                    <input type="password" name="password" id="password" placeholder="Password"
                        class="w-full bg-transparent border-b-2 border-b-secondary px-3 py-3 text-md focus:scale-[102%] outline-none focus:outline-none focus:transtition focus:duration-500 placeholder:text-secondary">
                    <i class="fa-solid fa-eye-slash absolute top-5 right-2 text-md text-secondary cursor-pointer"
                        id="passwordSee"></i>
                </div>

                <div class="w-full flex flex-col items-center space-y-2">
                    <div>
                        <input type="checkbox" name="remember" id="remember" class="">
                        <label for="remember" class="text-secondary text-md">Remember me</label>
                    </div>
                    <button
                        class="text-butter text-lg bg-secondary border-secondary border-2 px-5 py-2 font-bold rounded-2xl inline-block hover:border-2 hover:border-secondary hover:bg-butter hover:text-secondary transition duration-700">
                        Log in
                    </button>
                </div>

            </form>

            <div class="w-full flex justify-between items-center">
                <p class="text-secondary text-lg font-semibold">Don't have an account?</p>
                <a href="{{ route('auth.register') }}"
                    class="text-butter text-md bg-secondary border-secondary border-2 px-4 py-2 font-bold rounded-2xl inline-block hover:border-2 hover:border-secondary hover:bg-butter hover:text-secondary transition duration-700">
                    Register
                </a>
            </div>
        </div>
    </main>

    <script src="{{ asset("js/login.js") }}"></script>
@endsection