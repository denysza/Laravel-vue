<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Display the form to request a password reset link.
     *
     * @param  string  $arg
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm($arg)
    {
        return view(config('const.template.password.reset'), ['arg' => $arg]);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $response = $this->broker($request->arg)->sendResetLink(
            $this->credentials($request)
        );

        return back()->with('status', 'パスワード再設定用のURLをメールで送信しました。')
                     ->withInput($request->only('email'));
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @param  string $arg
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker($arg)
    {
        return Password::broker($arg);
    }
}
