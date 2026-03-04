<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Product Barcodes</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        
        /* The A4 page container */
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 10mm 5mm; /* Balanced vertical and horizontal padding */
            margin: 10mm auto;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* 4 columns */
            justify-items: center; /* Center stickers within grid cells */
            align-content: start;
            gap: 0;
            box-sizing: border-box;
            page-break-after: always;
        }

        /* Individual Sticker Styling */
        .sticker {
            /* border: 1px dotted #eee; */ /* Removed global border to avoid placeholder borders */
            box-sizing: border-box;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Visible border only for actual barcodes */
        .sticker.has-content {
            border: 1px dotted #eee;
        }

        /* Size: 1" x 0.375" */
        .size-1x0-375 {
            width: 1in;
            height: 0.375in;
            padding: 1mm;
            justify-content: space-between;
        }
        .size-1x0-375 .product-name { font-size: 6px; }
        .size-1x0-375 .barcode-text { font-size: 6px; }
        .size-1x0-375 .price { font-size: 7px; }

        /* Size: 48.5mm x 25.4mm (44 per page: 4 columns x 11 rows) */
        .size-48-5x25-4 {
            width: 48.5mm;
            height: 25.4mm;
            padding: 2mm;
            justify-content: center;
            margin-bottom: 0.5mm; /* Minimal spacing to fit 11 rows */
        }
        .size-48-5x25-4 .product-name { font-size: 9px; margin-bottom: 2px; }
        .size-48-5x25-4 .barcode-text { font-size: 8px; margin-bottom: 2px; }
        .size-48-5x25-4 .price { font-size: 11px; }

        /* Size: 2" x 1" (40 per page: 4 columns x 10 rows) */
        .size-2x1 {
            width: 2in;    /* ~50.8mm */
            height: 1in;   /* ~25.4mm */
            padding: 2mm;
            justify-content: center;
            margin-bottom: 2mm; /* Spacing for 10 rows */
        }
        .size-2x1 .product-name { font-size: 10px; margin-bottom: 4px; }
        .size-2x1 .barcode-text { font-size: 9px; margin-bottom: 4px; }
        .size-2x1 .price { font-size: 13px; }

        .product-name {
            font-weight: bold;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            text-align: center;
        }

        .barcode-container {
            margin: 1px 0;
            line-height: 0;
        }

        .barcode-text {
            letter-spacing: 0.5px;
            font-family: 'Courier New', monospace;
            line-height: 1;
        }

        .price {
            font-weight: bold;
            line-height: 1;
        }

        /* Print-specific styles */
        @media print {
            .no-print { display: none; }
            body { background-color: white; }
            .page { 
                margin: 0; 
                box-shadow: none;
                border: none;
            }
            .sticker { border: none; }
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

    @php
        $sizeClass = 'size-' . str_replace(['"', '.', ' '], ['', '-', ''], $size);
        
        // Define settings based on size
        $perPage = 40; // Default
        $barcodeWidth = 2;
        $barcodeHeight = 30;
        
        if ($size == '1x0.375') {
            $perPage = 80;
            $barcodeWidth = 1;
            $barcodeHeight = 10;
        } elseif ($size == '48.5x25.4') {
            $perPage = 44;
            $barcodeWidth = 1.3;
            $barcodeHeight = 15;
        } elseif ($size == '2x1') {
            $perPage = 40;
            $barcodeWidth = 1.5;
            $barcodeHeight = 20;
        }

        // Logic for skipping stickers
        // We'll prepend the printQueue with empty placeholders for the skipped amount
        $skip = (int) ($skip ?? 0);
        $totalItems = $printQueue->count() + $skip;
    @endphp

    @php
        // Create a flat collection including skip placeholders for the first page logic
        $placeholders = collect();
        for ($i = 0; $i < $skip; $i++) {
            $placeholders->push(null);
        }
        $fullQueue = $placeholders->concat($printQueue);
    @endphp

    @foreach($fullQueue->chunk($perPage) as $chunk)
        <div class="page">
            @foreach($chunk as $item)
                @if($item)
                    <div class="sticker {{ $sizeClass }} has-content">
                        <div class="product-name">{{ $item->name }}</div>
                        
                        <div class="barcode-container">
                            {!! $generator->getBarcode($item->barcode_value, $generator::TYPE_CODE_128, $barcodeWidth, $barcodeHeight) !!}
                        </div>
                        
                        <div class="barcode-text">{{ $item->barcode_value }}</div>
                        <div class="price">AED {{ number_format($item->price, 2) }}</div>
                    </div>
                @else
                    <div class="sticker {{ $sizeClass }}">
                        <!-- Empty placeholder for skipped sticker -->
                    </div>
                @endif
            @endforeach

            {{-- Padding for empty grid spots to maintain alignment --}}
            @php
                $emptySpots = $perPage - $chunk->count();
            @endphp
            @for($i = 0; $i < $emptySpots; $i++)
                <div class="sticker {{ $sizeClass }}">
                    <!-- Empty placeholder sticker without border -->
                </div>
            @endfor
        </div>
    @endforeach

</body>
</html>