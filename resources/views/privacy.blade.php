<x-layouts.site :title="__('site.meta.privacy_title')" :description="tx('site.meta.description')">
    <article class="section">
        <div class="container-x max-w-3xl">
            <h1 class="h-section">{{ tx('site.privacy.title') }}</h1>
            <div class="prose-legal mt-10 text-[1.0625rem]">
                {{-- [TODO] Tekst politike (Zakon o zaštiti podataka o ličnosti) biće dostavljen. --}}
                <p class="rounded-card border-2 border-dashed border-coral bg-paper p-6 font-semibold">{{ __('site.privacy.todo') }}</p>
            </div>
            <a href="{{ route('home') }}" class="btn btn-ghost mt-10">← {{ __('site.privacy.back') }}</a>
        </div>
    </article>
</x-layouts.site>
