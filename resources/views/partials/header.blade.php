@php
    $home = request()->routeIs('home') ? '' : route('home');
    $nav = [
        'why' => __('site.nav.why'),
        'how' => __('site.nav.how'),
        'examples' => __('site.nav.examples'),
        'pricing' => __('site.nav.pricing'),
        'faq' => __('site.nav.faq'),
        'contacts' => __('site.nav.contacts'),
    ];
@endphp
<header
    x-data="siteHeader"
    @keydown.escape.window="close()"
    class="sticky top-0 z-50 py-3 transition-[background-color,box-shadow,padding] duration-300 lg:py-5"
    :class="(scrolled || open) && 'bg-cream/90 backdrop-blur-md shadow-[0_1px_0_0_var(--c-line)] !py-2'"
>
    <div class="container-x flex items-center justify-between gap-4">
        <a href="{{ $home ?: '#main' }}" class="relative z-10 shrink-0 rounded-lg" title="{{ __('site.a11y.home') }}">
            @include('partials.wordmark')
        </a>

        <nav aria-label="{{ __('site.a11y.main_nav') }}" class="hidden lg:block">
            <ul class="flex items-center gap-1 xl:gap-2">
                @foreach ($nav as $anchor => $label)
                    <li>
                        <a href="{{ $home }}#{{ $anchor }}" class="rounded-full px-3 py-2 text-[0.9375rem] font-semibold transition-colors hover:bg-ink/5">{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ $home }}#configurator" class="btn btn-primary hidden !min-h-11 !px-5 !py-2.5 text-[0.9375rem] sm:inline-flex" :class="scrolled && '!min-h-10'">{{ __('site.nav.cta') }}</a>

            <button
                type="button"
                class="relative z-10 grid size-11 place-items-center rounded-full bg-ink text-cream lg:hidden"
                @click="open = !open"
                :aria-expanded="open.toString()"
                aria-controls="mobile-nav"
                :aria-label="open ? @js(__('site.a11y.menu_close')) : @js(__('site.a11y.menu_open'))"
                aria-label="{{ __('site.a11y.menu_open') }}"
            >
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                    <path x-show="!open" d="M4 7h16M4 12h16M4 17h10" />
                    <path x-show="open" x-cloak d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>
    </div>

    <div
        id="mobile-nav"
        x-show="open"
        x-cloak
        x-transition.opacity.duration.200ms
        class="fixed inset-0 top-0 z-0 overflow-y-auto bg-cream pt-24 pb-10 lg:hidden"
    >
        <nav aria-label="{{ __('site.a11y.main_nav') }}" class="container-x">
            <ul class="flex flex-col gap-1">
                @foreach ($nav as $anchor => $label)
                    <li>
                        <a href="{{ $home }}#{{ $anchor }}" @click="close()" class="block rounded-2xl py-3 font-display text-[1.75rem] font-bold">{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
            <a href="{{ $home }}#configurator" @click="close()" class="btn btn-primary mt-8 w-full">{{ __('site.nav.cta') }}</a>
        </nav>
    </div>
</header>
