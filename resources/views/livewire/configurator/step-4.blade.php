@php
    $field = function (string $name, string $label, string $type = 'text', array $attrs = []) {
        return compact('name', 'label', 'type', 'attrs');
    };
    $fields = [
        $field('name', __('cfg.contact.name'), 'text', ['autocomplete' => 'name', 'required' => true, 'maxlength' => 120]),
        $field('company', __('cfg.contact.company'), 'text', ['autocomplete' => 'organization', 'maxlength' => 160]),
        $field('phone', __('cfg.contact.phone'), 'tel', ['autocomplete' => 'tel', 'inputmode' => 'tel', 'maxlength' => 40, 'placeholder' => '+7']),
        $field('email', __('cfg.contact.email'), 'email', ['autocomplete' => 'email', 'inputmode' => 'email', 'maxlength' => 160]),
    ];
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    @foreach ($fields as $f)
        @php($key = 'form.'.$f['name'])
        <div>
            <label for="cfg-{{ $f['name'] }}" class="cfg-label">
                {{ $f['label'] }}@if ($f['attrs']['required'] ?? false)<span class="text-[#B42318]" aria-hidden="true">*</span><span class="sr-only"> ({{ __('cfg.contact.required') }})</span>@endif
            </label>
            <input id="cfg-{{ $f['name'] }}" type="{{ $f['type'] }}" wire:model="{{ $key }}" class="cfg-input"
                   @foreach ($f['attrs'] as $attr => $val) @if ($val === true) {{ $attr }} @else {{ $attr }}="{{ $val }}" @endif @endforeach
                   @if (in_array($f['name'], ['phone', 'email'])) aria-describedby="cfg-contact-hint @error($key) err-form-{{ $f['name'] }} @enderror" @else @error($key) aria-describedby="err-form-{{ $f['name'] }}" @enderror @endif
                   @error($key) aria-invalid="true" @enderror>
            <x-cfg.error :field="$key" />
        </div>
    @endforeach
</div>
<p id="cfg-contact-hint" class="!mt-3 text-[0.9375rem] text-muted">{{ typo(__('cfg.contact.hint')) }}</p>

<x-cfg.question :title="__('cfg.channel.q')" error="form.channel">
    <div class="flex flex-wrap gap-2">
        @foreach (__('cfg.channel.options') as $value => $label)
            <label class="chip cursor-pointer border-2 border-ink px-5 py-2.5 transition-colors hover:bg-ink/5 has-checked:bg-ink has-checked:text-cream has-focus-visible:outline-3 has-focus-visible:outline-offset-3 has-focus-visible:outline-violet">
                <input type="radio" class="sr-only" name="form.channel" value="{{ $value }}" wire:model="form.channel">
                {{ $label }}
            </label>
        @endforeach
    </div>
</x-cfg.question>

<div>
    <label for="cfg-comment" class="cfg-label">{{ __('cfg.comment.label') }}</label>
    <textarea id="cfg-comment" wire:model="form.comment" rows="4" maxlength="3000" placeholder="{{ __('cfg.comment.placeholder') }}" class="cfg-input min-h-28 resize-y"
              @error('form.comment') aria-invalid="true" aria-describedby="err-form-comment" @enderror></textarea>
    <x-cfg.error field="form.comment" />
</div>

<div>
    <label class="flex cursor-pointer items-start gap-3 text-[1rem] leading-snug">
        <input type="checkbox" wire:model="form.consent" class="peer mt-0.5 size-5 shrink-0 cursor-pointer accent-violet" required
               @error('form.consent') aria-invalid="true" aria-describedby="err-form-consent" @enderror>
        <span>
            {{ __('cfg.consent.before') }}<a href="{{ route('privacy') }}" target="_blank" class="font-bold text-violet underline underline-offset-2">{{ __('cfg.consent.link') }}<span class="sr-only"> ({{ __('site.a11y.external') }})</span></a>{{ __('cfg.consent.after') }}
        </span>
    </label>
    <x-cfg.error field="form.consent" />
</div>

@if ($captchaEnabled)
    @include('livewire.configurator.captcha')
@endif
