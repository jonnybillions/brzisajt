<?php

namespace App\Support;

use GdImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Generiše umanjene kopije slike (WebP + JPEG) pored originala:
 * covers/foo.jpg → covers/foo-480.webp, covers/foo-480.jpg, … (GD nema AVIF).
 */
class ImageVariants
{
    public const WIDTHS = [480, 960, 1440];

    /** @return array{0: int, 1: int}|null širina i visina originala */
    public static function generate(string $disk, string $path): ?array
    {
        $storage = Storage::disk($disk);

        if (! $storage->exists($path) || self::isSvg($path)) {
            return null;
        }

        $source = @imagecreatefromstring($storage->get($path));

        if (! $source instanceof GdImage) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);

        foreach (self::widthsFor($width) as $target) {
            $resized = $target === $width ? $source : self::resize($source, $width, $height, $target);

            foreach (['webp', 'jpg'] as $format) {
                ob_start();
                $format === 'webp' ? imagewebp($resized, null, 80) : imagejpeg(self::flatten($resized), null, 82);
                $storage->put(self::variantPath($path, $target, $format), ob_get_clean(), 'public');
            }
        }

        return [$width, $height];
    }

    public static function delete(string $disk, ?string $path): void
    {
        if (! $path || self::isSvg($path)) {
            return;
        }

        $files = [];
        foreach (self::WIDTHS as $w) {
            $files[] = self::variantPath($path, $w, 'webp');
            $files[] = self::variantPath($path, $w, 'jpg');
        }
        Storage::disk($disk)->delete($files);
    }

    /** @return list<int> */
    public static function widthsFor(int $originalWidth): array
    {
        $widths = array_values(array_filter(self::WIDTHS, fn ($w) => $w <= $originalWidth));

        return $widths ?: [$originalWidth];
    }

    public static function variantPath(string $path, int $width, string $format): string
    {
        $dir = pathinfo($path, PATHINFO_DIRNAME);
        $name = pathinfo($path, PATHINFO_FILENAME);

        return ltrim(($dir === '.' ? '' : $dir.'/')."{$name}-{$width}.{$format}", '/');
    }

    public static function srcset(string $disk, string $path, int $originalWidth, string $format): string
    {
        return collect(self::widthsFor($originalWidth))
            ->map(fn ($w) => Storage::disk($disk)->url(self::variantPath($path, $w, $format))." {$w}w")
            ->implode(', ');
    }

    public static function isSvg(string $path): bool
    {
        return Str::lower(pathinfo($path, PATHINFO_EXTENSION)) === 'svg';
    }

    private static function resize(GdImage $src, int $w, int $h, int $targetW): GdImage
    {
        $targetH = (int) round($h * $targetW / $w);
        $dst = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetW, $targetH, $w, $h);

        return $dst;
    }

    /** JPEG nema providnost — podmećemo pozadinu sajta. */
    private static function flatten(GdImage $img): GdImage
    {
        $w = imagesx($img);
        $h = imagesy($img);
        $bg = imagecreatetruecolor($w, $h);
        imagefill($bg, 0, 0, imagecolorallocate($bg, 0xF6, 0xF4, 0xEF));
        imagecopy($bg, $img, 0, 0, 0, 0, $w, $h);

        return $bg;
    }
}
