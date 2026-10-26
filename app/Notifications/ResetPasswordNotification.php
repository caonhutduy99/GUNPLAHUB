<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    use Queueable;

    public function __construct(string $token)
    {
        parent::__construct($token);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Đặt lại mật khẩu - GunplaHub')
            ->greeting('Xin chào '.$notifiable->full_name.',')
            ->line('Bạn vừa yêu cầu đặt lại mật khẩu tài khoản GunplaHub.')
            ->action('Đặt lại mật khẩu', route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]))
            ->line('Liên kết này sẽ hết hạn sau '.config('auth.passwords.users.expire').' phút.')
            ->line('Nếu bạn không yêu cầu thay đổi mật khẩu, hãy bỏ qua email này.');
    }
}
