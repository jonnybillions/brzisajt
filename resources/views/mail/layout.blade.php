<!doctype html>
<html lang="sr-Latn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $title ?? __('site.brand.name') }}</title>
</head>
<body style="margin:0;padding:0;background:#F6F4EF;font-family:-apple-system,'Segoe UI',Roboto,Arial,sans-serif;color:#121016;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F6F4EF;">
    <tr>
        <td align="center" style="padding:32px 16px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;">
                <tr>
                    <td style="padding:0 4px 20px;font-size:22px;font-weight:800;letter-spacing:-0.02em;">
                        <span style="color:#6B3BFF;">{{ __('site.brand.wordmark_accent') }}</span>{{ __('site.brand.wordmark_rest') }}
                        <span style="font-size:12px;font-weight:700;color:#5C5766;">{{ __('site.brand.wordmark_by') }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="background:#FFFFFF;border:2px solid #121016;border-radius:24px;padding:32px 28px;">
                        {{ $slot }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px 4px 0;font-size:13px;color:#5C5766;">
                        {{ __('mail.footer') }} · <a href="{{ url('/') }}" style="color:#5C5766;">{{ parse_url(url('/'), PHP_URL_HOST) }}</a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
