<?php

namespace App\Http\Controllers;

use App\Models\UserImage;
use Illuminate\Http\Request;

class UserImageController extends Controller
{
    /**
     * Display a listing of all user images.
     */
    public function index()
    {
        $images = UserImage::all();
        return response()->json($images);
    }

    /**
     * Store a newly uploaded image in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id', // Ensure the user ID exists
            'image' => 'required|image|max:25600',  // Max 25MB file
        ]);

        $path = $request->file('image')->store('user_images', 'public');

        $image = new UserImage([
            'user_id' => $request->user_id,
            'image_path' => $path
        ]);
        $image->save();

        return response()->json($image, 201);
    }

    /**
     * Display the specified user image.
     *
     * @param UserImage $userImage
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(UserImage $userImage)
    {
        return response()->json($userImage);
    }

    /**
     * Update the specified image in storage.
     *
     * @param Request $request
     * @param UserImage $userImage
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, UserImage $userImage)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id', // Validate user ID
            'image' => 'image|max:25600',  // Optional, Max 25MB file
        ]);

        if ($request->user_id != $userImage->user_id) {
            return response()->json(['message' => 'Unauthorized to update this image.'], 403);
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('user_images', 'public');
            $userImage->update([
                'image_path' => $path
            ]);
        }

        return response()->json($userImage);
    }

    /**
     * Remove the specified image from storage.
     *
     * @param UserImage $userImage
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(UserImage $userImage)
    {
        $userImage->delete();
        return response()->json(['message' => 'Image deleted successfully']);
    }
}
