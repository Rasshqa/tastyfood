<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                $admin = Auth::guard('admin')->user();
                $token = $request->session()->getId();
                $request->session()->put('admin_id', $admin->id);
                $request->session()->save();

                \Illuminate\Support\Facades\DB::table('sessions')->updateOrInsert(
                    ['id' => $token],
                    [
                        'user_id' => $admin->id,
                        'ip_address' => $request->ip(),
                        'user_agent' => substr((string) $request->userAgent(), 0, 500),
                        'payload' => base64_encode(json_encode([
                            'admin_id' => $admin->id,
                            '_token' => $request->session()->token(),
                            'login_admin_' . sha1(\App\Models\Admin::class) => $admin->id,
                        ])),
                        'last_activity' => time(),
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil',
                    'token' => $token,
                    'admin' => [
                        'id' => $admin->id,
                        'name' => $admin->name,
                        'email' => $admin->email,
                    ],
                ]);
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 401);
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil logout',
            ]);
        }

        return redirect()->route('admin.login');
    }
}
