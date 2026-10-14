<footer class="on-dark border-t border-white/10 py-8">
    <div class="container-x flex flex-col items-start justify-between gap-4 text-[0.9375rem] text-muted-dark sm:flex-row sm:items-center">
        <div class="flex items-center gap-4">
            @include('partials.wordmark', ['accent' => 'text-lime', 'class' => 'text-cream'])
        </div>
        <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
            <a href="{{ route('privacy') }}" class="underline decoration-white/30 underline-offset-4 hover:text-cream hover:decoration-lime">{{ __('site.footer.privacy') }}</a>
            @if (\App\Support\Analytics::needsConsent())
                <button type="button" x-data @click="$dispatch('consent-open')" class="underline decoration-white/30 underline-offset-4 hover:text-cream hover:decoration-lime">{{ __('site.footer.cookies') }}</button>
            @endif
            <span>{{ __('site.footer.copy') }}</span>
        </div>
    </div>
</footer>
