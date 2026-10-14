<?php

use App\Support\Typo;

it('formats rubles with non-breaking spaces', function () {
    expect(Typo::money(60000))->toBe("60\u{00A0}000\u{00A0}₽")
        ->and(Typo::money(450000, false))->toBe("450\u{00A0}000");
});

it('binds short words and dashes', function () {
    expect(Typo::apply('Сайт за 7 дней — и в срок'))
        ->toBe("Сайт за\u{00A0}7\u{00A0}дней\u{00A0}— и\u{00A0}в\u{00A0}срок");
});
