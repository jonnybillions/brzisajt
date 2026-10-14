<?php

use App\Support\Typo;

it('formats euro amounts the Serbian way', function () {
    expect(Typo::money(600))->toBe("600\u{00A0}€")
        ->and(Typo::money(4500))->toBe("4.500\u{00A0}€")
        ->and(Typo::money(1250000))->toBe("1.250.000\u{00A0}€")
        ->and(Typo::money(4500, false))->toBe('4.500');
});

it('binds short words, numbers and dashes', function () {
    expect(Typo::apply('Sajt za 7 dana — i u roku'))
        ->toBe("Sajt za\u{00A0}7\u{00A0}dana\u{00A0}— i\u{00A0}u\u{00A0}roku");
});

it('keeps the euro sign and per-month suffix together', function () {
    expect(Typo::apply('od 400 €/mes.'))->toBe("od\u{00A0}400\u{00A0}€/\u{2060}mes.");
});

it('uses Serbian quotation marks', function () {
    expect(Typo::apply('dugme "hamburger"'))->toBe('dugme „hamburger“');
});
