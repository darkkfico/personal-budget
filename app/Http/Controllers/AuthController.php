<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\OnboardingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function registerForm(OnboardingService $onboarding)
    {
        if (Auth::check()) {
            return $onboarding->redirect(Auth::user());
        }

        return view('auth.register');
    }

    public function register(RegisterRequest $request, OnboardingService $onboarding)
    {

        $user = User::create($request->only("name", "lastname", "email", "password"));

        $onboarding->mark($user, OnboardingService::CHOOSE_TYPE);

        Auth::login($user);
        $request->session()->regenerate();

        return $onboarding->redirect($user);
    }
    public function loginForm(OnboardingService $onboarding)
    {
        if (Auth::check()) {
            return $onboarding->redirect(Auth::user());
        }

        return view("auth.login");
    }

    public function login(LoginRequest $request, OnboardingService $onboarding)
    {
        $remember = $request->remember;

        if (Auth::attempt(["email" => $request->email, 'password' => $request->password], $remember)) {
            $request->session()->regenerate();

            return $onboarding->redirect(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route("home.index");
    }

    public function destroy(Request $request)
    {
        if (! $request->filled('password')) {
            return back()
                ->withErrors(['delete_password' => 'Please enter your password.'])
                ->with('show_delete_account', true);
        }

        $user = $request->user();

        if (! Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['delete_password' => 'The password is incorrect.'])
                ->with('show_delete_account', true);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return redirect()->route('home.index');
    }
}
