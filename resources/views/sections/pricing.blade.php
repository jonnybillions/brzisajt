@php
    $price = settings()->int('base_price');
    $check = '<svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg>';
    $cross = '<svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>';
@endphp
<section id="pricing" class="on-dark grain section" aria-labelledby="pricing-title">
    <div class="container-x">
        <h2 id="pricing-title" class="h-section" x-reveal>{{ tx('site.pricing.title') }}</h2>

        <div class="mt-12 grid gap-5 lg:mt-16 lg:grid-cols-12">
            {{-- Velika kartica sa cenom --}}
            <div class="card relative overflow-hidden bg-violet text-white lg:col-span-7" x-reveal>
                <p class="font-display font-extrabold leading-none tracking-tight" x-data="countUp({{ $price }})">
                    <span class="relative inline-block text-[clamp(3.25rem,10vw,7.5rem)] tabular-nums"><span class="invisible">{{ \App\Support\Typo::money($price, false) }}</span><span class="absolute inset-0 text-right" x-text="formatted">{{ \App\Support\Typo::money($price, false) }}</span></span><span class="text-[clamp(2.25rem,6vw,4.5rem)]">{{ "\u{00A0}" }}€</span>
                </p>
                <p class="mt-3 text-[1.125rem] font-bold text-white/85">— {{ __('site.pricing.price_label') }}</p>

                <h3 class="mt-10 text-[1.25rem] font-bold">{{ __('site.pricing.included_title') }}</h3>
                <ul class="mt-5 grid gap-3 sm:grid-cols-2">
                    @foreach (__('site.pricing.included') as $item)
                        <li class="flex gap-3 text-[1rem] leading-snug"><span class="text-lime">{!! $check !!}</span>{{ typo($item) }}</li>
                    @endforeach
                </ul>

                <a href="#configurator" class="btn btn-lime mt-10 w-full sm:w-auto">{{ __('site.pricing.cta') }}</a>
            </div>

            <div class="grid gap-5 lg:col-span-5">
                <div class="card bg-ink-2 ring-1 ring-white/10" x-reveal="80">
                    <h3 class="text-[1.25rem] font-bold">{{ __('site.pricing.excluded_title') }}</h3>
                    <ul class="mt-5 space-y-3">
                        @foreach (__('site.pricing.excluded') as $item)
                            <li class="flex gap-3 leading-snug text-muted-dark"><span class="text-coral">{!! $cross !!}</span>{{ typo($item) }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                    <div class="card bg-lime text-ink" x-reveal="140">
                        <h3 class="text-[1.125rem] font-bold">{{ __('site.pricing.payment_title') }}</h3>
                        <p class="mt-3 leading-snug">{{ typo(__('site.pricing.payment')) }}</p>
                    </div>
                    <div class="card bg-sky text-ink" x-reveal="200">
                        <h3 class="text-[1.125rem] font-bold">{{ __('site.pricing.warranty_title') }}</h3>
                        <p class="mt-3 leading-snug">{{ typo(__('site.pricing.warranty')) }}</p>
                    </div>
                </div>

                <div class="card bg-paper text-ink" x-reveal="260">
                    <h3 class="text-[1.125rem] font-bold">{{ __('site.pricing.extras_title') }}</h3>
                    <ul class="mt-4 space-y-2">
                        <li class="flex items-center gap-2"><span class="size-2 rounded-full bg-coral" aria-hidden="true"></span>{{ tx('site.pricing.extras.logo', ['price' => money(settings()->int('extra_logo_price'))]) }}</li>
                        <li class="flex items-center gap-2"><span class="size-2 rounded-full bg-violet" aria-hidden="true"></span>{{ tx('site.pricing.extras.seo', ['price' => money(settings()->int('extra_seo_price'))]) }}</li>
                    </ul>
                    <p class="mt-6 border-t border-line pt-5 leading-snug text-muted">{{ typo(__('site.pricing.custom')) }}</p>
                    <a href="mailto:{{ settings('email') }}" class="mt-3 inline-flex items-center gap-1.5 font-bold text-violet underline decoration-2 underline-offset-4 hover:decoration-coral">{{ __('site.pricing.custom_cta') }} →</a>
                </div>
            </div>
        </div>
    </div>
</section>
