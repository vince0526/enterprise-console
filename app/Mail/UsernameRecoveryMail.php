<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UsernameRecoveryMail extends Mailable
{
    use Queueable, SerializesModels;

    public int $code;

    public string $username;

    public function __construct(int $code, string $username)
    {
        $this->code = $code;
        $this->username = $username;
    }

    public function build(): self
    {
        return $this->subject('Your username recovery code')
            ->view('emails.username_recovery');
    }
}
