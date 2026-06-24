<?php

namespace App\Http\Middleware;

use App\Models\Login;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLogin
{
    public function __construct(
        protected JwtService $jwt
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie(config('jwt.cookie'));

        if ($token) {
            $payload = $this->jwt->validate($token);

            if ($payload && isset($payload->sub)) {
                $user = Login::find($payload->sub);

                if ($user && $user->status) {
                    $request->attributes->set('auth_user', $user);

                    if (! session()->has('loginId')) {
                        session([
                            'loginId' => $user->id,
                            'user_email' => $user->email,
                            'role' => $user->role,
                            'jwt_remember' => (bool) ($payload->remember ?? false),
                        ]);
                    }

                    return $next($request);
                }
            }
        }

        if (session()->has('loginId')) {
            session()->flush();
        }

        return redirect('/')
            ->with('error', 'Please login first')
            ->withCookie(\App\Http\Controllers\AuthController::forgetAuthCookie());
    }
}
