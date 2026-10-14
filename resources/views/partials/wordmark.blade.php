@php($logoSvg = settings('logo_svg'))
@if ($logoSvg)
    <span class="inline-flex h-8 items-center [&>svg]:h-full [&>svg]:w-auto" aria-hidden="true">{!! $logoSvg !!}</span>
    <span class="sr-only">{{ __('site.brand.name') }}</span>
@else
    <span class="font-display text-[1.25rem] font-extrabold tracking-tight whitespace-nowrap {{ $class ?? '' }}"><span class="{{ $accent ?? 'text-violet' }}">{{ __('site.brand.wordmark_accent') }}</span>{{ __('site.brand.wordmark_rest') }}<span class="ms-1.5 align-middle font-sans text-[0.75rem] font-bold tracking-normal opacity-70">{{ __('site.brand.wordmark_by') }}</span></span>
@endif
