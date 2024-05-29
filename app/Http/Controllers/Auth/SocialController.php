<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request; // Corrected import
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialController extends Controller
{
    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from Facebook.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Failed to login with Facebook.');
        }

        // Check if the user already exists
        $user = User::where('facebook_id', $facebookUser->id)->first();

        // Create a new user if it doesn't exist
        $user = User::updateOrCreate(['facebook_id' => $facebookUser->id], [
            'name' => $facebookUser->name,
            'email' => $facebookUser->email,
            'password' => bcrypt(Str::random(24)), // Use Str::random to generate a random password
        ]);

        // Log the user in
        Auth::login($user, true);

        return redirect('/'); // Redirect to your desired route
    }

    /**
     * Handle the data deletion request from Facebook.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataDeletionRequest(Request $request)
    {
        $signed_request = $request->input('signed_request');
        $data = $this->parseSignedRequest($signed_request);

        if (!$data) {
            return response()->json(['error' => 'Invalid signed request'], 400);
        }

        $user_id = $data['user_id'];

        // Start data deletion process for the user
        // Example: Delete user from the database
        $user = User::where('facebook_id', $user_id)->first();
        if ($user) {
            $user->delete();
        }

        // Generate a unique confirmation code and status URL
        $confirmation_code = Str::random(12); // Generate a random confirmation code
        $status_url = 'https://www.i-plus.co/deletion?code=' . $confirmation_code; // URL to track the deletion

        $response_data = [
            'url' => $status_url,
            'confirmation_code' => $confirmation_code,
        ];

        return response()->json($response_data);
    }

    /**
     * Parse the signed request from Facebook.
     *
     * @param string $signed_request
     * @return array|null
     */
    private function parseSignedRequest($signed_request)
    {
        list($encoded_sig, $payload) = explode('.', $signed_request, 2);

        $secret = config('services.facebook.client_secret'); // Use your app secret from config

        // Decode the data
        $sig = $this->base64UrlDecode($encoded_sig);
        $data = json_decode($this->base64UrlDecode($payload), true);

        // Confirm the signature
        $expected_sig = hash_hmac('sha256', $payload, $secret, true);
        if ($sig !== $expected_sig) {
            Log::error('Bad Signed JSON signature!');
            return null;
        }

        return $data;
    }

    /**
     * Decode a base64 URL encoded string.
     *
     * @param string $input
     * @return string
     */
    private function base64UrlDecode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
}
