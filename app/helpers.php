<?php

use App\Support\Settings;
use App\Support\Typo;

if (! function_exists('settings')) {
    function settings(?string $key = null, mixed $default = null): mixed
    {
        $settings = app(Settings::class);

        return $key === null ? $settings : $settings->get($key, $default);
    }
}

if (! function_exists('money')) {
    function money(int|float $amount): string
    {
        return Typo::money($amount);
    }
}

if (! function_exists('tx')) {
    /**
     * Prevod sa tipografijom. Placeholder :price se automatski
     * popunjava trenutnom osnovnom cenom.
     */
    function tx(string $key, array $replace = []): string
    {
        $replace += ['price' => Typo::money(app(Settings::class)->int('base_price'))];

        return Typo::apply(__($key, $replace));
    }
}

if (! function_exists('typo')) {
    function typo(?string $text): string
    {
        return Typo::apply($text);
    }
}

if (! function_exists('tel_href')) {
    /** „+381 63 115 0495“ → „tel:+381631150495“ */
    function tel_href(?string $phone): string
    {
        return 'tel:'.preg_replace('/[^\d+]/', '', (string) $phone);
    }
}

if (! function_exists('viber_href')) {
    function viber_href(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return str_starts_with($value, 'viber:') || str_starts_with($value, 'http')
            ? $value
            : 'viber://chat?number='.rawurlencode('+'.preg_replace('/\D/', '', $value));
    }
}

if (! function_exists('whatsapp_href')) {
    function whatsapp_href(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return str_starts_with($value, 'http') ? $value : 'https://wa.me/'.preg_replace('/\D/', '', $value);
    }
}
