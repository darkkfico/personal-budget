<header>
    <section id="nav" class="py-5 md:py-8 px-4 md:px-20 shadow-secondary shadow-xl flex justify-between items-center gap-3">
        @auth
        <h1 class="text-lg md:text-2xl font-bold text-butter min-w-0 truncate">
            Welcome <span class="text-accent font-normal">{{ Auth::user()->name }}</span>
        </h1>
        @endauth
        @auth
        <div class="flex justify-end items-center gap-2 shrink-0">
            <button type="button" id="deleteAccountOpen"
                class="relative text-secondary bg-butter px-3 md:px-5 py-2 text-sm md:text-base font-bold rounded-2xl inline-block hover:bg-accent hover:text-butter transition duration-700 cursor-pointer">Delete Account</button>
            <a href="{{ route('auth.logout') }}"
                class="relative text-butter bg-secondary px-3 md:px-5 py-2 text-sm md:text-base font-bold rounded-2xl inline-block hover:text-secondary hover:bg-butter transition duration-700">Log Out</a>
        </div>
        @endauth
    </section>
</header>

@auth
    <div id="deleteAccountPopup"
        class="{{ $errors->has('delete_password') || session('show_delete_account') ? '' : 'hidden ' }}fixed inset-0 z-50 flex items-center justify-center p-4"
        data-step="{{ $errors->has('delete_password') || session('show_delete_account') ? 'password' : 'confirm' }}">
        <div id="deleteAccountBackdrop" class="absolute inset-0 bg-secondary/50"></div>
        <div class="relative w-full max-w-md bg-butter rounded-2xl shadow-2xl p-6 space-y-4" role="dialog" aria-modal="true" aria-labelledby="deleteAccountTitle">
            <div id="deleteAccountConfirm" class="{{ $errors->has('delete_password') || session('show_delete_account') ? 'hidden' : '' }} space-y-4">
                <h2 id="deleteAccountTitle" class="text-secondary text-xl font-extrabold">Delete your account?</h2>
                <p class="text-secondary text-sm font-semibold">
                    This will permanently remove your account, budget, and history. This cannot be undone.
                </p>
                <div class="flex gap-2">
                    <button type="button" id="deleteAccountNo" class="flex-1 px-4 py-2 rounded-xl border-2 border-secondary text-secondary font-bold cursor-pointer">No</button>
                    <button type="button" id="deleteAccountYes" class="flex-1 px-4 py-2 rounded-xl bg-secondary text-butter font-bold cursor-pointer">Yes</button>
                </div>
            </div>
            <div id="deleteAccountPassword" class="{{ $errors->has('delete_password') || session('show_delete_account') ? '' : 'hidden' }} space-y-4">
                <h2 class="text-secondary text-xl font-extrabold">Confirm with your password</h2>
                <p class="text-secondary text-sm font-semibold">
                    Type your password to delete your account.
                </p>
                <form method="POST" action="{{ route('auth.destroy') }}" class="space-y-3">
                    @csrf
                    @method('DELETE')
                    <input type="password" name="password" required autocomplete="current-password"
                        class="w-full bg-butter/60 border-2 border-lightbutter px-3 py-3 text-md text-secondary outline-none focus:border-secondary rounded-2xl"
                        placeholder="Password">
                    @error('password')
                        <p class="text-sm text-red-600 font-semibold">{{ $message }}</p>
                    @enderror
                    @error('delete_password')
                        <p class="text-sm text-red-600 font-semibold">{{ $message }}</p>
                    @enderror
                    <div class="flex gap-2">
                        <button type="button" id="deleteAccountPasswordCancel" class="flex-1 px-4 py-2 rounded-xl border-2 border-secondary text-secondary font-bold cursor-pointer">Cancel</button>
                        <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-secondary text-butter font-bold cursor-pointer">Delete account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/delete-account.js') }}"></script>
@endauth
