<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('admin')->check()) {
            $token = $request->bearerToken()
                ?: $request->header('X-Auth-Token')
                ?: $request->header('X-Session-Id');

            if ($token) {
                $token = trim($token);
                $session = DB::table('sessions')->where('id', $token)->first();
                if ($session) {
                    if (!empty($session->user_id)) {
                        Auth::guard('admin')->loginUsingId($session->user_id);
                    }
                    if (!Auth::guard('admin')->check()) {
                        $payload = json_decode(base64_decode($session->payload), true) ?: @unserialize(base64_decode($session->payload));
                        if (is_array($payload)) {
                            $adminId = $payload['admin_id'] ?? null;
                            if (!$adminId) {
                                foreach ($payload as $key => $val) {
                                    if (str_starts_with($key, 'login_admin_') && is_numeric($val)) {
                                        $adminId = $val;
                                        break;
                                    }
                                }
                            }
                            if ($adminId) {
                                Auth::guard('admin')->loginUsingId($adminId);
                            }
                        }
                    }
                }
            }
        }

        if (!Auth::guard('admin')->check()) {
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi admin tidak sah atau telah berakhir.',
                ], 401);
            }
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
