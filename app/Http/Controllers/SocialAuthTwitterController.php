<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Socialite;

class SocialAuthTwitterController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('twitter')->redirect();
    }

    /**
     * Return a callback method from twitter api.
     *
     * @return callback URL from twitter
     */
    public function callback()
    {
        $user = $service->createOrGetUser(Socialite::driver('twitter')->user());
        auth()->login($user);
        return redirect()->to('/home');
    }
}
