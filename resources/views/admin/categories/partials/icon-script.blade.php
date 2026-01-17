<!-- AlpineJS & Icon Logic -->
<script>
    function iconPicker(initial = '') {
        return {
            isOpen: false,
            search: initial,
            selectedIcon: initial,
            icons: [
                // Tech & Electronics
                'ri-smartphone-line', 'ri-computer-line', 'ri-macbook-line', 'ri-hard-drive-2-line',
                'ri-tv-line', 'ri-camera-lens-line', 'ri-headphone-line', 'ri-speaker-line',
                'ri-gamepad-line', 'ri-watch-line', 'ri-printer-line', 'ri-router-line',
                'ri-cpu-line', 'ri-battery-charge-line', 'ri-plug-line', 'ri-usb-line',
                'ri-sim-card-line', 'ri-sd-card-line', 'ri-mouse-line', 'ri-keyboard-box-line',
                'ri-tablet-line', 'ri-flight-takeoff-line', 'ri-fire-line', 'ri-vidicon-line',
                'ri-scissors-cut-line', 'ri-webcam-line', 'ri-wifi-line', 'ri-bluetooth-line',
                // General Shopping
                'ri-shopping-bag-line', 'ri-shopping-cart-2-line', 'ri-gift-line', 'ri-price-tag-3-line',
                'ri-coupon-3-line', 'ri-store-2-line', 'ri-wallet-3-line', 'ri-truck-line',
                // Office & Tools
                'ri-briefcase-line', 'ri-calculator-line', 'ri-projector-2-line', 'ri-pencil-ruler-2-line',
                'ri-archive-line', 'ri-printer-cloud-line',
                // Misc
                'ri-lightbulb-line', 'ri-shield-check-line', 'ri-trophy-line', 'ri-rocket-line'
            ],
            get filteredIcons() {
                if (this.search === '' || this.search === this.selectedIcon) {
                    return this.icons;
                }
                return this.icons.filter(icon =>
                    icon.toLowerCase().includes(this.search.toLowerCase())
                );
            }
        }
    }
</script>
