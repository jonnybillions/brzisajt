<?php

namespace App\Providers;

use App\Support\Captcha\CaptchaVerifier;
use App\Support\Captcha\NullCaptcha;
use App\Support\Captcha\TurnstileCaptcha;
use App\Support\Settings;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Settings::class);

        $this->app->bind(CaptchaVerifier::class, function () {
            $config = config('brzisajt.antispam');

            return $config['captcha'] === 'turnstile' && $config['turnstile']['secret_key']
                ? new TurnstileCaptcha($config['turnstile']['secret_key'], (string) $config['turnstile']['site_key'])
                : new NullCaptcha;
        });
    }

    public function boot(): void
    {
        // JS je potreban samo za interakciju — ne smetamo CSS-u i fontovima prvog ekrana.
        Vite::useScriptTagAttributes(['fetchpriority' => 'low']);
    }
}
