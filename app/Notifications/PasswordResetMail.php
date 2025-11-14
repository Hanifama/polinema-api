<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordResetMail extends Notification
{
    // Tidak perlu implementasi Queueable dan ShouldQueue
    protected $newPassword;

    public function __construct($newPassword)
    {
        $this->newPassword = $newPassword;
    }

    public function via($notifiable)
    {
        return ['mail'];  // Hanya melalui email
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Permintaan Pengaturan Ulang Kata Sandi')
                    ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun Anda di Polinema.')
                    ->line('Kode reset kata sandi Anda adalah: ' . $this->newPassword)
                    ->line('Kode ini akan kadaluarsa dalam 3 menit.');
    }

    public function toArray($notifiable)
    {
        return [
            'reset_code' => $this->newPassword,
        ];
    }
}
