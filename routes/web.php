<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::get('/', function (Request $request) {
    return view('welcome');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/send-test-email', function () {
    Mail::raw('This is a test email from localhost!', function ($message) {
        $message->to('recipient@example.com')
                ->subject('Test Email');
    });

    return 'Test email sent!';       
});