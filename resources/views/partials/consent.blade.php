{{-- Saglasnost za kolačiće — prikazuje se samo kada je uključen GA4. --}}
@if (\App\Support\Analytics::needsConsent())
    <div
        x-data="cookieConsent(@js(\App\Support\Analytics::ga4Id()))"
        @consent-open.window="open = true"
        x-show="open"
        x-cloak
        x-transition.opacity
        role="dialog"
        aria-live="polite"
        aria-labelledby="consent-text"
        class="fixed inset-x-3 bottom-3 z-[60] max-w-md rounded-card border-2 border-ink bg-paper p-5 shadow-[0_6px_0_0_var(--c-ink)] sm:inset-x-auto sm:left-4 sm:bottom-4"
    >
        <p id="consent-text" class="text-[0.9375rem] leading-snug">
            {{ typo(__('site.consent.text')) }}
            <a href="{{ route('privacy') }}" class="font-bold text-violet underline underline-offset-2">{{ __('site.consent.more') }}</a>
        </p>
        <div class="mt-4 flex flex-wrap gap-2">
            <button type="button" class="btn btn-primary !min-h-11 !px-5 !py-2" @click="accept()">{{ __('site.consent.accept') }}</button>
            <button type="button" class="btn btn-ghost !min-h-11 !px-5 !py-2" @click="decline()">{{ __('site.consent.decline') }}</button>
        </div>
    </div>
@endif
