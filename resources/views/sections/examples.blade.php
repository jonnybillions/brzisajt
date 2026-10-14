@php
    use App\Enums\Industry;
    $tagColors = [
        'restaurants' => 'bg-coral text-ink',
        'architecture' => 'bg-sky text-ink',
        'business' => 'bg-lime text-ink',
    ];
    $present = $projects->pluck('industry')->unique()->map->value->all();
@endphp
<section id="examples" class="section" aria-labelledby="examples-title" x-data="{ filter: 'all' }">
    <div class="container-x">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl" x-reveal>
                <h2 id="examples-title" class="h-section">{{ tx('site.examples.title') }}</h2>
                <p class="mt-5 text-[1.0625rem] leading-relaxed text-muted lg:text-[1.125rem]">{{ typo(__('site.examples.subtitle')) }}</p>
            </div>

            @if ($projects->isNotEmpty())
                <div role="group" aria-label="{{ __('site.examples.filter_label') }}" class="flex flex-wrap gap-2" x-reveal="100">
                    @foreach (['all' => __('site.examples.filter_all')] + Industry::options() as $value => $label)
                        @continue($value !== 'all' && ! in_array($value, $present, true))
                        <button type="button"
                                class="chip border-2 border-ink transition-colors"
                                :class="filter === @js($value) ? 'bg-ink text-cream' : 'bg-transparent hover:bg-ink/5'"
                                :aria-pressed="(filter === @js($value)).toString()"
                                aria-pressed="{{ $value === 'all' ? 'true' : 'false' }}"
                                @click="filter = @js($value)">{{ $label }}</button>
                    @endforeach
                </div>
            @endif
        </div>

        @if ($projects->isEmpty())
            <p class="mt-12 text-muted">{{ __('site.examples.empty') }}</p>
        @else
            <ul class="mt-12 grid gap-6 sm:grid-cols-2 lg:mt-14 lg:grid-cols-3 lg:gap-7">
                @foreach ($projects as $project)
                    <li x-show="filter === 'all' || filter === @js($project->industry->value)"
                        x-transition:enter="transition duration-300 ease-out"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100">
                        <article x-spotlight class="group relative flex h-full flex-col overflow-hidden rounded-card border-2 border-ink bg-paper transition-transform duration-300 hover:-translate-y-1">
                            {{-- Isticanje koje prati kursor (desktop) --}}
                            <span aria-hidden="true" class="pointer-events-none absolute z-10 size-56 -translate-x-1/2 -translate-y-1/2 rounded-full bg-lime opacity-0 mix-blend-multiply blur-3xl transition-opacity duration-300 group-hover:opacity-50" style="left: var(--mx, 50%); top: var(--my, 50%);"></span>

                            <div class="aspect-[16/10] overflow-hidden border-b-2 border-ink bg-line">
                                <x-picture :path="$project->cover" :width="$project->cover_width" :height="$project->cover_height"
                                           sizes="(min-width: 1024px) 380px, (min-width: 640px) 50vw, 100vw"
                                           alt="" class="size-full object-cover object-top transition-transform duration-500 group-hover:scale-[1.03]" />
                            </div>

                            <div class="relative z-20 flex flex-1 flex-col p-6">
                                <span class="chip w-max px-3 py-1 text-[0.8125rem] {{ $tagColors[$project->industry->value] }}">{{ $project->industry->label() }}</span>
                                <h3 class="mt-4 text-[1.3rem] font-bold leading-tight">{{ typo($project->name) }}</h3>

                                <div class="mt-auto flex flex-wrap gap-2 pt-6">
                                    <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="btn btn-ghost bg-paper !min-h-11 !px-4 !py-2 text-[0.9375rem]">
                                        {{ __('site.examples.view') }}
                                        <span class="sr-only">{{ $project->name }} ({{ __('site.a11y.external') }})</span>
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"/></svg>
                                    </a>
                                    <a href="?style={{ $project->slug }}#configurator"
                                       @click.prevent="$dispatch('cfg-style', { slug: @js($project->slug) })"
                                       class="btn btn-primary !min-h-11 !px-4 !py-2 text-[0.9375rem] !shadow-none">
                                        {{ __('site.examples.use_style') }}
                                        <span class="sr-only">— {{ $project->name }}</span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</section>
