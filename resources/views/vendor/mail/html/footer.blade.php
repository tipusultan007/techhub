<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center">
@if($slot->isEmpty())
&copy; {{ date('Y') }} {{ settings('site_name', 'Tech Hub') }}. All rights reserved.
@else
{{ Illuminate\Mail\Markdown::parse($slot) }}
@endif
</td>
</tr>
</table>
</td>
</tr>
