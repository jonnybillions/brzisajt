{{-- Cloudflare Turnstile: učitava se samo ako je CAPTCHA_DRIVER=turnstile --}}
<div wire:ignore
     x-data
     x-init="
        const render = () => window.turnstile.render($refs.box, {
            sitekey: @js(config('brzisajt.antispam.turnstile.site_key')),
            language: 'sr',
            callback: (token) => $wire.set('captchaToken', token, false),
        });
        if (window.turnstile) { render(); } else {
            window.onBrziTurnstile = render;
            const s = document.createElement('script');
            s.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onBrziTurnstile&render=explicit';
            s.async = true;
            document.head.appendChild(s);
        }
     ">
    <div x-ref="box" class="min-h-[65px]"></div>
</div>
