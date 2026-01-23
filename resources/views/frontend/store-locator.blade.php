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
        color: var(--brand-magenta);
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
                    <p>Building 42, Computer Street,<br>Al Mankhool, Bur Dubai, UAE</p>
                </div>
            </div>

            <div class="info-item">
                <i class="ri-phone-fill"></i>
                <div>
                    <h4>Phone</h4>
                    <p>+971 4 000 0000</p>
                </div>
            </div>

            <div class="info-item">
                <i class="ri-whatsapp-fill" style="color: #25D366;"></i>
                <div>
                    <h4>WhatsApp</h4>
                    <p>+971 50 000 0000</p>
                </div>
            </div>

            <div class="info-item">
                <i class="ri-mail-fill"></i>
                <div>
                    <h4>Email</h4>
                    <p>sales@techhub.ae</p>
                </div>
            </div>

            <div class="business-hours">
                <h4><i class="ri-time-fill"></i> Business Hours</h4>
                <div class="hours-row">
                    <span>Monday - Saturday</span>
                    <span>9:00 AM - 9:00 PM</span>
                </div>
                <div class="hours-row">
                    <span>Sunday</span>
                    <span>11:00 AM - 7:00 PM</span>
                </div>
            </div>

            <a href="https://maps.google.com" target="_blank" class="btn-direction">
                <i class="ri-navigation-fill"></i> Get Directions
            </a>
        </div>

        <!-- Google Map -->
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14436.438517220551!2d55.289133!3d25.260583!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f43393979bb95%3A0xe67f5bb14f5a34e8!2sBur%20Dubai%20-%20Dubai!5e0!3m2!1sen!2sae!4v1705660000000!5m2!1sen!2sae" 
                width="100%" 
                height="100%" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </div>
</div>
@endsection
