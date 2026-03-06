@extends('layouts.frontend')

@section('title', 'Processing Payment | Tech Hub')

@section('content')
<div class="container py-10 text-center" style="min-height: 50vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">

    <div class="payment-loading" id="payment-loading">
        <div class="mb-4">
            <i class="ri-lock-line text-5xl text-emerald-500"></i>
        </div>
        <h2 class="text-2xl font-bold mb-2">Redirecting to Secure Payment...</h2>
        <p class="text-gray-600 mb-6">Please do not refresh the page or click back.</p>

        <div class="order-details-mini p-6 bg-gray-50 rounded-lg border inline-block text-left mb-6">
            <p><strong>Order ID:</strong> {{ $order->invoice_no }}</p>
            <p><strong>Amount:</strong> {{ number_format($order->total, 2) }} AED</p>
        </div>

        <div class="mt-4" id="manual-btn-wrapper" style="display:none;">
            <p class="text-sm text-gray-500 mb-2">Payment window did not open automatically.</p>
            <button id="manual-pay-btn" class="bg-emerald-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-emerald-700 transition">
                Open Secure Payment Window
            </button>
        </div>

        <div id="payment-error" style="display:none;" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
            <strong>Could not load payment gateway.</strong>
            <p class="text-sm mt-1">Please <a href="{{ route('checkout.index') }}" class="underline">go back</a> and try again.</p>
        </div>
    </div>

</div>

@push('scripts')
{{--
    ALL functions must be defined BEFORE the RAKBANK script tag,
    because the script's onload fires before the next <script> block executes.
--}}
<script type="text/javascript">
    var RAKBANK_SESSION_ID = '{{ $sessionId }}';
    var RAKBANK_CALLBACK_URL = "{{ route('rakbank.callback') }}?order_id={{ $order->id }}";
    var CHECKOUT_INDEX_URL = "{{ route('checkout.index') }}";
    var CHECKOUT_SUCCESS_URL = "{{ route('checkout.success', $order->id) }}";

    function errorCallback(error) {
        console.error("RAKBANK Error:", JSON.stringify(error));
        var errDiv = document.getElementById('payment-error');
        if (errDiv) {
            errDiv.style.display = 'block';
            errDiv.innerHTML = '<strong>Payment Error:</strong> <p class="text-sm mt-1">' +
                (error.cause || 'An error occurred') +
                '. <a href="' + CHECKOUT_INDEX_URL + '" class="underline">Go back and try again</a>.</p>';
        }
    }

    function cancelCallback() {
        window.location.href = CHECKOUT_INDEX_URL;
    }

    function completeCallback(resultIndicator, sessionVersion) {
        window.location.href = RAKBANK_CALLBACK_URL + '&resultIndicator=' + resultIndicator;
    }

    function onRakbankScriptError() {
        console.error("Failed to load RAKBANK checkout script.");
        var errDiv = document.getElementById('payment-error');
        if (errDiv) errDiv.style.display = 'block';
    }

    function initRakbankCheckout() {
        if (typeof Checkout === 'undefined' || typeof Checkout.configure !== 'function') {
            console.error("Checkout global not available after script load.");
            onRakbankScriptError();
            return;
        }

        // API v67+: ONLY session is allowed in configure()
        // All other settings must be passed via INITIATE_CHECKOUT on the server
        Checkout.configure({
            session: {
                id: RAKBANK_SESSION_ID
            }
        });

        // Try Lightbox first, fall back to full-page redirect
        setTimeout(function() {
            if (typeof Checkout.showLightbox === 'function') {
                try {
                    Checkout.showLightbox();
                } catch (e) {
                    console.warn("Lightbox failed, trying showPaymentPage:", e);
                    Checkout.showPaymentPage();
                }
            } else if (typeof Checkout.showPaymentPage === 'function') {
                console.info("Lightbox not available, using payment page redirect.");
                Checkout.showPaymentPage();
            } else {
                console.error("No RAKBANK payment method available.");
                document.getElementById('manual-btn-wrapper').style.display = 'block';
            }
        }, 800);
    }

    // Fallback: show manual button after 5 seconds
    setTimeout(function() {
        var btn = document.getElementById('manual-btn-wrapper');
        if (btn) btn.style.display = 'block';
    }, 5000);

    // Manual button listener
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('manual-pay-btn');
        if (btn) {
            btn.addEventListener('click', function() {
                if (typeof Checkout === 'undefined') {
                    onRakbankScriptError();
                } else if (typeof Checkout.showLightbox === 'function') {
                    Checkout.showLightbox();
                } else if (typeof Checkout.showPaymentPage === 'function') {
                    Checkout.showPaymentPage();
                } else {
                    onRakbankScriptError();
                }
            });
        }
    });
</script>

{{-- Load RAKBANK script AFTER all callbacks are defined --}}
<script
    src="https://rakbankpay-nam.gateway.mastercard.com/static/checkout/checkout.min.js"
    data-error="errorCallback"
    data-cancel="cancelCallback"
    data-complete="completeCallback"
    onload="initRakbankCheckout()"
    onerror="onRakbankScriptError()">
</script>
@endpush
@endsection
