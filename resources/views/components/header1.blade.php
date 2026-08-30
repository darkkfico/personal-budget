<header>
    <section id="nav" class="py-5 md:py-8 px-4 md:px-20 shadow-secondary shadow-xl flex justify-between items-center gap-3">
        @auth
        <h1 class="text-lg md:text-2xl font-bold text-butter min-w-0 truncate">
            Welcome <span class="text-accent font-normal">{{ Auth::user()->name }}</span>
        </h1>
        @endauth
        <div class="flex justify-end items-center shrink-0">
            <a href="{{ route('auth.logout') }}"
                class="relative text-butter bg-secondary px-3 md:px-5 py-2 text-sm md:text-base font-bold rounded-2xl inline-block hover:text-secondary hover:bg-butter transition duration-700">Log Out</a>
        </div>
    </section>
</header>
