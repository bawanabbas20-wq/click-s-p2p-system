@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
@if (trim($slot) === 'Laravel' || trim($slot) === 'Click P2P')
<div style="text-align: center; padding: 20px;">
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logo.png'))) }}" 
         alt="Click P2P Logo" 
         style="height: 60px; width: auto; display: block; margin: 0 auto 10px auto;">
    <p style="margin: 0; color: #666; font-size: 14px; font-family: Arial, sans-serif;">
        Procurement & Purchase System
    </p>
</div>
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
