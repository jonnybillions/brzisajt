<?php

// Podešavanja Brzisajta. Svaka vrednost iz grupe `settings` može da se
// promeni u admin panelu (Podešavanja) — vidi App\Support\Settings.

return [

    'settings' => [
        // Cene, €
        'base_price' => (int) env('BRZI_BASE_PRICE', 600),
        'extra_logo_price' => (int) env('BRZI_EXTRA_LOGO_PRICE', 200),
        'extra_seo_price' => (int) env('BRZI_EXTRA_SEO_PRICE', 400),

        // Kontakt
        'phone' => env('BRZI_PHONE', '+381 63 115 0495'),
        'email' => env('BRZI_EMAIL', 'filip@otlr.digital'),
        'viber' => env('BRZI_VIBER'),       // broj telefona u međunarodnom formatu
        'whatsapp' => env('BRZI_WHATSAPP'), // broj telefona u međunarodnom formatu

        // Kome se šalju upiti (odvojeno zarezom)
        'notify_emails' => env('ORDER_NOTIFY_EMAILS', 'filip@otlr.digital'),

        // Analitika: none | plausible | ga4
        'analytics_driver' => env('ANALYTICS_DRIVER', 'none'),
        'plausible_domain' => env('PLAUSIBLE_DOMAIN'),
        'plausible_script' => env('PLAUSIBLE_SCRIPT_URL', 'https://plausible.io/js/script.js'),
        'ga4_id' => env('GA4_MEASUREMENT_ID'),

        // SVG logotip umesto tekstualnog (kod <svg>…</svg>)
        'logo_svg' => null,
    ],

    'limits' => [
        'max_pages' => 7,
        'upload_max_kb' => 20 * 1024,
        'upload_mimes' => ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'webp', 'svg', 'zip'],
    ],

    'antispam' => [
        'min_fill_seconds' => (int) env('BRZI_MIN_FILL_SECONDS', 8),
        'rate_limit_per_hour' => (int) env('BRZI_RATE_LIMIT', 5),
        // none | turnstile
        'captcha' => env('CAPTCHA_DRIVER', 'none'),
        'turnstile' => [
            'site_key' => env('TURNSTILE_SITE_KEY'),
            'secret_key' => env('TURNSTILE_SECRET_KEY'),
        ],
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
    ],

    'admin' => [
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
        'name' => env('ADMIN_NAME', 'Administrator'),
    ],
];
