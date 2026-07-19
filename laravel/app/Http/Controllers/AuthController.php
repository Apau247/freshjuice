<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $userId = $request->input('user_id');
        $password = $request->input('password');

        $user = DB::table('users')
            ->where('UserID', $userId)
            ->where('Status', 'Active')
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return back()
                ->withInput($request->only('user_id'))
                ->withErrors(['user_id' => 'Invalid credentials.']);
        }

        session([
            'user_id' => $user->UserID,
            'user_name' => $user->Name,
            'user_role' => $user->RoleID,
        ]);

        DB::table('users')
            ->where('UserID', $user->UserID)
            ->update(['updated_at' => now()]);

        return redirect()->intended(route('dashboard'));
    }

    public function logout()
    {
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    }
}
