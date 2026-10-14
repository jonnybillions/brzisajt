<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Support\Settings as SiteSettings;
use App\Support\SvgSanitizer;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Cene, kontakt, primaoci upita, analitika i SVG logotip.
 * Prazno polje — podrazumevana vrednost iz config/brzisajt.php.
 */
class Settings extends Page
{
    protected string $view = 'filament.pages.settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 10;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('admin.settings.title');
    }

    public function getTitle(): string
    {
        return __('admin.settings.title');
    }

    public function mount(): void
    {
        $overrides = Setting::pluck('value', 'key')->all();
        $this->form->fill(array_intersect_key($overrides, config('brzisajt.settings')));
    }

    public function form(Schema $schema): Schema
    {
        $default = fn (string $key) => (string) config("brzisajt.settings.$key");

        return $schema
            ->statePath('data')
            ->components([
                Section::make(__('admin.settings.prices'))->description(__('admin.settings.empty_hint'))->columns(3)->schema([
                    TextInput::make('base_price')->label(__('admin.settings.base_price'))->integer()->minValue(0)->placeholder($default('base_price')),
                    TextInput::make('extra_logo_price')->label(__('admin.settings.extra_logo_price'))->integer()->minValue(0)->placeholder($default('extra_logo_price')),
                    TextInput::make('extra_seo_price')->label(__('admin.settings.extra_seo_price'))->integer()->minValue(0)->placeholder($default('extra_seo_price')),
                ]),
                Section::make(__('admin.settings.contacts'))->columns(2)->schema([
                    TextInput::make('phone')->label(__('admin.settings.phone'))->tel()->telRegex('/^[\d\s()+\-.]{6,}$/')->maxLength(40)->placeholder($default('phone')),
                    TextInput::make('email')->label(__('admin.settings.email'))->email()->maxLength(160)->placeholder($default('email')),
                    TextInput::make('viber')->label(__('admin.settings.viber'))->tel()->telRegex('/^[\d\s()+\-.]{6,}$/')->maxLength(40)->placeholder($default('viber')),
                    TextInput::make('whatsapp')->label(__('admin.settings.whatsapp'))->tel()->telRegex('/^[\d\s()+\-.]{6,}$/')->maxLength(40)->placeholder($default('whatsapp')),
                ]),
                Section::make(__('admin.settings.notifications'))->schema([
                    TextInput::make('notify_emails')->label(__('admin.settings.notify_emails'))
                        ->helperText(__('admin.settings.notify_emails_help'))->maxLength(500)->placeholder($default('notify_emails'))
                        ->rule(fn () => function (string $attribute, $value, $fail) {
                            foreach (array_filter(array_map('trim', explode(',', (string) $value))) as $email) {
                                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    $fail(__('validation.email'));
                                }
                            }
                        }),
                ]),
                Section::make(__('admin.settings.analytics'))->description(__('admin.settings.analytics_help'))->columns(2)->schema([
                    Select::make('analytics_driver')->label(__('admin.settings.analytics_driver'))
                        ->options(['none' => __('admin.settings.analytics_none'), 'plausible' => 'Plausible', 'ga4' => 'Google Analytics 4'])
                        ->placeholder(__('admin.settings.analytics_none')),
                    TextInput::make('ga4_id')->label(__('admin.settings.ga4_id'))->regex('/^G-[A-Z0-9]{4,20}$/')->maxLength(24)
                        ->placeholder($default('ga4_id')),
                    TextInput::make('plausible_domain')->label(__('admin.settings.plausible_domain'))->maxLength(160)
                        ->placeholder($default('plausible_domain')),
                    TextInput::make('plausible_script')->label(__('admin.settings.plausible_script'))->url()->maxLength(255)
                        ->placeholder($default('plausible_script')),
                ]),
                Section::make(__('admin.settings.branding'))->schema([
                    Textarea::make('logo_svg')->label(__('admin.settings.logo_svg'))->helperText(__('admin.settings.logo_svg_help'))
                        ->rows(6)->maxLength(100_000)->extraInputAttributes(['style' => 'font-family:monospace;font-size:12px']),
                ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $data['logo_svg'] = SvgSanitizer::clean($data['logo_svg'] ?? null);

        $values = collect(array_keys(config('brzisajt.settings')))
            ->mapWithKeys(fn (string $key) => [$key => filled($data[$key] ?? null) ? (string) $data[$key] : null])
            ->all();

        app(SiteSettings::class)->put($values);
        $this->form->fill(array_filter($values, fn ($v) => $v !== null));

        Notification::make()->title(__('admin.settings.saved'))->success()->send();
    }
}
