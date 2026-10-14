<?php

namespace App\Enums;

/** Tipovi stranica: za šablone stranica i za izbor u konfiguratoru. */
enum PageType: string
{
    case Home = 'home';
    case About = 'about';
    case Services = 'services';
    case Catalog = 'catalog';
    case Gallery = 'gallery';
    case Team = 'team';
    case Reviews = 'reviews';
    case Blog = 'blog';
    case Contacts = 'contacts';

    public function label(): string
    {
        return __("cfg.pages.options.{$this->value}");
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
