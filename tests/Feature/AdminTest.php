<?php

use App\Enums\OrderStatus;
use App\Filament\Pages\Settings;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Models\ClientLogo;
use App\Models\Order;
use App\Models\PageTemplate;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function admin(): User
{
    $user = User::factory()->create();
    $user->forceFill(['is_admin' => true])->save();

    return $user;
}

function orderWithFile(): Order
{
    Storage::fake('orders');
    $order = Order::unguarded(fn () => Order::create([
        'name' => 'Анна', 'email' => 'anna@example.com', 'consent_at' => now(),
        'config' => ['type' => 'onepage', 'scroll' => 'slides', 'pages' => ['home', 'contacts'], 'hero' => 'video', 'motion' => 'expressive'],
        'extras' => ['seo'],
    ]));
    Storage::disk('orders')->put("{$order->id}/secret.pdf", 'PDF');
    $order->files()->create(['path' => "{$order->id}/secret.pdf", 'original_name' => 'Бриф.pdf', 'mime' => 'application/pdf', 'size' => 3]);

    return $order;
}

it('redirects guests to the admin login', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
    $this->get('/admin/orders')->assertRedirect('/admin/login');
});

it('forbids the panel for non-admin users', function () {
    $this->actingAs(User::factory()->create())->get('/admin/orders')->assertForbidden();
});

it('has no public registration', function () {
    $this->get('/admin/register')->assertNotFound();
});

it('lets admins see orders and a readable order view', function () {
    $order = orderWithFile();
    $this->actingAs(admin());

    $this->get('/admin/orders')->assertOk()->assertSee('BS-0001');
    $this->get("/admin/orders/{$order->id}")
        ->assertOk()
        ->assertSee('Одностраничный')
        ->assertSee('Полноэкранные слайды')
        ->assertSee('Главная, Контакты')
        ->assertSee('Бриф.pdf')
        ->assertDontSee('"scroll":"slides"', false);
});

it('shows the number of new orders in the navigation', function () {
    orderWithFile();
    Order::unguarded(fn () => Order::create(['name' => 'B', 'config' => [], 'consent_at' => now(), 'status' => 'won']));

    expect(OrderResource::getNavigationBadge())->toBe('1');
});

it('changes status and notes from the order page', function () {
    $order = orderWithFile();
    $this->actingAs(admin());

    Livewire::test(ViewOrder::class, ['record' => $order->getRouteKey()])
        ->callAction('status', ['status' => 'contacted'])
        ->callAction('notes', ['notes' => 'Перезвонить в пятницу']);

    expect($order->refresh())
        ->status->toBe(OrderStatus::Contacted)
        ->notes->toBe('Перезвонить в пятницу');
});

it('filters orders by status', function () {
    $new = orderWithFile();
    $won = Order::unguarded(fn () => Order::create(['name' => 'B', 'config' => [], 'consent_at' => now(), 'status' => 'won']));
    $this->actingAs(admin());

    Livewire::test(ListOrders::class)
        ->filterTable('status', ['won'])
        ->assertCanSeeTableRecords([$won])
        ->assertCanNotSeeTableRecords([$new]);
});

it('saves settings and applies them on the site', function () {
    $this->actingAs(admin());

    Livewire::test(Settings::class)
        ->fillForm(['base_price' => 70000, 'phone' => '+7 (900) 000-00-00', 'logo_svg' => '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script><rect width="1"/></svg>'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(settings()->int('base_price'))->toBe(70000)
        ->and(settings('logo_svg'))->not->toContain('script');

    $this->get('/')->assertSee("70\u{00A0}000", false)->assertSee('+7 (900) 000-00-00');
});

describe('order files', function () {
    it('can be downloaded by admins', function () {
        $file = orderWithFile()->files->first();

        $this->actingAs(admin())->get(route('admin.order-files.download', $file))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeaderContains('Content-Disposition', "filename*=utf-8''".rawurlencode('Бриф.pdf'));
    });

    it('cannot be downloaded by guests or non-admins', function () {
        $file = orderWithFile()->files->first();

        $this->get(route('admin.order-files.download', $file))->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create())->get(route('admin.order-files.download', $file))->assertForbidden();
    });

    it('are not stored on a public disk', function () {
        $file = orderWithFile()->files->first();

        expect(config('filesystems.disks.orders.root'))->not->toStartWith(storage_path('app/public'))
            ->and(config('filesystems.disks.orders.serve'))->toBeFalse();

        $this->get('/storage/'.$file->path)->assertNotFound();
        $this->get('/storage/orders/'.$file->path)->assertNotFound();
    });
});

it('renders every admin page', function (string $url) {
    $project = Project::factory()->create(['id' => 1]);
    PageTemplate::factory()->for($project)->create();
    ClientLogo::factory()->create();

    $this->actingAs(admin())->get($url)->assertOk();
})->with([
    '/admin',
    '/admin/projects',
    '/admin/projects/create',
    '/admin/projects/1/edit',
    '/admin/page-templates',
    '/admin/page-templates/create',
    '/admin/client-logos',
    '/admin/settings',
]);

it('sanitizes and measures uploaded SVG logos', function () {
    Storage::fake('public');
    Storage::disk('public')->put('logos/x.svg', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 40" onload="alert(1)"><script>alert(2)</script><rect width="120" height="40"/></svg>');

    $logo = ClientLogo::create(['name' => 'X', 'logo' => 'logos/x.svg']);

    expect(Storage::disk('public')->get('logos/x.svg'))->not->toContain('script')->not->toContain('onload')
        ->and($logo->logo_width)->toBe(120)
        ->and($logo->logo_height)->toBe(40);
});
