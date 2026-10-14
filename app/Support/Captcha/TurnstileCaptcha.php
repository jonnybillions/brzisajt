<?php

namespace App\Support\Captcha;

use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Cloudflare Turnstile (CAPTCHA_DRIVER=turnstile). Ključevi — u .env.
 * https://developers.cloudflare.com/turnstile/
 */
class TurnstileCaptcha implements CaptchaVerifier
{
    public function __construct(private string $secretKey, private string $siteKey) {}

    public function enabled(): bool
    {
        return true;
    }

    public function siteKey(): string
    {
        return $this->siteKey;
    }

    public function verify(?string $token, ?string $ip): bool
    {
        if (! $token) {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(5)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $this->secretKey,
                'response' => $token,
                'remoteip' => $ip,
            ]);
        } catch (Throwable $e) {
            report($e);

            // Servis nije dostupan — ne gubimo upit: ostale provere su već prošle.
            return true;
        }

        return $response->json('success') === true;
    }
}
