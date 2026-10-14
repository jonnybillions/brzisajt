<?php

use App\Configurator\OrderSummary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('turns configurator choices into readable lines', function () {
    $lines = collect(OrderSummary::lines([
        'type' => 'onepage', 'scroll' => 'horizontal', 'pages' => ['home', 'custom'], 'custom_page' => 'Акции',
        'style' => 'trust', 'hero' => 'image', 'menu_type' => 'burger', 'menu_position' => 'side', 'motion' => 'minimal',
        'colors' => ['#FF0000'], 'industry' => 'hotel', 'langs' => 'two', 'second_lang' => 'английский',
        'features' => ['catalog'], 'texts' => 'have', 'images' => 'own', 'logo' => 'new', 'seo' => false,
    ]))->pluck('value', 'label');

    expect($lines['Тип'])->toBe('Одностраничный')
        ->and($lines['Как переключаются разделы?'])->toBe('Горизонтальная прокрутка')
        ->and($lines['Страницы'])->toBe('Главная, Акции')
        ->and($lines['Меню'])->toBe('Скрытое (кнопка-«бургер»), сбоку')
        ->and($lines['Цвета'])->toBe('#FF0000')
        ->and($lines['Языки'])->toBe('Два языка: английский')
        ->and($lines['На сайте'])->toBe('Форма обратной связи, Каталог товаров или услуг (без онлайн-оплаты)')
        ->and($lines['Логотип'])->toBe("Нужен новый (от 20\u{00A0}000\u{00A0}₽)");
});

it('formats the price with extras', function () {
    expect(OrderSummary::price([]))->toBe("60\u{00A0}000\u{00A0}₽")
        ->and(OrderSummary::price(['logo', 'seo']))->toBe("60\u{00A0}000\u{00A0}₽ + логотип от 20\u{00A0}000\u{00A0}₽ + SEO от 35\u{00A0}000\u{00A0}₽/мес");
});
