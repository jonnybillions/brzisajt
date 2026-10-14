@php($dots = ['bg-lime text-ink', 'bg-sky text-ink', 'bg-coral text-ink', 'bg-violet text-white'])
<section id="how" class="on-dark grain section" aria-labelledby="how-title">
    <div class="container-x">
        <h2 id="how-title" class="h-section" x-reveal>{{ tx('site.how.title') }}</h2>

        <ol class="relative mt-12 grid gap-10 lg:mt-20 lg:grid-cols-4 lg:gap-8">
            {{-- Linija vremenske ose: vertikalna na mobilnom, horizontalna na desktopu --}}
            <span aria-hidden="true" class="absolute top-2 bottom-2 left-7 w-0.5 bg-white/15 lg:top-7 lg:right-[12%] lg:bottom-auto lg:left-[12%] lg:h-0.5 lg:w-auto"></span>

            @foreach (__('site.how.steps') as $i => $step)
                <li class="relative grid grid-cols-[3.5rem_1fr] gap-5 lg:grid-cols-1 lg:gap-6 lg:text-center" x-reveal="{{ $i * 120 }}">
                    <span class="relative z-10 grid size-14 place-items-center rounded-full font-display text-[1.5rem] font-extrabold ring-8 ring-ink {{ $dots[$i] }} lg:mx-auto">{{ $i + 1 }}</span>
                    <div>
                        <h3 class="text-[1.25rem] font-bold lg:text-[1.35rem]">{{ typo($step['title']) }}</h3>
                        <p class="mt-3 leading-relaxed text-muted-dark">{{ typo($step['text']) }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>
