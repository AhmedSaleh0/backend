<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailVerificationController extends Controller
{
    /**
     * Display a message that the email verification link has been sent.
     *
     * @group Authentication
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @response 200 {
     *  "message": "Email verification link sent."
     * }
     */
    public function notice(Request $request)
    {
        return response()->json(['message' => 'Email verification link sent.']);
    }

    /**
     * Verify the user's email address.
     *
     * @group Authentication
     * @param EmailVerificationRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @response 200 {
     *  "message": "Email verified successfully."
     * }
     */
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return response()->json(['message' => 'Email verified successfully.']);
    }

    /**
     * Resend the email verification link.
     *
     * @group Authentication
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @response 200 {
     *  "message": "Email verification link resent."
     * }
     */
    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Email verification link resent.']);
    }
}
