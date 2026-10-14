<?php

use App\Models\ClientLogo;
use App\Models\Project;

it('renders the landing with all sections', function () {
    $project = Project::factory()->create(['name' => 'Тестовый проект']);
    ClientLogo::factory()->create(['name' => 'РЖД']);

    $this->get('/')
        ->assertOk()
        ->assertSee('<html lang="ru"', false)
        ->assertSee('Тестовый проект')
        ->assertSee('?style='.$project->slug, false)
        ->assertSee("60\u{00A0}000\u{00A0}₽", false)
        ->assertSeeInOrder(['id="why"', 'id="how"', 'id="examples"', 'id="pricing"', 'id="configurator"', 'id="faq"', 'id="contacts"'], false);
});

it('hides unpublished projects', function () {
    Project::factory()->create(['name' => 'Скрытый', 'is_published' => false]);

    $this->get('/')->assertOk()->assertDontSee('Скрытый');
});

it('uses the base price from admin settings', function () {
    settings()->put(['base_price' => 75000]);

    $this->get('/')->assertSee("75\u{00A0}000", false)->assertDontSee("60\u{00A0}000\u{00A0}₽", false);
});

it('renders the privacy page with its placeholder', function () {
    $this->get('/privacy')->assertOk()->assertSee('[TODO]');
});
