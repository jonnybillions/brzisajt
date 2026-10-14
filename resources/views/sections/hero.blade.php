@php
    $price = settings()->int('base_price');
    [$priceBefore, $priceAfter] = array_pad(explode('%PRICE%', __('site.hero.title_3', ['price' => '%PRICE%']), 2), 2, '');
    $chipColors = ['bg-lime text-ink', 'bg-sky text-ink', 'bg-coral text-ink', 'bg-violet text-white'];
@endphp
<section id="top" class="relative isolate overflow-hidden pt-8 pb-20 lg:pt-14 lg:pb-28" aria-labelledby="hero-title">
    {{-- Jedini gradijent na sajtu: spori „mesh“ blob. --}}
    {{-- Blob se crta 4 puta manji i skalira: zamućenje je jeftinije. --}}
    <div aria-hidden="true" class="pointer-events-none absolute -top-40 right-[-30%] -z-10 size-[44rem] opacity-70 sm:right-[-12%] lg:-top-24 lg:right-[-6%]">
        <div class="size-[11rem] origin-top-left scale-400">
            <div class="size-full animate-blob rounded-[42%_58%_61%_39%/45%_39%_61%_55%] blur-[16px] will-change-transform"
                 style="background: radial-gradient(circle at 30% 30%, var(--c-violet) 0, transparent 55%), radial-gradient(circle at 70% 40%, var(--c-sky) 0, transparent 50%), radial-gradient(circle at 45% 75%, var(--c-coral) 0, transparent 50%), radial-gradient(circle at 80% 80%, var(--c-lime) 0, transparent 45%);"></div>
        </div>
    </div>

    <div class="container-x">
        <p class="chip border-2 border-ink bg-paper text-[0.8125rem] eyebrow">
            <span class="size-2 rounded-full bg-coral" aria-hidden="true"></span>
            {{ __('site.hero.eyebrow') }}
        </p>

        <div class="relative mt-7">
            <h1 id="hero-title" class="max-w-[14ch] text-[clamp(2.15rem,7.4vw,5.9rem)] font-extrabold leading-[1.02]">
                <span class="block">{{ tx('site.hero.title_1') }}</span>
                <span class="mt-2 inline-block -rotate-1 rounded-[0.35em] bg-lime px-[0.25em] py-[0.04em] text-ink">{{ tx('site.hero.title_2') }}</span>
                <span class="mt-2 block">{{ typo($priceBefore) }}<span
                        x-data="countUp({{ $price }})"
                        class="text-violet tabular-nums"
                    ><span class="relative inline-block"><span class="invisible">{{ \App\Support\Typo::money($price, false) }}</span><span class="absolute inset-0 text-right" x-text="formatted">{{ \App\Support\Typo::money($price, false) }}</span></span>{{ "\u{00A0}" }}€</span>{{ $priceAfter }}</span>
            </h1>

            <p class="sticker absolute right-[4%] bottom-0 hidden size-48 rounded-full bg-coral p-6 text-center text-[1.05rem] normal-case text-ink animate-wiggle lg:grid" style="--r: 12deg">
                {{ __('site.hero.badge') }}
            </p>
        </div>

        <p class="mt-8 max-w-2xl text-[1.0625rem] leading-relaxed text-muted lg:text-[1.25rem]">
            {{ typo(__('site.hero.subtitle')) }}
        </p>

        <ul class="mt-8 flex flex-wrap gap-2.5" aria-label="{{ __('site.hero.eyebrow') }}">
            @foreach (__('site.hero.chips') as $i => $chip)
                <li class="chip {{ $chipColors[$i % 4] }}">{{ typo($chip) }}</li>
            @endforeach
        </ul>

        <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:items-center">
            <a href="#configurator" class="btn btn-primary text-[1.0625rem]">
                {{ __('site.hero.cta_primary') }}
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
            <a href="#examples" class="btn btn-ghost">{{ __('site.hero.cta_secondary') }}</a>
            <span class="sticker mt-4 w-max rounded-2xl bg-coral px-4 py-3 text-[0.95rem] text-ink lg:hidden" style="--r: -4deg">{{ __('site.hero.badge') }}</span>
        </div>

        <p class="mt-6 flex max-w-xl items-start gap-2 text-[0.9375rem] text-muted">
            <svg class="mt-0.5 size-5 shrink-0 text-violet" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01" stroke-linecap="round"/></svg>
            <span>{{ typo(__('site.hero.note')) }}</span>
        </p>
    </div>
</section>
