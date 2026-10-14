@php
    $cols = __('site.compare.columns');
    $rows = __('site.compare.rows');
    $price = money(settings()->int('base_price'));
    $cell = fn ($value) => typo(str_replace(':price', $price, $value));
@endphp
<section class="section" aria-labelledby="compare-title">
    <div class="container-x">
        <h2 id="compare-title" class="h-section max-w-3xl" x-reveal>{{ tx('site.compare.title') }}</h2>

        {{-- Desktop: tabela --}}
        <div class="mt-12 hidden md:block lg:mt-16" x-reveal>
            <table class="w-full table-fixed border-separate border-spacing-0 text-left">
                <caption class="sr-only">{{ __('site.compare.title') }}</caption>
                <colgroup><col class="w-[22%]"><col><col class="w-[28%]"><col></colgroup>
                <thead>
                    <tr>
                        <td class="p-0"></td>
                        <th scope="col" class="px-5 pb-5 align-bottom text-[1rem] font-bold text-muted">{{ $cols['builder'] }}</th>
                        <th scope="col" class="rounded-t-[1.75rem] bg-violet px-6 pt-7 pb-5 align-bottom font-display text-[1.5rem] font-extrabold text-white">{{ $cols['brzi'] }}</th>
                        <th scope="col" class="px-5 pb-5 align-bottom text-[1rem] font-bold text-muted">{{ $cols['custom'] }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $r)
                        <tr>
                            <th scope="row" class="border-t border-line py-5 pe-4 align-top text-[0.9375rem] font-bold uppercase tracking-wide">{{ $r['label'] }}</th>
                            <td class="border-t border-line px-5 py-5 align-top text-muted">{{ $cell($r['builder']) }}</td>
                            <td class="border-t border-white/20 bg-violet px-6 py-5 align-top text-[1.0625rem] font-bold text-white @if ($loop->last) rounded-b-[1.75rem] pb-7 @endif">{{ $cell($r['brzi']) }}</td>
                            <td class="border-t border-line px-5 py-5 align-top text-muted">{{ $cell($r['custom']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobilni: kartice sa prevlačenjem --}}
        <div class="mt-10 md:hidden">
            <p class="mb-3 text-[0.875rem] font-semibold text-muted" aria-hidden="true">{{ __('site.compare.swipe_hint') }} →</p>
            <ul class="-mx-4 flex snap-x snap-mandatory gap-3 overflow-x-auto px-4 pb-4 [scrollbar-width:none]" tabindex="0" aria-label="{{ __('site.compare.title') }}">
                @foreach (['brzi', 'builder', 'custom'] as $key)
                    @php($hl = $key === 'brzi')
                    <li class="card w-[85%] shrink-0 snap-center {{ $hl ? 'bg-violet text-white' : 'border-2 border-line bg-paper' }}">
                        <h3 class="{{ $hl ? 'font-display text-[1.5rem] font-extrabold' : 'text-[1.125rem] font-bold' }}">{{ $cols[$key] }}</h3>
                        <dl class="mt-5 space-y-4">
                            @foreach ($rows as $r)
                                <div>
                                    <dt class="text-[0.75rem] font-bold uppercase tracking-wider {{ $hl ? 'text-white' : 'text-muted' }}">{{ $r['label'] }}</dt>
                                    <dd class="mt-0.5 {{ $hl ? 'font-bold' : '' }}">{{ $cell($r[$key]) }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
