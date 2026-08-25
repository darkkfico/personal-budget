<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\AutoBudget;
use App\Models\CustomBudget;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {

        User::create($request->only("name", "lastname", "email", "password"));

        return redirect()->route("auth.login");
    }
    public function loginForm()
    {
        return view("auth.login");
    }

    public function login(LoginRequest $request)
    {
        $remember = $request->remember;

        if (Auth::attempt(["email" => $request->email, 'password' => $request->password], $remember)) {
            $request->session()->regenerate();

            if (AutoBudget::where("user_id", Auth::id())->exists()) {

                session(["type" => "auto"]);
                return redirect()->route("auto.index");
            } elseif (CustomBudget::where("user_id", Auth::id())->exists()) {

                session(["type" => "custom"]);
                return redirect()->route("custom.index");
            } else {

                return redirect()->route("start.dashboard");
            }
        } else {
            return back()->withErrors([
                'email' => 'Invalid email or password.',
            ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route("home.index");
    }
}
