<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->invoice_no }}</title>
    <style>
        /* General Settings */
        body {
            font-family: 'Courier New', Courier, monospace;
            background: #eee;
            margin: 0;
            padding: 0;
        }

        /* The Receipt Container */
        .ticket {
            width: 80mm;
            /* Standard Thermal Width */
            max-width: 80mm;
            margin: 20px auto;
            background: #fff;
            padding: 10px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        /* Text Alignment */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        /* Typography */
        h1 {
            font-size: 16px;
            margin: 5px 0;
            text-transform: uppercase;
        }

        p {
            font-size: 12px;
            margin: 2px 0;
        }

        .bold {
            font-weight: bold;
        }

        .small {
            font-size: 10px;
        }

        /* Separators */
        .line {
            border-bottom: 1px dashed #000;
            margin: 8px 0;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th {
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }

        td {
            padding: 4px 0;
            vertical-align: top;
        }

        /* Buttons (Hidden in Print) */
        .no-print {
            text-align: center;
            margin-bottom: 10px;
        }

        .btn {
            background: #333;
            color: #fff;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            font-family: sans-serif;
            font-size: 12px;
            cursor: pointer;
            border: none;
        }

        /* Print Media Query */
        @media print {
            body {
                background: #fff;
            }

            .ticket {
                width: 100%;
                margin: 0;
                box-shadow: none;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn">Print Receipt</button>
        <button onclick="window.close()" class="btn" style="background: #d9534f;">Close</button>
    </div>

    <div class="ticket">
        <!-- Header -->
        <div class="text-center">
            <h1>ElectroMart UAE</h1>
            <p>Dubai Silicon Oasis, Dubai</p>
            <p class="small">Phone: +971 50 123 4567</p>
            <p class="bold">TRN: 100200300400500</p>
            <p style="margin-top: 5px;"> {{ $order->vat_amount > 0 ? 'TAX INVOICE' : 'RECEIPT' }}</p>
        </div>

        <div class="line"></div>

        <!-- Meta Data -->
        <div>
            <p><strong>Inv No:</strong> {{ $order->invoice_no }}</p>
            <p><strong>Date:</strong> {{ $order->created_at->format('d/m/Y h:i A') }}</p>
            <p><strong>Customer:</strong> {{ $order->customer_name ?? 'Walk-in' }}</p>
            @if (isset($order->customer) && $order->customer->trn_number)
                <p><strong>Cust TRN:</strong> {{ $order->customer->trn_number }}</p>
            @endif
            <p><strong>Cashier:</strong> {{ $order->user->name ?? 'Admin' }}</p>
        </div>

        <div class="line"></div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th width="45%">Item</th>
                    <th width="15%" class="text-center">Qty</th>
                    <th width="20%" class="text-right">Price</th>
                    <th width="20%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>
                            {{ $item->product_name }}
                            @if ($item->variant)
                                <br><span class="small">{{ $item->variant->variant_name }}</span>
                            @endif

                            <!-- NEW: Display Serial & Warranty -->
                            @if ($item->serial_numbers)
                                <div style="font-size: 10px; margin-top: 2px;">
                                    SN: <span style="font-family: monospace;">{{ $item->serial_numbers }}</span>
                                </div>
                                @if ($item->warranty_end_date)
                                    <div style="font-size: 10px;">
                                        Warranty until:
                                        {{ \Carbon\Carbon::parse($item->warranty_end_date)->format('d M Y') }}
                                    </div>
                                @endif
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="line"></div>

        <!-- Totals Table -->
        <table style="font-weight: bold;">
            <tr>
                <td class="text-right" colspan="3">Cart Total:</td>
                <td class="text-right">{{ number_format($order->total + $order->discount, 2) }}</td>
            </tr>

            @if ($order->discount > 0)
                <tr>
                    <td class="text-right" colspan="3">Discount:</td>
                    <td class="text-right">-{{ number_format($order->discount, 2) }}</td>
                </tr>
            @endif

            <tr>
                <td class="text-right" colspan="3">Net Amount (Excl VAT):</td>
                <td class="text-right">{{ number_format($order->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td class="text-right" colspan="3">VAT (5%):</td>
                <td class="text-right">{{ number_format($order->vat_amount, 2) }}</td>
            </tr>
            <tr style="font-size: 14px; border-top: 1px dashed #000;">
                <td class="text-right" colspan="3">TOTAL PAYABLE:</td>
                <td class="text-right">{{ number_format($order->total, 2) }}</td>
            </tr>
        </table>

        <div class="line"></div>

        <div class="text-center">
            <p>Paid via: {{ ucfirst($order->payment_method) }}</p>
        </div>

        <div class="line"></div>

        <!-- Footer -->
        <div class="text-center small">
            <p>Thank you for shopping with us!</p>
            <p>Items can be exchanged within 7 days with original receipt.</p>
            <p>No Cash Refunds.</p>
        </div>

        <div class="text-center small" style="margin-top: 15px;">
            <p>Powered by ElectroMart System</p>
        </div>

    </div>

    <script>
        // Automatically open print dialog when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
