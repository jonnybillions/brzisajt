<?php

use App\Livewire\Configurator;
use App\Models\Order;
use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Mail::fake();
    Storage::fake('orders');
    RateLimiter::clear('order-submit:127.0.0.1');
});

it('stores a valid order and shows the success state', function () {
    filledConfigurator(['logo' => 'new', 'seo' => true, 'comment' => 'Do proleća'])
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted.number', 'BS-0001')
        ->assertSee('Upit je poslat!')
        ->assertSee('BS-0001')
        ->assertDispatched('cfg-submitted', number: 'BS-0001');

    $order = Order::sole();
    expect($order->number)->toBe('BS-0001')
        ->and($order->status->value)->toBe('new')
        ->and($order->name)->toBe('Ana')
        ->and($order->channel)->toBe('viber')
        ->and($order->comment)->toBe('Do proleća')
        ->and($order->extras)->toBe(['logo', 'seo'])
        ->and($order->consent_at)->not->toBeNull()
        ->and($order->ip)->toBe('127.0.0.1')
        ->and($order->config)->toMatchArray([
            'type' => 'multipage',
            'pages' => ['home', 'about', 'services', 'contacts'],
            'scroll' => null,
            'features' => ['map'],
            'logo' => 'new',
            'seo' => true,
        ]);
});

it('numbers orders sequentially', function () {
    Order::unguarded(fn () => Order::create(['name' => 'X', 'config' => [], 'consent_at' => now()]));

    filledConfigurator()->call('submit')->assertSet('submitted.number', 'BS-0002');
});

it('stores uploaded files on the private disk', function () {
    filledConfigurator()
        ->set('newUploads', [UploadedFile::fake()->create('brif-čćšžđ.pdf', 200, 'application/pdf')])
        ->assertHasNoErrors()
        ->call('submit')
        ->assertHasNoErrors();

    $file = Order::sole()->files()->sole();
    expect($file->original_name)->toBe('brif-čćšžđ.pdf');
    Storage::disk('orders')->assertExists($file->path);
});

it('saves per-page template choices and the selected project style', function () {
    $project = Project::factory()->create(['slug' => 'oriental']);
    $template = $project->pageTemplates()->create(['page_type' => 'about', 'name' => 'O nama']);

    filledConfigurator(['style' => 'oriental', 'pageStyles' => ['about' => (string) $template->id, 'services' => '']])
        ->call('submit')
        ->assertHasNoErrors();

    expect(Order::sole()->config)->toMatchArray(['style' => 'oriental', 'page_styles' => ['about' => $template->id]]);
});

it('preselects the style from ?style= and keeps it over a restored draft', function () {
    Project::factory()->create(['slug' => 'anza']);

    Livewire::test(Configurator::class, ['style' => 'anza'])
        ->assertSet('form.style', 'anza')
        ->call('restore', ['style' => 'trust', 'hero' => 'video'], 1)
        ->assertSet('form.style', 'anza')
        ->assertSet('form.hero', 'video');
});

it('ignores an unknown style in the query string', function () {
    Livewire::test(Configurator::class, ['style' => 'no-such-project'])->assertSet('form.style', 'trust');
});

