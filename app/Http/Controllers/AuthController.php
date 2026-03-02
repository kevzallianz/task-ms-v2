<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'remember' => 'sometimes|boolean',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $routes = match (Auth::user()->role) {
                'superadmin' => 'superadmin.campaigns',
                'user' => 'user.overview',
                'admin' => 'admin.campaigns',
                default => 'user.overview',
            };

            return response()->json([
                'message' => "You've login successfully!",
                "redirect" => route($routes)
            ], 200);
        }

        return response()->json([
            'message' => 'The provided credentials do not match our records.'
        ], 401);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'nullable|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return response()->json([
            'message' => "Registration successful! You've been logged in.",
            "redirect" => route('user.overview')
        ], 201);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($request->wantsJson() || $request->ajax()) {
            if ($status === Password::RESET_LINK_SENT) {
                return response()->json(['message' => __($status)], 200);
            }

            return response()->json(['message' => __($status)], 400);
        }

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($request->wantsJson() || $request->ajax()) {
            if ($status === Password::PASSWORD_RESET) {

                Auth::loginUsingId(User::where('email', $request->email)->first()->id);
                $request->session()->regenerate();

                return response()->json([
                    'message' => __($status),
                    'redirect' => route('user.overview')
                ], 200);
            }

            return response()->json(['message' => __($status)], 400);
        }

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('password.reset.success')->with('status', __($status));
        }

        return back()->withErrors(['email' => [__($status)]]);
    }

    public function showResetSuccess()
    {
        return view('auth.reset-success');
    }
}
