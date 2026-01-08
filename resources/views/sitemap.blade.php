<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($urls as $url)
    <url>
        <loc>{{ $url }}</loc>
        <lastmod>{{ $lastmod }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
@endforeach

@if (!empty($getRides))
    @foreach ($getRides as $getRide)
    @if (isset($getRide->defaultRideDetail[0]->departure) && isset($getRide->defaultRideDetail[0]->destination) && $getRide->defaultRideDetail[0]->departure != null)
    @php
      $date = Carbon\Carbon::parse($getRide->created_at);
    @endphp 
      <url>
        <loc>{{ route('ride_detail', ['lang' => 'en', 'departure' => $getRide->defaultRideDetail[0]->departure, 'destination' => $getRide->defaultRideDetail[0]->destination, 'id' => $getRide->id]) }}</loc>
        <lastmod>{{date("Ymd\THis\Z", strtotime($date))}}</lastmod>
        <priority>0.80</priority>
      </url>    
    @endif
    
    @endforeach
@endif
</urlset>
