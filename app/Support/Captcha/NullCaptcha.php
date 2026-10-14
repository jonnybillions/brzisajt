<?php

namespace App\Support\Captcha;

/** Captcha je isključena: štite zamka-polje, tajmer i limit zahteva. */
class NullCaptcha implements CaptchaVerifier
{
    public function enabled(): bool
    {
        return false;
    }

    public function verify(?string $token, ?string $ip): bool
    {
        return true;
    }
}
