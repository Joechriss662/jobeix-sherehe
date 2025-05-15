<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Services\SmsForwarderService;

class ForgotPasswordPhoneController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkPhone(Request $request)
    {
        $request->validate(['phone' => 'required|exists:users,phone']);

        $token = Str::random(6); // 6-digit code
        DB::table('password_reset_tokens')->updateOrInsert(
            ['phone' => $request->phone],
            ['token' => $token, 'created_at' => now()]
        );

        $message = "Your password reset code is: $token";

        try {
            $response = SmsForwarderService::send($request->phone, $message);
            if ($response->successful()) {
                return back()->with('status', 'Reset code sent to your phone!');
            } else {
                return back()->withErrors(['phone' => 'Failed to send SMS. Please try again.']);
            }
        } catch (\Exception $e) {
            \Log::error('SMSForwarder Connection Error:', [
                'message' => $e->getMessage(),
            ]);
            return back()->withErrors(['phone' => 'Failed to connect to the SMS gateway.']);
        }
    }

    public function showResetForm($token)
    {
        return view('auth.reset-password-phone', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'phone' => 'required|exists:users,phone',
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('phone', $request->phone)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return back()->withErrors(['token' => 'Invalid token or phone number.']);
        }

        $user = User::where('phone', $request->phone)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the token
        DB::table('password_reset_tokens')->where('phone', $request->phone)->delete();

        return redirect()->route('login')->with('status', 'Password reset successful!');
    }
}