describe('validation', function () {
    it('requires a site type before moving on', function () {
        Livewire::test(Configurator::class)
            ->call('next')
            ->assertHasErrors(['form.type' => 'required'])
            ->assertSet('step', 1);
    });

    it('allows at most 7 pages including the home page', function () {
        $eight = ['home', 'about', 'services', 'catalog', 'gallery', 'team', 'reviews', 'blog'];

        filledConfigurator(['pages' => $eight])->call('submit')
            ->assertHasErrors(['form.pages' => 'max'])
            ->assertSet('step', 1);

        expect(Order::count())->toBe(0);
    });

    it('accepts exactly 7 pages', function () {
        filledConfigurator(['pages' => ['home', 'about', 'services', 'catalog', 'gallery', 'team', 'contacts']])
            ->call('submit')->assertHasNoErrors();
    });

    it('always keeps the home page', function () {
        filledConfigurator(['pages' => ['about']])->call('submit')->assertHasErrors('form.pages');
    });

    it('needs a name for a custom page', function () {
        filledConfigurator(['pages' => ['home', 'custom']])->call('submit')->assertHasErrors(['form.customPage' => 'required']);
        filledConfigurator(['pages' => ['home', 'custom'], 'customPage' => 'Akcije'])->call('submit')->assertHasNoErrors();
    });

    it('requires the second language when two are chosen', function () {
        filledConfigurator(['langs' => 'two'])->call('submit')->assertHasErrors(['form.secondLang' => 'required_if']);
    });

    it('requires a phone or an email', function () {
        filledConfigurator(['phone' => '', 'email' => ''])->call('submit')
            ->assertHasErrors(['form.phone' => 'required_without', 'form.email' => 'required_without'])
            ->assertSet('step', 4);
    });

    it('accepts only a phone', function () {
        filledConfigurator(['email' => ''])->call('submit')->assertHasNoErrors();
        expect(Order::sole()->email)->toBeNull();
    });

    it('accepts only an email', function () {
        filledConfigurator(['phone' => ''])->call('submit')->assertHasNoErrors();
    });

    it('validates the email format', function () {
        filledConfigurator(['email' => 'not-an-email'])->call('submit')->assertHasErrors(['form.email' => 'email']);
    });

    it('requires consent', function () {
        filledConfigurator(['consent' => false])->call('submit')->assertHasErrors(['form.consent' => 'accepted']);
        expect(Order::count())->toBe(0);
    });

    it('rejects disallowed file types', function () {
        filledConfigurator()
            ->set('newUploads', [UploadedFile::fake()->create('virus.exe', 10, 'application/x-msdownload')])
            ->assertHasErrors('newUploads.0');
    });

    it('rejects a file over 20 MB', function () {
        filledConfigurator()
            ->set('newUploads', [UploadedFile::fake()->create('big.pdf', 20 * 1024 + 1, 'application/pdf')])
            ->assertHasErrors('newUploads.0');
    });

    it('limits the total size of all files to 20 MB', function () {
        filledConfigurator()
            ->set('newUploads', [UploadedFile::fake()->create('a.pdf', 11 * 1024, 'application/pdf')])
            ->assertHasNoErrors()
            ->set('newUploads', [UploadedFile::fake()->create('b.pdf', 11 * 1024, 'application/pdf')])
            ->assertHasErrors('uploads')
            ->call('submit')
            ->assertHasErrors('uploads');

        expect(Order::count())->toBe(0);
    });

    it('accepts all listed file types', function (string $name, string $mime) {
        filledConfigurator()
            ->set('newUploads', [UploadedFile::fake()->create($name, 10, $mime)])
            ->assertHasNoErrors();
    })->with([
        ['brief.pdf', 'application/pdf'],
        ['brief.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        ['texts.txt', 'text/plain'],
        ['photo.jpg', 'image/jpeg'],
        ['logo.png', 'image/png'],
        ['logo.svg', 'image/svg+xml'],
        ['all.zip', 'application/zip'],
    ]);

    it('rejects a cloud link that is not a URL', function () {
        filledConfigurator(['cloudLink' => 'javascript:alert(1)'])->call('submit')->assertHasErrors('form.cloudLink');
    });
});

describe('navigation', function () {
    it('moves forward only through valid steps and back freely', function () {
        $c = Livewire::test(Configurator::class)->set('form.type', 'onepage')->call('next')
            ->assertSet('step', 2)->assertSet('maxStep', 2);

        $c->call('goTo', 4)->assertSet('step', 2);   // korak još nije otvoren
        $c->call('back')->assertSet('step', 1);
        $c->call('goTo', 2)->assertSet('step', 2);   // pređeni — može
    });

    it('restores a draft only up to the first invalid step', function () {
        Livewire::test(Configurator::class)
            ->call('restore', ['type' => 'multipage', 'pages' => ['about'], 'unknown' => 'x'], 4)
            ->assertSet('form.pages', ['home', 'about'])
            ->assertSet('step', 3); // koraci 1–2 su ispravni, korak 3 još nije popunjen
    });

    it('ignores values of the wrong type in a draft', function () {
        Livewire::test(Configurator::class)
            ->call('restore', ['pages' => 'home', 'seo' => 'yes', 'hero' => ['x']], 1)
            ->assertSet('form.pages', ['home'])
            ->assertSet('form.seo', false)
            ->assertSet('form.hero', 'slider');
    });
});

describe('anti-spam', function () {
    it('rejects submissions with the honeypot filled', function () {
        filledConfigurator()->set('website', 'https://spam.example')->call('submit')
            ->assertHasErrors('submit')
            ->assertSet('submitted', null);

        expect(Order::count())->toBe(0);
        Mail::assertNothingQueued();
    });

    it('rejects forms filled faster than the minimum time', function () {
        $component = Livewire::test(Configurator::class);
        foreach (validOrder() as $key => $value) {
            $component->set("form.$key", $value);
        }

        $component->call('submit')->assertHasErrors('submit')->assertSee(__('cfg.errors.too_fast'));
        expect(Order::count())->toBe(0);
    });

    it('rate limits orders per IP', function () {
        $limit = config('brzisajt.antispam.rate_limit_per_hour');

        foreach (range(1, $limit) as $i) {
            filledConfigurator()->call('submit')->assertHasNoErrors();
        }

        filledConfigurator()->call('submit')->assertHasErrors('submit')->assertSee(__('cfg.errors.rate_limited'));
        expect(Order::count())->toBe($limit);

        $this->travel(61)->minutes();
        filledConfigurator()->call('submit')->assertHasNoErrors();
    });

    it('verifies the captcha when it is enabled', function () {
        config(['brzisajt.antispam.captcha' => 'turnstile', 'brzisajt.antispam.turnstile.secret_key' => 'secret']);
        Http::fake(['challenges.cloudflare.com/*' => Http::sequence()
            ->push(['success' => false])
            ->push(['success' => true])]);

        filledConfigurator()->set('captchaToken', 'bad')->call('submit')->assertHasErrors('submit');
        expect(Order::count())->toBe(0);

        filledConfigurator()->set('captchaToken', 'token')->call('submit')->assertHasNoErrors();

        Http::assertSent(fn ($request) => $request->url() === 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
            && $request['secret'] === 'secret' && $request['response'] === 'token');
    });
});
