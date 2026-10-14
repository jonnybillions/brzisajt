@php
    // Bento mreža: veličina, boja i ikonica za svaku od 6 kartica.
    $layout = [
        ['span' => 'lg:col-span-4 lg:row-span-2', 'color' => 'bg-violet text-white', 'icon' => 'code', 'big' => true],
        ['span' => 'lg:col-span-2', 'color' => 'bg-lime text-ink', 'icon' => 'admin'],
        ['span' => 'lg:col-span-2', 'color' => 'bg-sky text-ink', 'icon' => 'screens'],
        ['span' => 'lg:col-span-2', 'color' => 'bg-coral text-ink', 'icon' => 'key'],
        ['span' => 'lg:col-span-2', 'color' => 'bg-paper text-ink', 'icon' => 'bolt'],
        ['span' => 'lg:col-span-2', 'color' => 'bg-ink-2 text-cream ring-1 ring-white/10', 'icon' => 'team'],
    ];
    $icons = [
        'code' => '<path d="M8 6 2 12l6 6M16 6l6 6-6 6M14 4l-4 16"/>',
        'admin' => '<rect x="3" y="4" width="18" height="16" rx="3"/><path d="M3 9h18M8 14h3M8 17h6"/>',
        'screens' => '<rect x="2" y="5" width="14" height="10" rx="2"/><rect x="17" y="8" width="5" height="11" rx="1.5"/><path d="M6 19h6"/>',
        'key' => '<circle cx="8" cy="15" r="4"/><path d="m11 12 9-9M17 6l3 3M15 8l2 2"/>',
        'bolt' => '<path d="M13 2 4 14h7l-1 8 9-12h-7z"/>',
        'team' => '<circle cx="9" cy="8" r="3.5"/><circle cx="17" cy="9" r="2.5"/><path d="M2.5 20c.8-3.6 3.4-5.5 6.5-5.5s5.7 1.9 6.5 5.5M15.5 14.6c2.6-.3 4.9 1.2 5.8 4.4"/>',
    ];
@endphp
<section class="on-dark grain section" aria-labelledby="benefits-title">
    <div class="container-x">
        <h2 id="benefits-title" class="h-section" x-reveal>{{ tx('site.benefits.title') }}</h2>

        <ul class="mt-12 grid gap-4 sm:grid-cols-2 lg:mt-16 lg:grid-cols-6 lg:gap-5">
            @foreach (__('site.benefits.items') as $i => $item)
                @php($l = $layout[$i])
                <li class="card relative flex flex-col overflow-hidden {{ $l['span'] }} {{ $l['color'] }} {{ ($l['big'] ?? false) ? 'sm:col-span-2 min-h-[20rem] lg:min-h-[28rem]' : 'min-h-[15rem]' }}" x-reveal="{{ $i * 70 }}">
                    <svg class="{{ ($l['big'] ?? false) ? 'size-14 lg:size-20' : 'size-11' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$l['icon']] !!}</svg>
                    <div class="mt-auto pt-8">
                        <h3 class="{{ ($l['big'] ?? false) ? 'text-[clamp(1.45rem,3.6vw,3rem)]' : 'text-[1.35rem]' }} font-bold">{{ typo($item['title']) }}</h3>
                        <p class="mt-3 max-w-md text-[1.0625rem] leading-relaxed opacity-90">{{ typo($item['text']) }}</p>
                    </div>
                    @if ($l['big'] ?? false)
                        <span aria-hidden="true" class="pointer-events-none absolute -right-6 -top-10 select-none font-display text-[12rem] font-extrabold leading-none text-white/10 lg:text-[18rem]">&lt;/&gt;</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</section>
