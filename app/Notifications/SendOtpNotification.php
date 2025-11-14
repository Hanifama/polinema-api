<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SendOtpNotification extends Notification
{
    use Queueable;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Kode OTP Verifikasi Akun')
            ->line('Halo, berikut adalah kode OTP untuk verifikasi akunmu:')
            ->line("🔐 Kode OTP: **{$this->otp}**")
            ->line('Kode ini berlaku selama 3 menit.')
            ->line('Jangan berikan kode ini ke siapapun.');
    }
}
