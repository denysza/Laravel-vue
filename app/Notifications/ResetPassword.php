<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    use Queueable;

    private $token;
    private $arg;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token, $arg)
    {
        $this->token = $token;
        $this->arg = $arg;  // auth.phpに定義されているパスワードブローカを指定する文字列
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->from(config('const.password.send_from'), mb_encode_mimeheader(config('const.password.sender')))
                    ->subject('パスワード初期化についてのお知らせ')
                    ->view('emails.passwordreset', [
                        'reset_url' => route('password.reset', ['arg' => $this->arg, 'token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]),
                        'count' => config("auth.passwords.{$this->arg}.expire")
                    ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
