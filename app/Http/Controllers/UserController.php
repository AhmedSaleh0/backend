<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Update user details.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @bodyParam first_name string optional The user's first name.
     * @bodyParam last_name string optional The user's last name.
     * @bodyParam email string optional The user's email address.
     * @bodyParam phone string optional The user's phone number.
     * @bodyParam country string optional The user's country.
     * @bodyParam birthdate date optional The user's birthdate in format day-month-year.
     * @bodyParam bio string optional A short bio for the user.
     */
    public function updateUser(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date_format:d-m-Y',
            'bio' => 'nullable|string|max:1000',
        ]);

        if (isset($validatedData['birthdate'])) {
            $validatedData['birthdate'] = Carbon::createFromFormat('d-m-Y', $validatedData['birthdate'])->toDateString();
        }

        $user->update($validatedData);

        return response()->json(['message' => 'User details updated successfully', 'user' => $user]);
    }

    /**
     * Update the user's username.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @bodyParam username string required The user's username.
     */
    public function updateUsername(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
        ]);

        $user->update(['username' => $validatedData['username']]);

        return response()->json(['message' => 'Username updated successfully', 'user' => $user]);
    }

        /**
     * Delete a user.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
