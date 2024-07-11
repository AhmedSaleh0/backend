<?php

namespace App\Http\Controllers\User;

use App\Models\User\User;
use App\Models\User\UserImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class UserController extends Controller
{


    /**
     * Get authenticated user details.
     *
     * @group User
     * @authenticated
     * @response 200 {
     *  "user": {
     *      "id": 1,
     *      "first_name": "John",
     *      "last_name": "Doe",
     *      "email": "user@example.com",
     *      "phone": "+1234567890",
     *      "country": "USA",
     *      "birthdate": "1990-12-31",
     *      "bio": "Just a developer!",
     *      "username": "johndoe",
     *      "created_at": "2024-06-08T12:52:12.000000Z",
     *      "updated_at": "2024-06-08T12:52:12.000000Z",
     *      "facebook_id": null,
     *      "google_id": null
     *  },
     *  "user_image": "https://your-bucket.s3.your-region.amazonaws.com/user_images/1.jpg"
     * }
     * @response 401 {
     *  "message": "User not authenticated"
     * }
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserDetails()
    {
        $user = Auth::user();

        if ($user) {
            return response()->json([
                'user' => $user,
                'user_image' => $user->image ? $user->image->image_path : null,
                'user_skills' => $user->skills,
            ], 200);
        } else {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    }

    /**
     * Update user details.
     *
     * @group User
     * @authenticated
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @bodyParam first_name string optional The user's first name. Example: John
     * @bodyParam last_name string optional The user's last name. Example: Doe
     * @bodyParam email string optional The user's email address, must be unique. Example: user@example.com
     * @bodyParam phone string optional The user's phone number. Example: +1234567890
     * @bodyParam country string optional The user's country. Example: USA
     * @bodyParam birthdate date optional The user's birthdate in format day-month-year. Example: 31-12-1990
     * @bodyParam bio string optional A short bio for the user. Example: Just a developer!
     * @bodyParam display_country boolean optional Whether to display the user's country. Example: true
     * @bodyParam display_birthdate boolean optional Whether to display the user's birthdate. Example: true
     * @response 200 {
     *  "message": "User details updated successfully",
     *  "user": {
     *      "id": 1,
     *      "first_name": "John",
     *      "last_name": "Doe",
     *      "email": "user@example.com",
     *      "phone": "+1234567890",
     *      "country": "USA",
     *      "birthdate": "1990-12-31",
     *      "bio": "Just a developer!",
     *      "display_country": false,
     *      "display_birthdate": false
     *  }
     * }
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
            'display_country' => 'nullable|boolean',
            'display_birthdate' => 'nullable|boolean',
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
     * @group User
     * @authenticated
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @bodyParam username string required The user's username. Example: johndoe
     * @response 200 {
     *  "message": "Username updated successfully",
     *  "user": {
     *      "id": 1,
     *      "username": "johndoe"
     *  }
     * }
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
     * Update user details, upload an image, and assign skills in one go.
     *
     * @group User
     * @authenticated
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @bodyParam first_name string optional The user's first name. Example: John
     * @bodyParam last_name string optional The user's last name. Example: Doe
     * @bodyParam email string optional The user's email address, must be unique. Example: user@example.com
     * @bodyParam phone string optional The user's phone number. Example: +1234567890
     * @bodyParam country string optional The user's country. Example: USA
     * @bodyParam birthdate date optional The user's birthdate in format day-month-year. Example: 31-12-1990
     * @bodyParam bio string optional A short bio for the user. Example: Just a developer!
     * @bodyParam image file optional An image file to be uploaded as user's profile picture.
     * @bodyParam skills array optional An array of skill IDs to assign to the user. Example: [1, 2, 3]
     *
     * @response 200 {
     *  "message": "User profile updated successfully",
     *  "user": {
     *      "id": 1,
     *      "first_name": "John",
     *      "last_name": "Doe",
     *      "email": "user@example.com",
     *      "phone": "+1234567890",
     *      "country": "USA",
     *      "birthdate": "1990-12-31",
     *      "bio": "Just a developer!"
     *  },
     *  "image": {
     *      "id": 1,
     *      "user_id": 1,
     *      "image_path": "https://your-bucket.s3.your-region.amazonaws.com/user_images/1.jpg"
     *  },
     *  "skills": [
     *      {"id": 1, "name": "PHP", "category": "Backend"},
     *      {"id": 2, "name": "JavaScript", "category": "Frontend"}
     *  ]
     * }
     */
    public function updateUserProfile(Request $request)
    {
        // Start a transaction to ensure data integrity
        DB::beginTransaction();
        try {
            // Update User Details
            $user = Auth::user();
            $validatedUserData = $request->validate([
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'birthdate' => 'nullable|date_format:d-m-Y',
                'bio' => 'nullable|string|max:1000',
            ]);

            if (isset($validatedUserData['birthdate'])) {
                $validatedUserData['birthdate'] = Carbon::createFromFormat('d-m-Y', $validatedUserData['birthdate'])->toDateString();
            }

            $user->update($validatedUserData);

            // Handle Image Upload
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'required|image|max:25600',  // Max 25MB file
                ]);

                // Check if the user already has an image
                $existingImage = UserImage::where('user_id', $user->id)->first();

                if ($existingImage) {
                    // Delete the old image from S3
                    Storage::disk('s3')->delete(parse_url($existingImage->image_path, PHP_URL_PATH));

                    // Update the existing image record
                    $path = $request->file('image')->store("user_images/{$user->id}", 's3');
                    $existingImage->update([
                        'image_path' => Storage::disk('s3')->url($path)
                    ]);
                    $userImage = $existingImage;
                } else {
                    // Create a new image record
                    $path = $request->file('image')->store("user_images/{$user->id}", 's3');
                    $userImage = new UserImage([
                        'user_id' => $user->id,
                        'image_path' => Storage::disk('s3')->url($path)
                    ]);
                    $userImage->save();
                }
            }

            // Assign Skills
            if ($request->has('skills')) {
                $request->validate([
                    'skills.*' => 'exists:skills,id'
                ]);

                $user->skills()->sync($request->skills);
            }

            DB::commit();

            return response()->json([
                'message' => 'User profile updated successfully',
                'user' => $user,
                'image' => $userImage ?? 'No image uploaded',
                'skills' => $user->skills
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update user profile', 'error' => $e->getMessage()], 500);
        }
    }
}
