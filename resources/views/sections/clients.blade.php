@if ($logos->isNotEmpty())
<section class="border-y border-line bg-paper py-10 lg:py-14" aria-labelledby="clients-title">
    <div class="container-x">
        <h2 id="clients-title" class="eyebrow text-muted">{{ __('site.clients.title') }}</h2>
    </div>

    <div class="group relative mt-8 overflow-hidden [mask-image:linear-gradient(90deg,transparent,#000_8%,#000_92%,transparent)] motion-reduce:[mask-image:none]">
        <ul class="flex w-max animate-marquee items-center gap-14 pe-14 group-hover:[animation-play-state:paused] group-focus-within:[animation-play-state:paused] motion-reduce:w-auto motion-reduce:animate-none motion-reduce:flex-wrap motion-reduce:justify-center motion-reduce:px-4 lg:gap-20 lg:pe-20">
            @foreach ([$logos, $logos] as $copy => $set)
                @foreach ($set as $logo)
                    <li class="shrink-0 @if ($copy) motion-reduce:hidden @endif" @if ($copy) aria-hidden="true" @endif>
                        @if ($logo->logo)
                            <img src="{{ Storage::disk('public')->url($logo->logo) }}" alt="{{ $copy ? '' : $logo->name }}" loading="lazy" decoding="async" @if ($logo->logo_width) width="{{ $logo->logo_width }}" height="{{ $logo->logo_height }}" @endif
                                 class="h-9 w-auto max-w-[15rem] object-contain opacity-60 grayscale transition duration-300 hover:opacity-100 hover:grayscale-0 lg:h-11">
                        @else
                            <span class="font-display text-xl font-bold opacity-60">{{ $logo->name }}</span>
                        @endif
                    </li>
                @endforeach
            @endforeach
        </ul>
    </div>
</section>
@endif
