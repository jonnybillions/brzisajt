<?php

it('serves a sitemap with the landing and privacy pages', function () {
    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee('<loc>'.route('home').'</loc>', false)
        ->assertSee('<loc>'.route('privacy').'</loc>', false);
});

it('blocks indexing outside production', function () {
    $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /');
});

it('allows indexing in production and points to the sitemap', function () {
    app()->detectEnvironment(fn () => 'production');

    $this->get('/robots.txt')
        ->assertSee('Disallow: /admin')
        ->assertSee('Sitemap: '.route('sitemap'))
        ->assertDontSee("Disallow: /\n");
});

it('outputs meta tags and structured data', function () {
    $html = $this->get('/')->assertOk()
        ->assertSee('<link rel="canonical"', false)
        ->assertSee('property="og:image"', false)
        ->assertSee('<meta name="description"', false)
        ->getContent();

    preg_match('#<script type="application/ld\+json">(.+?)</script>#s', $html, $m);
    $graph = collect(json_decode($m[1], true)['@graph'])->keyBy('@type');

    expect($graph->keys()->all())->toEqualCanonicalizing(['Organization', 'Service', 'FAQPage'])
        ->and($graph['Service']['offers'])->toMatchArray(['price' => '600', 'priceCurrency' => 'EUR'])
        ->and($graph['FAQPage']['mainEntity'])->toHaveCount(10);
});

it('loads no analytics by default', function () {
    $this->get('/')
        ->assertDontSee('plausible.io', false)
        ->assertDontSee('googletagmanager', false)
        ->assertDontSee('cookieConsent', false)
        ->assertDontSee(__('site.footer.cookies'));
});

it('loads Plausible without a cookie banner', function () {
    settings()->put(['analytics_driver' => 'plausible', 'plausible_domain' => 'brzisajt.rs']);

    $this->get('/')
        ->assertSee('data-domain="brzisajt.rs"', false)
        ->assertSee('https://plausible.io/js/script.js', false)
        ->assertDontSee('cookieConsent', false);
});

it('needs a domain before Plausible is enabled', function () {
    settings()->put(['analytics_driver' => 'plausible']);

    $this->get('/')->assertDontSee('plausible.io', false);
});

it('puts GA4 behind the consent banner', function () {
    settings()->put(['analytics_driver' => 'ga4', 'ga4_id' => 'G-ABC1234']);

    $html = $this->get('/')
        ->assertSee("cookieConsent('G-ABC1234')", false)
        ->assertSee(__('site.consent.accept'))
        ->assertSee(__('site.footer.cookies'))
        ->getContent();

    // Google se učitava iz JavaScripta tek posle pristanka — ne iz HTML-a.
    expect($html)->not->toContain('googletagmanager.com/gtag/js');
});

it('ignores an invalid GA4 measurement ID', function () {
    settings()->put(['analytics_driver' => 'ga4', 'ga4_id' => 'UA-123"><script>']);

    $this->get('/')->assertDontSee('cookieConsent', false)->assertDontSee('UA-123', false);
});

it('does not reference third-party CDNs on the public site', function () {
    $html = $this->get('/')->getContent();

    expect($html)->not->toContain('fonts.googleapis.com')
        ->not->toContain('fonts.bunny.net')
        ->not->toContain('cdn.jsdelivr.net')
        ->not->toContain('unpkg.com')
        ->not->toContain('recaptcha')
        ->not->toContain('challenges.cloudflare.com');
});

it('has no references to other language versions in the codebase', function () {
    $dirs = ['app', 'config', 'lang', 'resources/views', 'resources/js', 'resources/css', 'routes', 'database/seeders', 'scripts'];
    $hits = [];

    foreach ($dirs as $dir) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path($dir), FilesystemIterator::SKIP_DOTS)) as $file) {
            if (! $file->isFile() || ! in_array($file->getExtension(), ['php', 'js', 'mjs', 'css', 'json', 'sh'], true)) {
                continue;
            }
            $text = file_get_contents($file->getPathname());
            if (preg_match('/[\x{0400}-\x{04FF}]|₽|bystro|yandex|metrica/iu', $text)) {
                $hits[] = str_replace(base_path().'/', '', $file->getPathname());
            }
        }
    }

    expect($hits)->toBe([]);
});
