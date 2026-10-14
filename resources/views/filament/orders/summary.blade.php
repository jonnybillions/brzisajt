<dl style="margin:0">
    @foreach ($rows as $row)
        <div style="display:grid;grid-template-columns:minmax(8rem,1fr) 2fr;gap:1rem;padding:.7rem 0;border-top:1px solid rgb(127 127 127 / .15)">
            <dt style="font-size:.875rem;opacity:.65">{{ $row['label'] }}</dt>
            <dd style="font-size:.875rem;font-weight:600;margin:0">{{ $row['value'] }}</dd>
        </div>
    @endforeach
</dl>
