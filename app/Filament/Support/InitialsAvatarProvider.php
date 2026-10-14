<?php

namespace App\Filament\Support;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

/** Avatar sa inicijalom, napravljen lokalno (umesto spoljnog ui-avatars.com). */
class InitialsAvatarProvider implements AvatarProvider
{
    public function get(Model $record): string
    {
        $initial = mb_strtoupper(mb_substr(trim(Filament::getNameForDefaultAvatar($record)), 0, 1)) ?: '?';
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><rect width="64" height="64" fill="#6B3BFF"/>'
            .'<text x="50%" y="50%" dominant-baseline="central" text-anchor="middle" font-family="Arial,sans-serif" font-size="30" font-weight="700" fill="#fff">'
            .e($initial).'</text></svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
