@php
    $popup = $previewPopup ?? $activePopup ?? null;
    $isPreview = isset($previewPopup);
@endphp

@if($popup)
<div id="offerPopup" class="offer-popup-overlay" style="display: none; opacity: 0; transition: opacity 0.4s ease;">
    <div class="offer-popup-content">
        <button type="button" class="close-popup" aria-label="Close">
            <i class="ri-close-line"></i>
        </button>
        
        <div class="popup-grid">
            @if($popup->image_path)
            <div class="popup-image">
                <img src="{{ Storage::url($popup->image_path) }}" alt="{{ $popup->title }}">
            </div>
            @endif
            
            <div class="popup-details @if(!$popup->image_path) w-100 text-center @endif">
                <h3 class="popup-title">{{ $popup->title }}</h3>
                @if($popup->subtitle)
                    <p class="popup-subtitle">{{ $popup->subtitle }}</p>
                @endif
                
                @if($popup->button_text && $popup->link)
                    <a href="{{ $popup->link }}" class="popup-cta">{{ $popup->button_text }}</a>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .offer-popup-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(11, 18, 21, 0.8); z-index: 9999;
        display: flex; align-items: center; justify-content: center;
        padding: 20px; backdrop-filter: blur(5px);
    }
    .offer-popup-content {
        background: white; width: 100%; max-width: 800px;
        border-radius: 20px; overflow: hidden; position: relative;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        transform: scale(0.95); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .popup-grid { display: flex; align-items: stretch; min-height: 400px; }
    .popup-image { flex: 1; background: #f1f5f9; position: relative; }
    .popup-image img { 
        width: 100%; height: 100%; object-fit: cover; position: absolute; left: 0; top: 0;
    }
    .popup-details { 
        flex: 1; padding: 40px; display: flex; flex-direction: column; 
        justify-content: center; background: white; 
    }
    .popup-title { 
        font-family: 'Outfit', sans-serif; font-weight: 800; 
        color: var(--brand-navy, #024959); font-size: 2rem; 
        line-height: 1.1; margin-bottom: 15px; 
    }
    .popup-subtitle { 
        font-size: 1rem; color: var(--text-muted, #64748b); 
        margin-bottom: 30px; line-height: 1.6; 
    }
    .popup-cta { 
        display: inline-block; padding: 14px 28px; 
        background: var(--brand-emerald, #2dae9a); color: white; 
        font-weight: 600; border-radius: 8px; text-decoration: none; 
        transition: transform 0.2s, box-shadow 0.2s; align-self: start; 
    }
    .popup-cta:hover { 
        background: var(--brand-navy, #024959); color: white; 
        transform: translateY(-2px); box-shadow: 0 10px 20px -5px rgba(3, 166, 150, 0.3); 
    }
    .close-popup { 
        position: absolute; top: 15px; right: 15px; z-index: 10; 
        width: 36px; height: 36px; border-radius: 50%; 
        background: white; border: none; cursor: pointer; 
        display: flex; align-items: center; justify-content: center; 
        font-size: 1.2rem; color: #64748b; 
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); transition: 0.2s; 
    }
    .close-popup:hover { background: #fee2e2; color: #ef4444; transform: rotate(90deg); }

    /* Active State Class (applied by JS) */
    .offer-popup-overlay.show-popup { opacity: 1 !important; }
    .offer-popup-overlay.show-popup .offer-popup-content { transform: scale(1); }

    @media (max-width: 768px) {
        .popup-grid { flex-direction: column; min-height: auto; }
        .popup-image { height: 200px; flex: none; }
        .popup-details { padding: 30px 20px; text-align: center; }
        .popup-cta { align-self: center; width: 100%; text-align: center; }
        .close-popup { background: rgba(255,255,255,0.9); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const popup = document.getElementById('offerPopup');
    const closeBtn = document.querySelector('.close-popup');
    if (!popup) return;

    const popupId = "{{ $popup->id }}";
    const cookieName = "hide_offer_popup_" + popupId;
    const cookieDuration = {{ $popup->cookie_duration }};
    // If preview mode, delay is 0, otherwise standard delay
    const isPreview = @json($isPreview);
    const delay = isPreview ? 0 : {{ $popup->display_delay }} * 1000;

    // Helper to get cookie
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    // Helper to set cookie
    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    // Show popup if cookie not set OR if in preview mode
    if (isPreview || !getCookie(cookieName)) {
        setTimeout(() => {
            popup.style.display = 'flex';
            // Slight delay to allow display flex to apply before opacity transition
            setTimeout(() => {
                popup.classList.add('show-popup');
            }, 10);
        }, delay);
    }

    // Close Handler
    closeBtn.addEventListener('click', function() {
        popup.classList.remove('show-popup');
        setTimeout(() => {
            popup.style.display = 'none';
        }, 400);
        setCookie(cookieName, 'true', cookieDuration);
    });
    
    // Close on overlay click
    popup.addEventListener('click', function(e) {
        if (e.target === popup) {
            closeBtn.click();
        }
    });
});
</script>
@endif
