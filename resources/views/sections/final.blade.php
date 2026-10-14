@php
    $phone = settings('phone');
    $email = settings('email');
    $viber = viber_href(settings('viber'));
    $whatsapp = whatsapp_href(settings('whatsapp'));
@endphp
<section id="contacts" class="section" aria-labelledby="final-title">
    <div class="container-x">
        <div class="card relative overflow-hidden bg-lime px-6 py-14 text-ink sm:px-10 lg:px-16 lg:py-20" x-reveal>
            <span aria-hidden="true" class="pointer-events-none absolute -right-10 -bottom-16 select-none font-display text-[14rem] font-extrabold leading-none text-ink/[0.06] lg:text-[22rem]">7</span>

            <h2 id="final-title" class="relative max-w-3xl text-[clamp(2.1rem,6vw,4.5rem)] font-extrabold">{{ tx('site.final.title') }}</h2>
            <a href="#configurator" class="btn btn-primary relative mt-10 text-[1.0625rem]">
                {{ __('site.final.cta') }}
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
        </div>

        <div class="mt-16 grid gap-8 lg:mt-20 lg:grid-cols-12">
            <h2 class="h-section lg:col-span-4">{{ __('site.contact.title') }}</h2>

            <address class="grid gap-6 not-italic sm:grid-cols-2 lg:col-span-8">
                <div>
                    <p class="eyebrow text-muted">{{ __('site.contact.email_label') }}</p>
                    <a href="mailto:{{ $email }}" class="mt-2 inline-block font-display text-[clamp(1.25rem,2.6vw,1.9rem)] font-bold break-all underline decoration-violet decoration-[3px] underline-offset-[6px] hover:text-violet">{{ $email }}</a>
                </div>
                <div>
                    <p class="eyebrow text-muted">{{ __('site.contact.or_call') }}</p>
                    <a href="{{ tel_href($phone) }}" class="mt-2 inline-block font-display text-[clamp(1.25rem,2.6vw,1.9rem)] font-bold whitespace-nowrap underline decoration-coral decoration-[3px] underline-offset-[6px] hover:text-violet">{{ $phone }}</a>
                </div>

                @if ($viber || $whatsapp)
                    <div class="flex flex-wrap gap-2 sm:col-span-2">
                        @if ($viber)
                            <a href="{{ $viber }}" class="chip bg-violet text-white">Viber</a>
                        @endif
                        @if ($whatsapp)
                            <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" class="chip bg-lime text-ink ring-2 ring-ink">WhatsApp <span class="sr-only">({{ __('site.a11y.external') }})</span></a>
                        @endif
                    </div>
                @endif
            </address>
        </div>
    </div>
</section>
