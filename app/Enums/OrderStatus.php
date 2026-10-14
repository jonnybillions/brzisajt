<?php

namespace App\Enums;

enum OrderStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case InProgress = 'in_progress';
    case Won = 'won';
    case Lost = 'lost';

    public function label(): string
    {
        return __("admin.status.{$this->value}");
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'info',
            self::Contacted => 'warning',
            self::InProgress => 'primary',
            self::Won => 'success',
            self::Lost => 'gray',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
