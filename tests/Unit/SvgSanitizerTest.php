<?php

use App\Support\SvgSanitizer;

it('strips scripts, event handlers and external links', function () {
    $clean = SvgSanitizer::clean('<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"><script>alert(2)</script>'
        .'<rect width="5" onclick="x"/><use href="#a"/><use href="https://evil.example/x.svg#a"/><foreignObject><b>x</b></foreignObject></svg>');

    expect($clean)->toContain('<rect width="5"/>')
        ->toContain('href="#a"')
        ->not->toContain('script')
        ->not->toContain('onload')
        ->not->toContain('onclick')
        ->not->toContain('evil.example')
        ->not->toContain('foreignObject');
});

it('rejects input that is not an SVG', function (?string $input) {
    expect(SvgSanitizer::clean($input))->toBeNull();
})->with([null, '', '<html><body>hi</body></html>', '<svg broken']);
