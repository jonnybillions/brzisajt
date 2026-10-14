<?php

use App\Models\ClientLogo;
use App\Models\Project;

it('renders the landing with all sections', function () {
    $project = Project::factory()->create(['name' => 'Probni projekat Čačak']);
    ClientLogo::factory()->create(['name' => 'RŽD (Ruske železnice)']);

    $this->get('/')
        ->assertOk()
        ->assertSee('<html lang="sr-Latn"', false)
        ->assertSee('Probni projekat Čačak')
        ->assertSee('?style='.$project->slug, false)
        ->assertSee("600\u{00A0}€", false)
        ->assertSee("4.500\u{00A0}€", false)
        ->assertSee('Nije konstruktor!')
        ->assertSeeInOrder(['id="why"', 'id="how"', 'id="examples"', 'id="pricing"', 'id="configurator"', 'id="faq"', 'id="contacts"'], false);
});

it('hides unpublished projects', function () {
    Project::factory()->create(['name' => 'Skriveni', 'is_published' => false]);

    $this->get('/')->assertOk()->assertDontSee('Skriveni');
});

it('uses the base price from admin settings', function () {
    settings()->put(['base_price' => 750]);

    $this->get('/')->assertSee("750\u{00A0}€", false)->assertDontSee("600\u{00A0}€", false);
});

it('renders the privacy page with its placeholder', function () {
    $this->get('/privacy')->assertOk()->assertSee('[TODO]')->assertSee('Zakon o zaštiti podataka o ličnosti');
});

it('shows Viber and WhatsApp contacts only when set', function () {
    $this->get('/')->assertDontSee('viber://', false)->assertDontSee('wa.me', false);

    settings()->put(['viber' => '+381 63 115 0495', 'whatsapp' => '+381 63 115 0495']);

    $this->get('/')
        ->assertSee('viber://chat?number=%2B381631150495', false)
        ->assertSee('https://wa.me/381631150495', false);
});

it('contains no language switcher or references to other language versions', function () {
    $html = $this->get('/')->getContent();

    expect($html)->not->toMatch('/[\x{0400}-\x{04FF}]/u')
        ->not->toContain('hreflang')
        ->not->toContain('bystro')
        ->not->toContain('₽');
});
