<x-layouts.site>
    @push('head')
        @include('seo.jsonld')
    @endpush

    @include('sections.hero')
    @include('sections.clients')
    @include('sections.about')
    @include('sections.benefits')
    @include('sections.compare')
    @include('sections.how')
    @include('sections.examples')
    @include('sections.pricing')
    @include('sections.configurator')
    @include('sections.faq')
    @include('sections.final')
</x-layouts.site>
