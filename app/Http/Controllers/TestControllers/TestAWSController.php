<?php

namespace App\Http\Controllers\TestControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestAWSController extends Controller
{
    public function showUploadForm()
    {
        return view('test.aws');
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            // Store the file in the "inspire" directory on S3
            $path = $request->file('file')->store('inspire', 's3');

            // Get the file's URL
            $url = Storage::disk('s3')->url($path);

            return back()->with('success', 'File uploaded successfully. URL: ' . $url);
        }

        return back()->with('error', 'File not uploaded');
    }

    public function phpInfo(){
        return view('test.info');
    }
}
