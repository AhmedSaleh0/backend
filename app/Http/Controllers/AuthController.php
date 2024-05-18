<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @unauthenticated
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @bodyParam first_name string required The user's first name.
     * @bodyParam last_name string required The user's last name.
     * @bodyParam email string required The user's email address.
     * @bodyParam password string required The user's password.
     * @bodyParam password_confirmation string required The password confirmation.
     * @bodyParam phone string required The user's phone number.
     */
    public function signup(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:255',
        ]);

        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'phone' => $validatedData['phone'],
        ]);

        return response()->json(['message' => 'User successfully registered', 'user' => $user], 201);
    }


    /**
     * Login user and create token.
     * 
     * @unauthenticated
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @bodyParam login string required The login credential (username or email) of the user. Example: john@example.com
     * @bodyParam password string required The password. Example: pass1234
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $request->login, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('Personal Access Token')->accessToken;
            return response()->json(['token' => $token], 200);
        }

        throw ValidationException::withMessages([
            'login' => ['The provided credentials are incorrect.'],
        ]);
    }


    /**
     * Logout user (Revoke the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $user = Auth::user();
        if ($user) {
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
     * @unauthenticated
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @bodyParam email string required The email of the user who is requesting a password reset.
     */
    public function resetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)]);
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    /**
     * Change the user's password.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @bodyParam current_password string required The current password of the user.
     * @bodyParam new_password string required The new password to set.
     * @bodyParam new_password_confirmation string required Confirmation of the new password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'No authenticated user found'], 401);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'The provided password does not match your current password.'], 400);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Password changed successfully.']);
    }
}
