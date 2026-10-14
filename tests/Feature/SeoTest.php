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
        ->and($graph['Service']['offers'])->toMatchArray(['price' => '60000', 'priceCurrency' => 'RUB'])
        ->and($graph['FAQPage']['mainEntity'])->toHaveCount(10);
});

it('loads Yandex Metrica only when an ID is set', function () {
    $this->get('/')->assertDontSee('mc.yandex.ru');

    settings()->put(['yandex_metrica_id' => '12345678']);
    $this->get('/')->assertSee('mc.yandex.ru/metrika/tag.js', false)->assertSee('ym(12345678', false);
});

it('does not reference third-party CDNs on the public site', function () {
    $html = $this->get('/')->getContent();

    expect($html)->not->toContain('fonts.googleapis.com')
        ->not->toContain('fonts.bunny.net')
        ->not->toContain('cdn.jsdelivr.net')
        ->not->toContain('unpkg.com')
        ->not->toContain('recaptcha')
        ->not->toContain('cloudflare');
});
