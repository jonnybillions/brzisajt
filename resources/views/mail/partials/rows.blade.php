<h2 style="margin:28px 0 12px;font-size:17px;font-weight:800;">{{ $title }}</h2>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:15px;line-height:1.5;">
    @foreach ($rows as $row)
        <tr>
            <td style="padding:8px 12px 8px 0;border-top:1px solid #E4E0D8;color:#5C5766;vertical-align:top;width:42%;">{{ $row['label'] }}</td>
            <td style="padding:8px 0;border-top:1px solid #E4E0D8;vertical-align:top;font-weight:600;">{!! nl2br(e($row['value'])) !!}</td>
        </tr>
    @endforeach
</table>
