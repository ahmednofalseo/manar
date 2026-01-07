<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * عرض صفحة طلب تأكيد البريد الإلكتروني
     */
    public function show()
    {
        return view('auth.verify');
    }

    /**
     * إرسال رابط التأكيد مرة أخرى
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard.index');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'تم إرسال رابط التأكيد مرة أخرى!');
    }

    /**
     * تأكيد البريد الإلكتروني
     */
    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard.index');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->route('dashboard.index')->with('verified', true);
    }
}
