<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Product Barcodes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        /* The A4 page container */
        .page {
            width: 210mm; /* A4 Width */
            padding: 5mm;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            align-content: flex-start;
        }

        /* --- Individual Sticker Styling --- */
        .sticker {
            width: 60mm;  /* Adjust to your label paper width */
            height: 40mm; /* Adjust to your label paper height */
            border: 1px dotted #ccc; /* Guide for cutting, hidden on print */
            margin: 5mm;
            padding: 4mm;
            box-sizing: border-box;
            page-break-inside: avoid; /* Prevents a sticker from breaking across pages */
            overflow: hidden;

            /* FIX: Use Flexbox to center everything */
            display: flex;
            flex-direction: column;
            justify-content: center; /* Vertical centering */
            align-items: center;     /* Horizontal centering */
        }

        .product-name {
            font-size: 10px;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            margin-bottom: 5px;
        }

        .barcode-container {
            margin-bottom: 5px;
        }

        .barcode-text {
            font-size: 11px;
            letter-spacing: 1px;
            font-family: 'Courier New', monospace;
            margin-bottom: 5px;
        }

        .price {
            font-size: 14px;
            font-weight: bold;
        }

        /* Print-specific styles */
        @media print {
            .no-print { display: none; }
            .sticker { border: none; } /* Hide dotted lines on actual labels */
            body, .page { margin: 0; padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="padding: 20px; text-align: center; border-bottom: 1px solid #ccc; background-color: #f8f9fa;">
        <p style="font-size: 14px; color: #333; margin-bottom: 10px;">
            Printing labels... Please ensure your print settings are correct (e.g., Scale: 100%, Margins: None).
        </p>
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
            Reprint
        </button>
        <button onclick="window.history.back()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Back
        </button>
    </div>

    <div class="page">
        @foreach($printQueue as $item)
            <div class="sticker">
                <div class="product-name">{{ $item->name }}</div>
                
                <div class="barcode-container">
                    {{-- FIX: Use {!! !!} to render the raw HTML generated for the barcode --}}
                    {!! $generator->getBarcode($item->barcode_value, $generator::TYPE_CODE_128, 2, 40) !!}
                </div>
                
                <div class="barcode-text">{{ $item->barcode_value }}</div>
                <div class="price">AED {{ number_format($item->price, 2) }}</div>
            </div>
        @endforeach
    </div>

</body>
</html>