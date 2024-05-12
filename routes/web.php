<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs', function () {
    $yaml = file_get_contents(base_path('resources/swagger/api-docs.yaml'));
    return response($yaml, 200)->header('Content-Type', 'text/yaml');
});
