<x-filament-panels::page>
    <form wire:submit="save" style="display:grid;gap:1.5rem">
        {{ $this->form }}

        <div>
            <x-filament::button type="submit">{{ __('filament-panels::resources/pages/edit-record.form.actions.save.label') }}</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
