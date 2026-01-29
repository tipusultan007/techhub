@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if(settings('site_logo'))
<img src="{{ settings('site_logo') }}" class="logo" alt="{{ settings('site_name', 'Tech Hub') }}">
@elseif (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo-v2.1.png" class="logo" alt="Laravel Logo">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
