<?php

namespace Database\Seeders;

use App\Models\ClientLogo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Logotipi klijenata. Dok nema pravih fajlova — generišemo
 * tekstualne SVG zamene; u admin panelu mogu da se zamene.
 */
class ClientLogoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (__('site.clients.logos') as $sort => $name) {
            $path = 'logos/'.Str::slug($name, '-', 'sr').'.svg';
            Storage::disk('public')->put($path, $this->placeholderSvg($name));

            ClientLogo::updateOrCreate(['name' => $name], [
                'logo' => $path,
                'sort' => $sort,
                'is_published' => true,
            ]);
        }
    }

    private function placeholderSvg(string $name): string
    {
        $size = 28;
        $width = (int) ceil(mb_strlen($name) * $size * 0.62) + 8;
        $text = e($name);

        return <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$width} 48" width="{$width}" height="48" role="img" aria-label="{$text}">
            <text x="50%" y="50%" dominant-baseline="central" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="{$size}" font-weight="700" fill="#121016">{$text}</text>
        </svg>
        SVG;
    }
}
