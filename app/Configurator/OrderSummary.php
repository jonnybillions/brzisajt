<?php

namespace App\Configurator;

use App\Models\Order;
use App\Models\PageTemplate;
use App\Models\Project;
use App\Support\Typo;

/**
 * Pretvara JSON sa odgovorima iz konfiguratora u pregledan spisak
 * „pitanje — odgovor“ za emailove, admin panel i Telegram.
 */
class OrderSummary
{
    /** @return list<array{label: string, value: string}> */
    public static function lines(array $config): array
    {
        $opt = fn (string $group, ?string $key) => $key === null ? null : __("cfg.$group.options.$key");

        $pages = collect($config['pages'] ?? [])->map(function (string $page) use ($config) {
            $label = $page === Options::CUSTOM_PAGE
                ? ($config['custom_page'] ?: __('cfg.pages.options.custom'))
                : __("cfg.pages.options.$page");

            $templateId = $config['page_styles'][$page] ?? null;
            if ($templateId && ($template = PageTemplate::with('project')->find($templateId))) {
                $label .= ' ('.__('cfg.pages.style_from', ['project' => $template->project->name]).')';
            }

            return $label;
        });

        $style = ($config['style'] ?? Options::STYLE_TRUST) === Options::STYLE_TRUST
            ? __('cfg.style.trust')
            : __('cfg.pages.style_from', ['project' => Project::where('slug', $config['style'])->value('name') ?? $config['style']]);

        $motion = isset($config['motion']) ? __("cfg.motion.options.{$config['motion']}.title") : null;

        $features = collect(['form', ...($config['features'] ?? [])])->map(fn ($f) => __("cfg.features.options.$f"));

        $langs = ($config['langs'] ?? 'one') === 'two'
            ? preg_replace('/\s*\(.*\)/u', '', __('cfg.langs.options.two')).': '.($config['second_lang'] ?: '—')
            : __('cfg.langs.options.one');

        $lines = [
            [__('cfg.sum.type'), isset($config['type']) ? __("cfg.type.{$config['type']}") : null],
            [__('cfg.scroll.q'), $opt('scroll', $config['scroll'] ?? null)],
            [__('cfg.sum.pages'), $pages->implode(', ')],
            [__('cfg.sum.style'), $style],
            [__('cfg.sum.hero'), $opt('hero', $config['hero'] ?? null)],
            [__('cfg.sum.menu'), trim(($opt('menu.type', $config['menu_type'] ?? null) ?? '').', '.mb_strtolower($opt('menu.position', $config['menu_position'] ?? null) ?? ''), ', ')],
            [__('cfg.sum.motion'), $motion],
            [__('cfg.sum.colors'), ($config['colors'] ?? []) ? implode(', ', $config['colors']) : __('cfg.colors.auto')],
            [__('cfg.sum.industry'), $opt('industry', $config['industry'] ?? null)],
            [__('cfg.sum.langs'), $langs],
            [__('cfg.sum.features'), $features->implode(', ')],
            [__('cfg.texts.q'), $opt('texts', $config['texts'] ?? null)],
            [__('cfg.images.q'), $opt('images', $config['images'] ?? null)],
            [__('cfg.logo.q'), isset($config['logo']) ? __("cfg.logo.options.{$config['logo']}", ['price' => Typo::money(settings()->int('extra_logo_price'))]) : null],
            [__('admin.order.seo'), ! empty($config['seo']) ? __('admin.yes') : __('admin.no')],
        ];

        return collect($lines)
            ->filter(fn ($l) => filled($l[1]))
            ->map(fn ($l) => ['label' => $l[0], 'value' => $l[1]])
            ->values()
            ->all();
    }

    /** Red sa cenom: „600 € + logo od 200 € + SEO od 400 €/mes.“. */
    public static function price(array $extras): string
    {
        $parts = [Typo::money(settings()->int('base_price'))];

        if (in_array('logo', $extras, true)) {
            $parts[] = __('cfg.sum.extra_logo', ['price' => Typo::money(settings()->int('extra_logo_price'))]);
        }
        if (in_array('seo', $extras, true)) {
            $parts[] = __('cfg.sum.extra_seo', ['price' => Typo::money(settings()->int('extra_seo_price'))]);
        }

        return implode(' + ', $parts);
    }

    /** @return list<array{label: string, value: string}> */
    public static function contacts(Order $order): array
    {
        return collect([
            [__('cfg.contact.name'), $order->name],
            [__('cfg.contact.company'), $order->company],
            [__('cfg.contact.phone'), $order->phone],
            [__('cfg.contact.email'), $order->email],
            [__('cfg.channel.q'), $order->channel ? __("cfg.channel.options.{$order->channel}") : null],
            [__('cfg.comment.label'), $order->comment],
            [__('cfg.files.link'), $order->cloud_link],
        ])->filter(fn ($l) => filled($l[1]))->map(fn ($l) => ['label' => $l[0], 'value' => $l[1]])->values()->all();
    }
}
