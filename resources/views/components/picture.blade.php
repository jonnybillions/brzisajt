@props([
    'path',
    'width' => null,
    'height' => null,
    'alt' => '',
    'sizes' => '100vw',
    'loading' => 'lazy',
    'disk' => 'public',
])
@php
    use App\Support\ImageVariants;
    use Illuminate\Support\Facades\Storage;
    $url = Storage::disk($disk)->url($path);
@endphp
@if ($path && $width && ! ImageVariants::isSvg($path))
    <picture>
        <source type="image/webp" srcset="{{ ImageVariants::srcset($disk, $path, $width, 'webp') }}" sizes="{{ $sizes }}">
        <img
            src="{{ Storage::disk($disk)->url(ImageVariants::variantPath($path, ImageVariants::widthsFor($width)[0], 'jpg')) }}"
            srcset="{{ ImageVariants::srcset($disk, $path, $width, 'jpg') }}"
            sizes="{{ $sizes }}"
            width="{{ $width }}"
            height="{{ $height }}"
            alt="{{ $alt }}"
            loading="{{ $loading }}"
            decoding="async"
            {{ $attributes }}
        >
    </picture>
@elseif ($path)
    <img src="{{ $url }}" alt="{{ $alt }}" loading="{{ $loading }}" decoding="async" @if ($width) width="{{ $width }}" height="{{ $height }}" @endif {{ $attributes }}>
@endif
