@extends('layout.master')

@section('header')
    <x-header1></x-header1>
@endsection

@section('content')

<main class="md:h-screen h-full py-10 md:py-20 px-4 md:px-6">
    <div id="card"
        class="w-full max-w-250 mx-auto inset-0 bg-butter/40 rounded-2xl animate-myanimation px-5 md:px-16 py-10 md:py-20 space-y-8 md:space-y-12 shadow-butter shadow-2xl hover:-translate-y-1 hover:shadow-3xl transition-all">
        <h1 class="text-secondary text-3xl md:text-6xl font-extrabold leading-tight">Start your journey</h1>
        <p class="text-butter font-bold bg-secondary rounded-full px-5 py-2 inline-block">Make a resolution</p>
        <ul class="bg-secondary rounded-2xl p-6 text-butter font-bold text-lg space-y-6">
            <li class="hover:scale-[102%] hover:translate-x-3 transition duration-300"><i
                    class="fa-solid fa-circle-chevron-right"></i> Start saving</li>
            <li class="hover:scale-[102%] hover:translate-x-3 transition duration-300"><i
                    class="fa-solid fa-circle-chevron-right"></i> Star making changes</li>
            <li class="hover:scale-[102%] hover:translate-x-3 transition duration-300"><i
                    class="fa-solid fa-circle-chevron-right"></i> Make your life easier</li>
            <li class="hover:scale-[102%] hover:translate-x-3 transition duration-300"><i
                    class="fa-solid fa-circle-chevron-right"></i> Keep eye on your budget</li>
        </ul>
        <!-- <p class="text-secondary text-lg">Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusantium corporis, odio dolores eum explicabo ipsa amet animi, consectetur rem est unde consequatur vero magnam qui sunt nemo architecto, omnis repellat!</p> -->
        <div class="flex justify-center items-center">
            <div class="group inline-block space-x-3">
                <a href="{{ route("start.start") }}"
                    class="text-butter bg-linear-to-r inline-block cursor-pointer from-butter to-secondary font-bold text-2xl group-hover:text-secondary px-7 py-4 rounded-xl group-hover:translate-x-2 transition duration-300">Start</a>
                <i
                    class="fa-solid fa-angles-right inline-block text-secondary -translate-x-7 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-200"></i>
            </div>
        </div>
</main>
@endsection
