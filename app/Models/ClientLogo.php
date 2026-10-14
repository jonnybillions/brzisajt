<?php

namespace App\Models;

use App\Support\ImageVariants;
use App\Support\SvgSanitizer;
use Database\Factories\ClientLogoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'logo', 'sort', 'is_published'])]
class ClientLogo extends Model
{
    /** @use HasFactory<ClientLogoFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        // Otpremljene SVG fajlove čistimo od skripti: fajl je na javnom disku.
        static::saving(function (self $logo) {
            if ($logo->isDirty('logo') && $logo->logo && ImageVariants::isSvg($logo->logo)) {
                $disk = Storage::disk('public');
                $clean = SvgSanitizer::clean($disk->get($logo->logo));
                $clean ? $disk->put($logo->logo, $clean) : $disk->delete($logo->logo);
                $logo->logo = $clean ? $logo->logo : null;
            }

            if ($logo->isDirty('logo')) {
                [$logo->logo_width, $logo->logo_height] = $logo->logo ? self::dimensions($logo->logo) : [null, null];
            }
        });
    }

    /** Dimenzije logotipa — za width/height u markupu (bez pomeranja sadržaja). */
    private static function dimensions(string $path): array
    {
        $disk = Storage::disk('public');
        if (! $disk->exists($path)) {
            return [null, null];
        }

        if (ImageVariants::isSvg($path)) {
            $svg = $disk->get($path);
            if (preg_match('/viewBox="\s*[-\d.]+[\s,]+[-\d.]+[\s,]+([\d.]+)[\s,]+([\d.]+)\s*"/i', $svg, $m)) {
                return [(int) round((float) $m[1]), (int) round((float) $m[2])];
            }

            return [null, null];
        }

        $size = @getimagesize($disk->path($path));

        return $size ? [$size[0], $size[1]] : [null, null];
    }

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('sort')->orderBy('id');
    }
}
