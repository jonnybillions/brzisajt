@php
    $faq = collect(__('site.faq.items'))->map(fn ($item) => [
        '@type' => 'Question',
        'name' => $item['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['a']],
    ])->all();

    $graph = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                '@id' => url('/#organization'),
                'name' => 'otlr.digital',
                'url' => 'https://otlr.digital',
                'email' => settings('email'),
                'telephone' => preg_replace('/[^\d+]/', '', (string) settings('phone')),
                'foundingDate' => '2012',
            ],
            [
                '@type' => 'Service',
                '@id' => url('/#service'),
                'name' => __('site.brand.name'),
                'serviceType' => 'Izrada sajta',
                'description' => __('site.meta.description', ['price' => money(settings()->int('base_price'))]),
                'provider' => ['@id' => url('/#organization')],
                'areaServed' => 'RU',
                'url' => route('home'),
                'offers' => [
                    '@type' => 'Offer',
                    'price' => (string) settings()->int('base_price'),
                    'priceCurrency' => 'RUB',
                    'availability' => 'https://schema.org/InStock',
                    'url' => route('home').'#configurator',
                ],
            ],
            [
                '@type' => 'FAQPage',
                '@id' => url('/#faq'),
                'mainEntity' => $faq,
            ],
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
