{{-- Živi pregled: pregledač od prostih blokova, reaguje na $wire.form bez zahteva ka serveru. --}}
<figure class="rounded-[1.75rem] border-2 border-ink bg-ink p-2.5 shadow-[0_6px_0_0_var(--c-violet)]">
    <figcaption class="flex items-center justify-between gap-2 px-2 pt-1 pb-2.5 text-[0.8125rem] font-bold text-cream">
        <span>{{ __('cfg.preview.label') }}</span>
        <span class="size-2 shrink-0 animate-pulse rounded-full bg-lime" aria-hidden="true"></span>
    </figcaption>

    <div aria-hidden="true" class="pv overflow-hidden rounded-[1.15rem] bg-cream" :class="'pv-' + (f.motion || 'smooth')" :style="`--p1:${palette[0]};--p2:${palette[1]};--p3:${palette[2]}`">
        {{-- Traka pregledača --}}
        <div class="flex items-center gap-1.5 border-b border-ink/10 bg-paper px-3 py-2">
            <span class="size-2 rounded-full bg-coral"></span><span class="size-2 rounded-full bg-lime"></span><span class="size-2 rounded-full bg-sky"></span>
            <span class="ms-2 flex-1 truncate rounded-full bg-cream px-3 py-0.5 text-[0.625rem] font-semibold text-muted">{{ __('cfg.preview.url') }}<span x-show="f.type === 'multipage' && pagesCount > 1">/…</span></span>
        </div>

        <div class="relative flex aspect-[4/3.3] text-[0.625rem]" :class="f.menuPosition === 'side' ? 'flex-row' : 'flex-col'">
            {{-- Bočni meni --}}
            <template x-if="f.menuPosition === 'side'">
                <div class="flex shrink-0 flex-col gap-2 border-e border-ink/10 bg-paper p-2.5 transition-all duration-500" :class="f.menuType === 'burger' ? 'w-9 items-center' : 'w-[28%]'">
                    <span class="size-4 rounded-md" style="background: var(--p1)"></span>
                    <template x-if="f.menuType === 'burger'">
                        <span class="pv-pulse mt-1 flex flex-col gap-0.5"><i class="block h-0.5 w-4 rounded bg-ink"></i><i class="block h-0.5 w-4 rounded bg-ink"></i><i class="block h-0.5 w-3 rounded bg-ink"></i></span>
                    </template>
                    <template x-if="f.menuType !== 'burger'">
                        <div class="mt-1 flex flex-col gap-1.5">
                            <template x-for="(label, i) in (f.type === 'multipage' ? pageLabels : pageLabels.slice(0, 5))" :key="i">
                                <span class="truncate rounded px-1 py-0.5 font-bold leading-tight" :class="i === 0 ? 'text-white' : 'text-ink/70'" :style="i === 0 ? 'background: var(--p1)' : ''" x-text="label"></span>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            <div class="relative flex min-w-0 flex-1 flex-col">
                {{-- Gornji meni --}}
                <template x-if="f.menuPosition !== 'side'">
                    <div class="flex items-center gap-2 border-b border-ink/10 bg-paper px-3 py-2">
                        <span class="size-4 shrink-0 rounded-md" style="background: var(--p1)"></span>
                        <span class="h-1.5 w-8 shrink-0 rounded bg-ink/80"></span>
                        <template x-if="f.menuType === 'burger'">
                            <span class="pv-pulse ms-auto flex flex-col gap-0.5"><i class="block h-0.5 w-4 rounded bg-ink"></i><i class="block h-0.5 w-4 rounded bg-ink"></i><i class="block h-0.5 w-3 rounded bg-ink"></i></span>
                        </template>
                        <template x-if="f.menuType !== 'burger'">
                            <div class="ms-auto flex min-w-0 gap-1 overflow-hidden">
                                <template x-for="(label, i) in pageLabels.slice(0, 4)" :key="i">
                                    <span class="shrink-0 truncate rounded-full px-1.5 py-0.5 font-bold" :class="i === 0 ? 'text-white' : 'text-ink/70'" :style="i === 0 ? 'background: var(--p1)' : ''" x-text="label"></span>
                                </template>
                                <span x-show="pagesCount > 4" class="shrink-0 font-bold text-ink/50">+<span x-text="pagesCount - 4"></span></span>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Sadržaj: horizontalno skrolovanje — sekcije u nizu --}}
                <div class="relative flex min-h-0 flex-1 gap-2 p-2.5" :class="f.type === 'onepage' && f.scroll === 'horizontal' ? 'flex-row' : 'flex-col'">
                    {{-- Početni ekran --}}
                    <div class="pv-hero relative min-h-0 shrink-0 overflow-hidden rounded-xl transition-all duration-500"
                         :class="[f.type === 'onepage' && f.scroll === 'horizontal' ? 'w-[78%]' : '', f.type === 'onepage' && f.scroll === 'slides' ? 'flex-1' : 'h-[58%]', f.hero === 'video' ? 'bg-ink' : '']"
                         :style="f.hero !== 'video' ? 'background: var(--p1)' : ''">
                        <template x-if="styleProject && styleProject.thumb && f.hero !== 'video'">
                            <img :src="styleProject.thumb" alt="" class="absolute inset-0 size-full object-cover object-top opacity-80 mix-blend-luminosity">
                        </template>
                        {{-- „Slika“: sunce i brda --}}
                        <template x-if="f.hero !== 'video'">
                            <div class="absolute inset-0">
                                <span class="pv-float absolute top-[18%] right-[16%] size-[18%] rounded-full" style="background: var(--p2)"></span>
                                <span class="absolute -bottom-[30%] -left-[10%] h-[70%] w-[70%] rounded-[50%] bg-ink/25"></span>
                                <span class="absolute -right-[15%] -bottom-[40%] h-[80%] w-[75%] rounded-[50%]" style="background: var(--p3); opacity: .85"></span>
                            </div>
                        </template>
                        <div class="relative z-10 flex h-full flex-col justify-end gap-1 p-3">
                            <span class="pv-rise h-2 w-[55%] rounded bg-white/95"></span>
                            <span class="pv-rise h-1.5 w-[35%] rounded bg-white/70" style="animation-delay: .15s"></span>
                            <span class="pv-rise mt-1 h-3 w-12 rounded-full" style="background: var(--p2); animation-delay: .3s"></span>
                        </div>
                        {{-- Slajder: strelice i tačke --}}
                        <template x-if="f.hero === 'slider'">
                            <div>
                                <span class="absolute top-1/2 left-1.5 z-10 grid size-4 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-[0.5rem] font-black text-ink">‹</span>
                                <span class="absolute top-1/2 right-1.5 z-10 grid size-4 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-[0.5rem] font-black text-ink">›</span>
                                <div class="absolute bottom-2 left-1/2 z-10 flex -translate-x-1/2 gap-1">
                                    <span class="pv-dot h-1.5 w-4 rounded-full bg-white"></span><span class="size-1.5 rounded-full bg-white/60"></span><span class="size-1.5 rounded-full bg-white/60"></span>
                                </div>
                            </div>
                        </template>
                        {{-- Video: dugme Play --}}
                        <template x-if="f.hero === 'video'">
                            <div class="absolute inset-0 grid place-items-center">
                                <span class="pv-pulse grid size-10 place-items-center rounded-full" style="background: var(--p2)">
                                    <svg class="ms-0.5 size-4 text-ink" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                                </span>
                                <span class="absolute right-2 bottom-2 left-2 h-1 rounded-full bg-white/20"><span class="pv-progress block h-full w-1/3 rounded-full" style="background: var(--p2)"></span></span>
                            </div>
                        </template>
                    </div>

                    {{-- Ispod prvog ekrana --}}
                    <template x-if="f.type === 'onepage' && f.scroll === 'horizontal'">
                        <div class="relative flex flex-1 flex-col gap-2 overflow-hidden rounded-xl bg-paper p-2">
                            <span class="h-1.5 w-2/3 rounded bg-ink/70"></span>
                            <span class="h-1 w-full rounded bg-ink/20"></span><span class="h-1 w-4/5 rounded bg-ink/20"></span>
                            <span class="pv-nudge absolute top-1/2 -left-3 grid size-6 -translate-y-1/2 place-items-center rounded-full text-[0.75rem] font-black text-white" style="background: var(--p1)">→</span>
                        </div>
                    </template>
                    <template x-if="f.type === 'onepage' && f.scroll === 'slides'">
                        <div class="absolute top-1/2 right-4 z-10 flex -translate-y-1/2 flex-col gap-1">
                            <template x-for="(label, i) in pageLabels.slice(0, 6)" :key="i">
                                <span class="rounded-full" :class="i === 0 ? 'pv-dot h-3 w-1.5 bg-white' : 'size-1.5 bg-white/60'"></span>
                            </template>
                        </div>
                    </template>
                    <template x-if="f.type === 'onepage' && f.scroll === 'classic'">
                        <div class="flex min-h-0 flex-1 flex-col gap-1.5 overflow-hidden">
                            <template x-for="(label, i) in pageLabels.slice(1, 5)" :key="i">
                                <div class="pv-rise flex shrink-0 items-center gap-2 rounded-lg bg-paper px-2 py-1.5" :style="`animation-delay: ${i * .1}s`">
                                    <span class="size-2.5 shrink-0 rounded-sm" :style="`background: var(--p${(i % 3) + 1})`"></span>
                                    <span class="truncate font-bold" x-text="label"></span>
                                </div>
                            </template>
                            <span x-show="pagesCount <= 1" class="h-1.5 w-1/2 rounded bg-ink/20"></span>
                        </div>
                    </template>
                    <template x-if="f.type !== 'onepage'">
                        <div class="grid min-h-0 flex-1 grid-cols-3 gap-2">
                            <template x-for="i in 3" :key="i">
                                <div class="pv-rise flex flex-col gap-1 rounded-lg bg-paper p-1.5" :style="`animation-delay: ${i * .1}s`">
                                    <span class="h-[45%] rounded" :style="`background: var(--p${i}); opacity: .9`"></span>
                                    <span class="h-1 w-4/5 rounded bg-ink/60"></span>
                                    <span class="h-1 w-3/5 rounded bg-ink/20"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <p class="mt-2.5 truncate px-2 text-[0.75rem] font-semibold text-muted-dark" x-show="styleProject" x-cloak>
        {{ __('cfg.style.like') }} <span class="text-lime" x-text="styleProject?.name"></span>
    </p>
</figure>
