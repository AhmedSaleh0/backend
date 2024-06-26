<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\PasswordReset;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    const OTP_EXPIRATION_MINUTES = 15; // Set the OTP expiration time

    /**
     * Register a new user and auto login.
     *
     * @group Authentication
     * @unauthenticated
     *
     * @bodyParam first_name string required The user's first name.
     * @bodyParam last_name string required The user's last name.
     * @bodyParam email string required The user's email address.
     * @bodyParam password string required The user's password.
     * @bodyParam password_confirmation string required The password confirmation.
     * @bodyParam phone string required The user's phone number. Example: 507742230
     * @bodyParam country_code string required The user's country code. Example: +1
     *
     * @response {
     *   "message": "User successfully registered. Please check your email to verify your account.",
     *   "user": {
     *     "id": 1,
     *     "first_name": "John",
     *     "last_name": "Doe",
     *     "email": "john.doe@example.com",
     *     "created_at": "2024-06-09T00:00:00.000000Z",
     *     "updated_at": "2024-06-09T00:00:00.000000Z"
     *   },
     *   "token": "token"
     * }
     */
    public function signup(Request $request)
    {
        // Validate the request data, including making the phone number unique
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'country_code' => 'required|string|max:5',
            'phone' => 'required|string|max:20|unique:users|regex:/^\+?[0-9()\s-]+$/',
        ]);

        // Create a new user with the validated data
        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'country_code' => $validatedData['country_code'],
            'phone' => $validatedData['phone'],
        ]);

        $user->sendEmailVerificationNotification();

        // Automatically log in the user and create a token
        $token = $user->createToken('Personal Access Token')->accessToken;

        // Return a success response with the user and token data
        return response()->json([
            'message' => 'User successfully registered. Please check your email to verify your account.',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * Login user and create token.
     *
     * @group Authentication
     * @unauthenticated
     *
     * @bodyParam login string required The login credential (username or email) of the user. Example: john@example.com
     * @bodyParam password string required The password. Example: pass1234
     *
     * @response {
     *   "token": "token",
     *   "user": {
     *     "id": 1,
     *     "first_name": "John",
     *     "last_name": "Doe",
     *     "email": "john.doe@example.com",
     *     "created_at": "2024-06-09T00:00:00.000000Z",
     *     "updated_at": "2024-06-09T00:00:00.000000Z"
     *   },
     *   "user_image": "https://your-bucket.s3.your-region.amazonaws.com/user_images/1/image.jpg"
     * }
     */
    public function login(Request $request)
    {
        // Validate the login credentials
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Determine if the login is an email or username
        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Attempt to authenticate the user
        if (Auth::attempt([$field => $request->login, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('Personal Access Token')->accessToken;

            // Return a success response with the token and user data
            return response()->json([
                'token' => $token,
                'user' => $user,
                'user_image' => $user->image ? $user->image->image_path : null,
            ], 200);
        }

        // If authentication fails, throw a validation exception
        throw ValidationException::withMessages([
            'login' => ['The provided credentials are incorrect.'],
        ]);
    }

    /**
     * Logout user (Revoke the token).
     *
     * @group Authentication
     * @authenticated
     *
     * @response {
     *   "message": "Successfully logged out"
     * }
     */
    public function logout()
    {
        $user = Auth::user();
        if ($user) {
            // Revoke all tokens for the authenticated user
            $user->tokens->each(function ($token, $key) {
                $token->delete();
            });
            return response()->json(['message' => 'Successfully logged out'], 200);
        }

        return response()->json(['message' => 'No authenticated user found'], 401);
    }

    /**
     * Send a password reset link to the given user.
     *
     * @group Authentication
     * @unauthenticated
     *
     * @bodyParam email string required The email of the user who is requesting a password reset.
     *
     * @response {
     *   "message": "We have emailed your password reset link!"
     * }
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validate the request email
        $request->validate(['email' => 'required|email']);

        // Send the password reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Return the appropriate response based on the status
        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 400);
    }

    /**
     * Change the user's password.
     *
     * @group Authentication
     * @authenticated
     *
     * @bodyParam current_password string required The current password of the user.
     * @bodyParam new_password string required The new password to set.
     * @bodyParam new_password_confirmation string required Confirmation of the new password.
     *
     * @response {
     *   "message": "Password changed successfully."
     * }
     */
    public function changePassword(Request $request)
    {
        // Validate the password change request
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::guard('api')->user();

        if (!$user) {
            Log::info('No authenticated user found');
            return response()->json(['message' => 'No authenticated user found'], 401);
        }

        Log::info('Authenticated user', ['user' => $user]);

        // Check if the current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'The provided password does not match your current password.'], 400);
        }

        // Update the user's password
        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Password changed successfully.']);
    }

    /**
     * Send a password reset OTP to the given user.
     *
     * @group Authentication
     * @unauthenticated
     *
     * @bodyParam email string required The email of the user who is requesting a password reset.
     *
     * @response {
     *   "message": "OTP sent successfully."
     * }
     */
    public function sendResetOtp(Request $request)
    {
        // Validate the request email
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email not found.'], 404);
        }

        $otp = rand(1000, 9999); // Generate a 4-digit OTP

        // Save or update the OTP in the password_resets table
        PasswordReset::updateOrCreate(
            ['email' => $request->email],
            ['token' => $otp, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        );

        // Send OTP to user's email
        Mail::send('emails.password_reset_otp', ['otp' => $otp], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Your Password Reset OTP');
        });

        return response()->json(['message' => 'OTP sent successfully.'], 200);
    }

    /**
     * Verify the password reset OTP.
     *
     * @group Authentication
     * @unauthenticated
     *
     * @bodyParam email string required The email of the user who is verifying the OTP.
     * @bodyParam otp string required The OTP to verify.
     *
     * @response {
     *   "message": "OTP verified successfully."
     * }
     */
    public function verifyResetOtp(Request $request)
    {
        // Validate the OTP verification request
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);

        // Find the password reset record
        $resetRecord = PasswordReset::where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        if (!$resetRecord) {
            return response()->json(['message' => 'Invalid OTP.'], 400);
        }

        // Check if the OTP is expired
        if (Carbon::parse($resetRecord->created_at)->addMinutes(self::OTP_EXPIRATION_MINUTES)->isPast()) {
            return response()->json(['message' => 'OTP has expired.'], 400);
        }

        return response()->json(['message' => 'OTP verified successfully.'], 200);
    }

    /**
     * Reset the password using the verified OTP.
     *
     * @group Authentication
     * @unauthenticated
     *
     * @bodyParam email string required The email of the user who is resetting the password.
     * @bodyParam otp string required The verified OTP.
     * @bodyParam password string required The new password.
     * @bodyParam password_confirmation string required Confirmation of the new password.
     *
     * @response {
     *   "message": "Password reset successfully."
     * }
     */
    public function resetPassword(Request $request)
    {
        // Validate the password reset request
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Find the password reset record
        $resetRecord = PasswordReset::where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        if (!$resetRecord) {
            return response()->json(['message' => 'Invalid OTP.'], 400);
        }

        // Check if the OTP is expired
        if (Carbon::parse($resetRecord->created_at)->addMinutes(self::OTP_EXPIRATION_MINUTES)->isPast()) {
            return response()->json(['message' => 'OTP has expired.'], 400);
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Update the user's password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the reset record
        $resetRecord->delete();

        return response()->json(['message' => 'Password reset successfully.'], 200);
    }
}
