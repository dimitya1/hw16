<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $url = 'https://www.facebook.com/v8.0/dialog/oauth';

    $parameters = [
        'client_id' => env('OAUTH_FACEBOOK_CLIENT_ID'),
        'redirect_uri' => env('OAUTH_FACEBOOK_CALLBACK_URI'),
        'scope' => 'email'
    ];

    $url .= '?' . http_build_query($parameters);

    return view('login', ['url' => $url]);
});


Route::get('/callback', function () {

    $response = \Illuminate\Support\Facades\Http::post('https://graph.facebook.com/v8.0/oauth/access_token', [
        'client_id' => env('OAUTH_FACEBOOK_CLIENT_ID'),
        'redirect_uri' => env('OAUTH_FACEBOOK_CALLBACK_URI'),
        'client_secret' => env('OAUTH_FACEBOOK_CLIENT_SECRET'),
        'code' => request()->get('code'),
    ]);

    $responseData = \Illuminate\Support\Facades\Http::
    get('https://graph.facebook.com/me?fields=name,email&access_token=' . $response['access_token']);

    if (($user = \App\User::where('email', '=', $responseData['email'])->first()) === null) {
        $user = new \App\User;
        $user->name = $responseData['name'];
        $user->email = $responseData['email'];
        $user->email_verified_at = now();
        $user->password = \Illuminate\Support\Facades\Hash::make(Str::random(50));
        $user->remember_token = Str::random(10);
        $user->save();
    }

    \Illuminate\Support\Facades\Auth::login($user);

    return redirect('/member');
});


Route::get('/member', function () {
    return view('member');
});


Route::get('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();

    return redirect('/');
});
