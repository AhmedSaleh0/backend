<?php

namespace App\Http\Controllers\User;

use App\Models\User\UserImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class UserImageController extends Controller
{
    /**
     * Display a listing of all user images.
     *
     * @group User Images
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $images = UserImage::all();
        return response()->json($images);
    }

    /**
     * Store or update a user's image.
     *
     * @group User Images
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @bodyParam user_id int required The ID of the user. Example: 1
     * @bodyParam image file required An image file to be uploaded as the user's profile picture.
     * @response 201 {
     *  "message": "Image uploaded successfully",
     *  "image": {
     *      "id": 1,
     *      "user_id": 1,
     *      "image_path": "https://your-bucket.s3.your-region.amazonaws.com/user_images/1.jpg"
     *  }
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'image' => 'required|image|max:25600', // Max 25MB file
        ]);

        $userId = $request->user_id;

        // Check if the user already has an image
        $existingImage = UserImage::where('user_id', $userId)->first();

        if ($existingImage) {
            // Delete the old image from S3
            Storage::disk('s3')->delete(parse_url($existingImage->image_path, PHP_URL_PATH));

            // Update the existing image record
            $path = $request->file('image')->store("user_images/{$userId}", 's3');
            $existingImage->update([
                'image_path' => Storage::disk('s3')->url($path)
            ]);
            $userImage = $existingImage;
        } else {
            // Create a new image record
            $path = $request->file('image')->store("user_images/{$userId}", 's3');
            $userImage = new UserImage([
                'user_id' => $userId,
                'image_path' => Storage::disk('s3')->url($path)
            ]);
            $userImage->save();
        }

        return response()->json(['message' => 'Image uploaded successfully', 'image' => $userImage], 201);
    }

    /**
     * Display the specified user image.
     *
     * @group User Images
     * @urlParam id int required The ID of the image. Example: 1
     * @response 200 {
     *  "id": 1,
     *  "user_id": 1,
     *  "image_path": "https://your-bucket.s3.your-region.amazonaws.com/user_images/1.jpg"
     * }
     * @param UserImage $userImage
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(UserImage $userImage)
    {
        return response()->json($userImage);
    }

    /**
     * Remove the specified user image from storage.
     *
     * @group User Images
     * @urlParam id int required The ID of the image. Example: 1
     * @response 200 {
     *  "message": "Image deleted successfully"
     * }
     * @param UserImage $userImage
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(UserImage $userImage)
    {
        // Delete the image from S3
        Storage::disk('s3')->delete(parse_url($userImage->image_path, PHP_URL_PATH));

        // Delete the record from the database
        $userImage->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }
}
