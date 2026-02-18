@extends('layouts.frontend')

@section('title', 'Store Locator | Tech Hub Computer Trading')
@section('meta_description', 'Find Tech Hub showrooms and service centers in Dubai and UAE.')

@section('content')
<style>
    .store-hero {
        background: var(--brand-navy);
        padding: 60px 0;
        text-align: center;
        color: white;
        margin-bottom: 50px;
    }
    .store-hero h1 {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 10px;
    }
    .store-hero p {
        font-size: 1.1rem;
        opacity: 0.9;
    }
    .store-grid {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 30px;
        margin-bottom: 60px;
    }
    .store-info-card {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid var(--border);
    }
    .store-info-card h2 {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--brand-deep-blue);
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .info-item {
        margin-bottom: 25px;
        display: flex;
        gap: 15px;
    }
    .info-item i {
        font-size: 1.5rem;
        color: var(--brand-emerald);
        margin-top: 3px;
    }
    .info-item h4 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 5px;
        color: var(--text-main);
    }
    .info-item p {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.4;
    }
    .map-container {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid var(--border);
        min-height: 450px;
    }
    .business-hours {
        margin-top: 30px;
        padding-top: 30px;
        border-top: 1px solid var(--border);
    }
    .hours-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 0.95rem;
    }
    .hours-row span:first-child {
        font-weight: 600;
        color: var(--text-main);
    }
    .hours-row span:last-child {
        color: var(--text-muted);
    }
    .btn-direction {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: var(--brand-gradient);
        color: white;
        padding: 15px;
        border-radius: 8px;
        font-weight: 700;
        margin-top: 30px;
        transition: 0.3s;
    }
    .btn-direction:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(22, 163, 74, 0.3);
        color: white;
    }

    @media (max-width: 991px) {
        .store-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="store-hero">
    <div class="container">
        <h1>Store Locator</h1>
        <p>Visit our flagship showroom in the heart of Dubai</p>
    </div>
</div>

<div class="container">
    <div class="store-grid">
        <!-- Store Details -->
        <div class="store-info-card">
            <h2><i class="ri-map-pin-2-fill"></i> Main Showroom</h2>
            
            <div class="info-item">
                <i class="ri-map-pin-fill"></i>
                <div>
                    <h4>Address</h4>
                    <p>{!! nl2br(e(settings('contact_address', 'Building 42, Computer Street, Al Mankhool, Bur Dubai, UAE'))) !!}</p>
                </div>
            </div>

            <div class="info-item">
                <i class="ri-phone-fill"></i>
                <div>
                    <h4>Phone</h4>
                    <p><a href="tel:+{{ settings('contact_phone', '+971 4 000 0000') }}">{{ settings('contact_phone', '+971 4 000 0000') }}</a></p>
                </div>
            </div>

            @if(settings('contact_whatsapp'))
            <div class="info-item">
                <i class="ri-whatsapp-fill" style="color: #25D366;"></i>
                <div>
                    <h4>WhatsApp</h4>
                    <p><a href="tel:+{{ settings('contact_whatsapp', '') }}">{{ settings('contact_whatsapp', '+971 4 000 0000') }}</a></p>
                </div>
            </div>
            @endif

            <div class="info-item">
                <i class="ri-mail-fill"></i>
                <div>
                    <h4>Email</h4>
                    <p><a href="mailto:{{ settings('contact_email', 'sales@techhub.ae') }}">{{ settings('contact_email', 'sales@techhub.ae') }}</a></p>
                </div>
            </div>

            <div class="business-hours">
                <h4><i class="ri-time-fill"></i> Business Hours</h4>
                @if(settings('hours_label_1'))
                <div class="hours-row">
                    <span>{{ settings('hours_label_1') }}</span>
                    <span>{{ settings('hours_time_1') }}</span>
                </div>
                @endif
                 @if(settings('hours_label_2'))
                <div class="hours-row">
                    <span>{{ settings('hours_label_2') }}</span>
                    <span>{{ settings('hours_time_2') }}</span>
                </div>
                @endif
            </div>

            <a href="https://maps.google.com" target="_blank" class="btn-direction">
                <i class="ri-navigation-fill"></i> Get Directions
            </a>
        </div>

        <!-- Google Map -->
        <div class="map-container relative">
            @php
                // Extract src if full iframe is pasted
                $mapHtml = settings('contact_map');
                $mapSrc = '';
                if (preg_match('/src="([^"]+)"/', $mapHtml, $match)) {
                    $mapSrc = $match[1];
                } else {
                    $mapSrc = $mapHtml; // Assume it's just the URL if no iframe tag
                }
            @endphp
            
            @if($mapSrc)
                <iframe 
                    src="{{ $mapSrc }}" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            @else
                 <div class="h-full w-full flex items-center justify-center bg-gray-100 text-gray-400">
                    <div class="text-center">
                        <i class="ri-map-2-line text-4xl mb-2"></i>
                        <p>Map Location Not Set</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
