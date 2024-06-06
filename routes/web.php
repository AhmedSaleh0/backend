<?php

use App\Http\Controllers\TestControllers\TestAWSController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs', function () {
    $yaml = file_get_contents(base_path('resources/swagger/api-docs.yaml'));
    return response($yaml, 200)->header('Content-Type', 'text/yaml');
});

Route::get('info', [TestAWSController::class, 'phpInfo'])->name('phpInfo');



Route::get('upload', [TestAWSController::class, 'showUploadForm'])->name('showUploadForm');
Route::post('upload', [TestAWSController::class, 'upload'])->name('upload');
