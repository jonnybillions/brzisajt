<section id="faq" class="section border-t border-line bg-paper" aria-labelledby="faq-title">
    <div class="container-x grid gap-10 lg:grid-cols-12">
        <h2 id="faq-title" class="h-section lg:col-span-4" x-reveal>{{ tx('site.faq.title') }}</h2>

        <div class="lg:col-span-8" x-data="accordion">
            @foreach (__('site.faq.items') as $i => $item)
                <div class="border-b-2 border-ink first:border-t-2">
                    <h3>
                        <button type="button"
                                id="faq-q-{{ $i }}"
                                class="flex w-full items-center justify-between gap-6 py-5 text-left text-[1.0625rem] font-bold leading-snug lg:py-6 lg:text-[1.25rem]"
                                aria-controls="faq-a-{{ $i }}"
                                aria-expanded="false"
                                :aria-expanded="isOpen({{ $i }}).toString()"
                                @click="toggle({{ $i }})">
                            <span>{{ typo($item['q']) }}</span>
                            <span class="grid size-10 shrink-0 place-items-center rounded-full border-2 border-ink transition-[transform,background-color] duration-300"
                                  :class="isOpen({{ $i }}) && 'rotate-45 bg-lime'" aria-hidden="true">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                            </span>
                        </button>
                    </h3>
                    <div id="faq-a-{{ $i }}" role="region" aria-labelledby="faq-q-{{ $i }}" x-show="isOpen({{ $i }})" x-collapse.duration.300ms x-cloak>
                        <p class="max-w-2xl pb-6 text-[1.0625rem] leading-relaxed text-muted">{{ typo($item['a']) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
