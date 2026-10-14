@php($files = $getRecord()->files)
@if ($files->isEmpty())
    <p style="font-size:.875rem;opacity:.65">{{ __('admin.order.no_files') }}</p>
@else
    <ul style="display:grid;gap:.6rem;margin:0;padding:0;list-style:none">
        @foreach ($files as $file)
            <li style="display:flex;align-items:center;justify-content:space-between;gap:.75rem;font-size:.875rem">
                <span style="min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-weight:600" title="{{ $file->original_name }}">{{ $file->original_name }}</span>
                <span style="display:flex;align-items:center;gap:.75rem;flex-shrink:0">
                    <span style="opacity:.65">{{ $file->humanSize() }}</span>
                    <x-filament::link :href="route('admin.order-files.download', $file)" icon="heroicon-o-arrow-down-tray">{{ __('admin.order.download') }}</x-filament::link>
                </span>
            </li>
        @endforeach
    </ul>
@endif
