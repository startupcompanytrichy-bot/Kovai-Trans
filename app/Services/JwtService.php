<?php

namespace App\Services;

use App\Models\Login;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Carbon;
use Throwable;
use UnexpectedValueException;

class JwtService
{
    public function issue(Login $user, bool $remember): string
    {
        $ttlMinutes = $remember
            ? (int) config('jwt.remember_ttl')
            : (int) config('jwt.ttl');

        $now = Carbon::now();
        $expiresAt = $now->copy()->addMinutes($ttlMinutes);

        $payload = [
            'iss' => config('app.url'),
            'sub' => $user->id,
            'iat' => $now->timestamp,
            'exp' => $expiresAt->timestamp,
            'remember' => $remember,
            'email' => $user->email,
            'role' => $user->role,
        ];

        return JWT::encode($payload, $this->secret(), config('jwt.algo'));
    }

    public function decode(string $token): object
    {
        return JWT::decode($token, new Key($this->secret(), config('jwt.algo')));
    }

    public function validate(string $token): ?object
    {
        try {
            return $this->decode($token);
        } catch (Throwable) {
            return null;
        }
    }

    public function cookieMinutes(bool $remember): int
    {
        return $remember
            ? (int) config('jwt.remember_ttl')
            : (int) config('jwt.ttl');
    }

    protected function secret(): string
    {
        $secret = (string) config('jwt.secret');

        if ($secret === '') {
            throw new UnexpectedValueException('JWT secret is not configured.');
        }

        return $secret;
    }
}
