<?php

namespace App\Configurator;

use App\Models\Project;
use Closure;
use Illuminate\Validation\Rule;

/**
 * Pravila provere konfiguratora po koracima. Ključevi — sa prefiksom „form.“,
 * kao kod svojstva Livewire komponente; fajlovi — „uploads“.
 */
class OrderRules
{
    /** @return array<string, mixed> */
    public static function forStep(int $step, array $form): array
    {
        return match ($step) {
            1 => self::structure($form),
            2 => self::look(),
            3 => self::content(),
            4 => self::contacts(),
            default => [],
        };
    }

    /** @return array<string, mixed> */
    public static function all(array $form): array
    {
        return array_merge(...array_map(fn ($s) => self::forStep($s, $form), [1, 2, 3, 4]));
    }

    private static function structure(array $form): array
    {
        $hasCustom = in_array(Options::CUSTOM_PAGE, $form['pages'] ?? [], true);

        return [
            'form.type' => ['required', Rule::in(Options::TYPES)],
            'form.scroll' => ['nullable', 'required_if:form.type,onepage', Rule::in(Options::SCROLLS)],
            'form.pages' => [
                'required', 'array', 'max:'.Options::maxPages(),
                function (string $attribute, mixed $value, Closure $fail) {
                    if (! in_array('home', (array) $value, true)) {
                        $fail(__('cfg.pages.hint'));
                    }
                },
            ],
            'form.pages.*' => ['distinct', Rule::in(Options::pages())],
            'form.customPage' => [Rule::requiredIf($hasCustom), 'nullable', 'string', 'max:60'],
            'form.pageStyles' => ['array'],
            'form.pageStyles.*' => ['nullable', 'integer', 'exists:page_templates,id'],
        ];
    }

    private static function look(): array
    {
        return [
            'form.style' => ['required', 'string', function (string $attribute, mixed $value, Closure $fail) {
                if ($value !== Options::STYLE_TRUST && ! Project::published()->where('slug', $value)->exists()) {
                    $fail(__('validation.in', ['attribute' => __('validation.attributes.form.style')]));
                }
            }],
            'form.hero' => ['required', Rule::in(Options::HEROES)],
            'form.menuType' => ['required', Rule::in(Options::MENU_TYPES)],
            'form.menuPosition' => ['required', Rule::in(Options::MENU_POSITIONS)],
            'form.motion' => ['required', Rule::in(Options::MOTIONS)],
            'form.colorsAuto' => ['boolean'],
            'form.colors' => ['array', 'max:'.Options::MAX_COLORS],
            'form.colors.*' => ['string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ];
    }

    private static function content(): array
    {
        $limits = config('brzisajt.limits');

        return [
            'form.industry' => ['required', Rule::in(Options::industries())],
            'form.langs' => ['required', Rule::in(Options::LANGS)],
            'form.secondLang' => ['nullable', 'required_if:form.langs,two', 'string', 'max:60'],
            'form.features' => ['array'],
            'form.features.*' => ['distinct', Rule::in(Options::FEATURES)],
            'form.texts' => ['required', Rule::in(Options::TEXTS)],
            'form.images' => ['required', Rule::in(Options::IMAGES)],
            'form.logo' => ['required', Rule::in(Options::LOGOS)],
            'form.seo' => ['boolean'],
            'form.cloudLink' => ['nullable', 'url:http,https', 'max:2048'],
            'uploads' => ['array', 'max:20', function (string $attribute, mixed $value, Closure $fail) use ($limits) {
                $total = collect((array) $value)->sum(fn ($file) => method_exists($file, 'getSize') ? $file->getSize() : 0);
                if ($total > $limits['upload_max_kb'] * 1024) {
                    $fail(__('cfg.errors.files_total'));
                }
            }],
            'uploads.*' => ['file', 'max:'.$limits['upload_max_kb'], 'mimes:'.implode(',', $limits['upload_mimes'])],
        ];
    }

    private static function contacts(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:120'],
            'form.company' => ['nullable', 'string', 'max:160'],
            'form.phone' => ['nullable', 'required_without:form.email', 'string', 'max:40', 'regex:/^[\d\s()+\-.]{6,}$/'],
            'form.email' => ['nullable', 'required_without:form.phone', 'email', 'max:160'],
            'form.channel' => ['required', Rule::in(Options::CHANNELS)],
            'form.comment' => ['nullable', 'string', 'max:3000'],
            'form.consent' => ['accepted'],
        ];
    }
}
