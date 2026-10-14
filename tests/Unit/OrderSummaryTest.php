<?php

use App\Configurator\OrderSummary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('turns configurator choices into readable Serbian lines', function () {
    $lines = collect(OrderSummary::lines([
        'type' => 'onepage', 'scroll' => 'horizontal', 'pages' => ['home', 'custom'], 'custom_page' => 'Akcije',
        'style' => 'trust', 'hero' => 'image', 'menu_type' => 'burger', 'menu_position' => 'side', 'motion' => 'minimal',
        'colors' => ['#FF0000'], 'industry' => 'hotel', 'langs' => 'two', 'second_lang' => 'engleski',
        'features' => ['catalog'], 'texts' => 'have', 'images' => 'own', 'logo' => 'new', 'seo' => false,
    ]))->pluck('value', 'label');

    expect($lines['Tip'])->toBe('Jednostranični')
        ->and($lines['Kako se smenjuju sekcije?'])->toBe('Horizontalno skrolovanje')
        ->and($lines['Stranice'])->toBe('Početna, Akcije')
        ->and($lines['Meni'])->toBe('Skriven (dugme „hamburger“), sa strane')
        ->and($lines['Boje'])->toBe('#FF0000')
        ->and($lines['Jezici'])->toBe('Dva jezika: engleski')
        ->and($lines['Na sajtu'])->toBe('Kontakt forma, Katalog proizvoda ili usluga (bez online plaćanja)')
        ->and($lines['Logo'])->toBe("Treba mi novi (od 200\u{00A0}€)")
        ->and($lines['SEO promocija nakon pokretanja'])->toBe('ne');
});

it('formats the price with extras in euros', function () {
    expect(OrderSummary::price([]))->toBe("600\u{00A0}€")
        ->and(OrderSummary::price(['logo', 'seo']))->toBe("600\u{00A0}€ + logo od 200\u{00A0}€ + SEO od 400\u{00A0}€/mes.");
});
