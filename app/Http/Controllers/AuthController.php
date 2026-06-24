<?php

namespace App\Http\Controllers;

use App\Mail\SendOtpMail;
use App\Models\Login;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct(
        protected JwtService $jwt
    ) {}

    public function index()
    {
        return view('Authorization.Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = Login::where('email', $request->username)
            ->orWhere('mobile', $request->username)
            ->first();

        if (! $user) {
            return back()->with('error', 'User not found');
        }

        if ($request->password != $user->password) {
            return back()->with('error', 'Invalid Password');
        }

        if (! $user->status) {
            return back()->with('error', 'Your account is inactive');
        }

        $remember = $request->boolean('remember');
        $token = $this->jwt->issue($user, $remember);

        session([
            'loginId' => $user->id,
            'user_email' => $user->email,
            'role' => $user->role,
            'jwt_remember' => $remember,
        ]);

        $cookieMinutes = $this->jwt->cookieMinutes($remember);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Login Successful')
            ->withCookie($this->authCookie($token, $cookieMinutes));
    }

    public function forgotPassword()
    {
        return view('Authorization.ForgotPassword');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = Login::where('email', $request->email)->active()->first();

        if (! $user) {
            return back()->with('error', 'No account found with this email');
        }

        DB::table('password_resets')
            ->where('email', $request->email)
            ->where('expires_at', '<', now())
            ->delete();

        $todayCount = DB::table('password_resets')
            ->where('email', $request->email)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        if ($todayCount >= 2) {
            return back()->with('error', "Today's OTP limit reached (max 2 per day). Please try again tomorrow.");
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = Str::random(64);

        $name = $user->user?->name ?? explode('@', $request->email)[0];

        try {
            \Illuminate\Support\Facades\Mail::to($request->email)->send(new SendOtpMail($otp, $request->email, $name));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email. Please try again later.');
        }

        DB::table('password_resets')->insert([
            'email'      => $request->email,
            'otp'        => $otp,
            'token'      => $token,
            'expires_at' => now()->addMinutes(2),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('password.verify-form', ['token' => $token])
            ->with('success', 'OTP sent to your email');
    }

    public function verifyForm(Request $request)
    {
        $token = $request->query('token');

        if (! $token) {
            return redirect()->route('password.forgot')->with('error', 'Invalid request');
        }

        $record = DB::table('password_resets')
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (! $record) {
            return redirect()->route('password.forgot')->with('error', 'Invalid or expired request');
        }

        return view('Authorization.VerifyOtp', [
            'token'          => $token,
            'created_at_ts'  => strtotime($record->created_at) * 1000,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'otp'   => ['required', 'string', 'size:6'],
        ]);

        $record = DB::table('password_resets')
            ->where('token', $request->token)
            ->where('expires_at', '>', now())
            ->first();

        if (! $record) {
            return back()->with('error', 'Invalid or expired request');
        }

        if ($record->otp !== $request->otp) {
            return back()->with('error', 'Invalid OTP');
        }

        // OTP verified — generate a fresh token for the reset step and expire the OTP record
        $newToken = Str::random(64);

        DB::table('password_resets')
            ->where('id', $record->id)
            ->update([
                'token'      => $newToken,
                'otp'        => null,
                'expires_at' => now()->addMinutes(30),
                'updated_at' => now(),
            ]);

        return redirect()->route('password.reset-form', ['token' => $newToken])
            ->with('success', 'OTP verified. Set your new password.');
    }

    public function resetForm(Request $request)
    {
        $token = $request->query('token');

        if (! $token) {
            return redirect()->route('password.forgot')->with('error', 'Invalid request');
        }

        $record = DB::table('password_resets')
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->whereNull('otp') // must have passed OTP verification
            ->first();

        if (! $record) {
            return redirect()->route('password.forgot')->with('error', 'Invalid or expired request');
        }

        return view('Authorization.ResetPassword', ['token' => $token]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'token'                => ['required', 'string'],
            'password'             => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $record = DB::table('password_resets')
            ->where('token', $request->token)
            ->where('expires_at', '>', now())
            ->whereNull('otp')
            ->first();

        if (! $record) {
            return back()->with('error', 'Invalid or expired request');
        }

        $user = Login::where('email', $record->email)->first();

        if (! $user) {
            return back()->with('error', 'User not found');
        }

        $user->update(['password' => $request->password]);

        DB::table('password_resets')->where('id', $record->id)->delete();

        return redirect()->route('login')
            ->with('success', 'Password reset successfully. Please login with your new password.');
    }

    protected function passwordMatches(string $plain, string $stored): bool
    {
        if (Hash::check($plain, $stored)) {
            return true;
        }

        return hash_equals($stored, $plain);
    }

    protected function authCookie(string $token, int $minutes)
    {
        return cookie(
            config('jwt.cookie'),
            $token,
            $minutes,
            '/',
            null,
            config('session.secure', false),
            true,
            false,
            'lax'
        );
    }

    public static function forgetAuthCookie()
    {
        return Cookie::forget(config('jwt.cookie'));
    }
}
