<header class="first-letter:">

    <section id="nav" class="py-8 md:px-20 px-6 shadow-secondary shadow-xl flex justify-between items-center">
        @auth
        <h1 class="text-2xl font-bold text-butter">Welcome <span class="text-accent font-normal">{{ Auth::user()->name}}</span></h1>
        @endauth
        <div class="flex justify-between items-center space-x-5">
            <a href="{{ route("auth.logout") }}" class="relative text-butter bg-secondary px-5 py-2 font-bold rounded-2xl inline-block hover:text-secondary hover:bg-butter transition duration-700">Log Out</a>
        </div>
    </section>
</header>