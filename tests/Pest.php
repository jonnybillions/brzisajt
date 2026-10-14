<?php

use App\Livewire\Configurator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/** Валидные ответы на все шаги конфигуратора. */
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
        'name' => 'Анна',
        'phone' => '+7 999 123-45-67',
        'email' => 'anna@example.com',
        'channel' => 'telegram',
        'consent' => true,
    ], $overrides);
}

/** Конфигуратор с заполненной формой; время «прошло» — таймер антиспама не мешает. */
function filledConfigurator(array $overrides = [], array $params = []): Testable
{
    $component = Livewire::test(Configurator::class, $params);

    foreach (validOrder($overrides) as $key => $value) {
        $component->set("form.$key", $value);
    }

    test()->travel(config('brzisajt.antispam.min_fill_seconds') + 1)->seconds();

    return $component;
}
