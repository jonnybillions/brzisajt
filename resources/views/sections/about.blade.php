<section id="why" class="section" aria-labelledby="about-title">
    <div class="container-x">
        <h2 id="about-title" class="h-section max-w-4xl" x-reveal>{{ tx('site.about.title') }}</h2>

        <div class="mt-12 grid gap-6 lg:mt-16 lg:grid-cols-2 lg:gap-8">
            <div class="card border-2 border-ink bg-paper" x-reveal>
                <p class="text-[1.0625rem] leading-relaxed lg:text-[1.125rem]">{{ typo(__('site.about.p1')) }}</p>
                <p class="mt-4 text-[1.0625rem] leading-relaxed text-muted lg:text-[1.125rem]">{{ typo(__('site.about.p2')) }}</p>
            </div>

            <div class="card relative bg-violet text-white" x-reveal="120">
                <h3 class="text-[1.5rem] font-bold lg:text-[1.75rem]">{{ tx('site.about.what_title') }}</h3>
                <p class="mt-4 text-[1.0625rem] leading-relaxed">{{ typo(__('site.about.what_p1')) }}</p>
                <p class="mt-4 text-[1.0625rem] leading-relaxed text-white/85">{{ typo(__('site.about.what_p2')) }}</p>
            </div>
        </div>

        <p class="mt-12 max-w-4xl font-display text-[clamp(1.35rem,3vw,2.25rem)] font-semibold leading-tight lg:mt-16" x-reveal>
            <span class="me-2 inline-block size-[0.7em] -translate-y-[0.05em] rounded-full bg-coral align-middle" aria-hidden="true"></span>{{ tx('site.about.mission') }}
        </p>
    </div>
</section>
