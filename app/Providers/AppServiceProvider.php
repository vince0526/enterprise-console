<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function ($notifiable, string $token): string {
            // $notifiable can be any notifiable type; defensively cast
            $email = '';
            if (is_object($notifiable) && method_exists($notifiable, 'getEmailForPasswordReset')) {
                $e = $notifiable->getEmailForPasswordReset();
                $email = is_scalar($e) ? (string) $e : '';
            }

            $frontendRaw = config('app.frontend_url');
            $frontend = is_scalar($frontendRaw) ? (string) $frontendRaw : '';

            return sprintf('%s/password-reset/%s?email=%s', $frontend, (string) $token, urlencode($email));
        });
    }
}
