<?php

use App\Livewire\Configurator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/** Ispravni odgovori na sve korake konfiguratora. */
function validOrder(array $overrides = []): array
{
    return array_merge([
        'type' => 'multipage',
        'pages' => ['home', 'about', 'services', 'contacts'],
        'style' => 'trust',
        'hero' => 'slider',
        'menuType' => 'regular',
        'menuPosition' => 'top',
        'motion' => 'smooth',
        'industry' => 'restaurant',
        'langs' => 'one',
        'features' => ['map'],
        'texts' => 'help',
        'images' => 'stock',
        'logo' => 'have',
        'seo' => false,
        'name' => 'Ana',
        'phone' => '+381 63 123 4567',
        'email' => 'ana@example.rs',
        'channel' => 'viber',
        'consent' => true,
    ], $overrides);
}

/** Konfigurator sa popunjenom formom; vreme je „prošlo“ — antispam tajmer ne smeta. */
function filledConfigurator(array $overrides = [], array $params = []): Testable
{
    $component = Livewire::test(Configurator::class, $params);

    foreach (validOrder($overrides) as $key => $value) {
        $component->set("form.$key", $value);
    }

    test()->travel(config('brzisajt.antispam.min_fill_seconds') + 1)->seconds();

    return $component;
}
