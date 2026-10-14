<?php

namespace App\Support\Captcha;

interface CaptchaVerifier
{
    /** Da li treba prikazati widget na formi. */
    public function enabled(): bool;

    public function verify(?string $token, ?string $ip): bool;
}
