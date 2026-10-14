<section id="configurator" class="section pb-28 lg:pb-28" aria-labelledby="cfg-title">
    <div class="container-x">
        <div class="max-w-3xl" x-reveal>
            <h2 id="cfg-title" class="h-section">{{ tx('cfg.title') }}</h2>
            <p class="mt-5 text-[1.0625rem] leading-relaxed text-muted lg:text-[1.125rem]">{{ typo(__('cfg.subtitle')) }}</p>
        </div>

        <livewire:configurator lazy :style="request()->query('style')" />
    </div>
</section>
