@php($analytics = \App\Support\Analytics::driver())
@if ($analytics === 'plausible')
    <script defer data-domain="{{ settings('plausible_domain') }}" src="{{ settings('plausible_script') }}"></script>
@endif
