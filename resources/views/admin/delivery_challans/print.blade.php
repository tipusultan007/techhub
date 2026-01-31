<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Challan - {{ $challan->challan_number }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 14px; line-height: 1.5; color: #333; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; border-bottom: 2px solid #eee; padding-bottom: 20px; }
        .logo { max-height: 60px; margin-bottom: 10px; }
        .company-info { font-size: 12px; color: #555; }
        .document-title { font-size: 24px; font-weight: bold; text-transform: uppercase; color: #333; text-align: right; }
        .document-details { text-align: right; font-size: 13px; margin-top: 10px; }
        .customer-section { margin-bottom: 30px; }
        .section-title { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #777; margin-bottom: 5px; border-bottom: 1px solid #ddd; padding-bottom: 2px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table th, .table td { border-bottom: 1px solid #eee; padding: 10px; text-align: left; }
        .table th { background-color: #f9f9f9; font-weight: bold; font-size: 12px; text-transform: uppercase; color: #555; }
        .text-center { text-align: center !important; }
        .text-bold { font-weight: bold; }
        .notes { margin-top: 30px; font-size: 12px; font-style: italic; color: #666; background: #f9f9f9; padding: 10px; border-radius: 4px; }
        .footer { margin-top: 60px; display: flex; justify-content: space-between; }
        .signature-box { text-align: center; width: 200px; }
        .signature-line { border-top: 1px solid #333; margin-bottom: 5px; }
        .signature-label { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #555; }
        @media print {
            body { padding: 0; margin: 0; }
            @page { margin: 1cm; }
        }
        .text-normal {
            font-weight: normal;
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <div>
            @if(settings('site_logo'))
                <img src="{{ settings('site_logo') }}" alt="{{ settings('site_name') }}" class="logo">
            @else
                <div style="font-size: 20px; font-weight: bold;">{{ settings('shop_name', 'Tech Hub Rak') }}</div>
            @endif
            <div class="company-info">
               <strong>{{ settings('shop_address', 'Computer Street, Bur Dubai, UAE') }}</strong><br>
                <strong>Phone:</strong> {{ settings('shop_phone', settings('contact_phone', '+971 4 000 0000')) }}<br>
                <strong>Email:</strong> {{ settings('contact_email', 'sales@techhubrak.ae') }} <br>
                <strong>Website:</strong> www.techhubrak.ae
            </div>
        </div>
        <div>
            <div class="document-title">Delivery Challan</div>
            <div class="document-details">
                <strong>Number:</strong> {{ $challan->challan_number }}<br>
                @if($challan->po_number)
                    <strong>PO#:</strong> {{ $challan->po_number }}<br>
                @endif
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($challan->date)->format('d M, Y') }}<br>
                @if($challan->quotation)
                    <strong>Ref Quotation:</strong> {{ $challan->quotation->quotation_no }}
                @endif
            </div>
        </div>
    </div>

    <div class="customer-section">
        <div class="section-title">Delivered To</div>
        <div style="font-weight: bold; font-size: 16px;">{{ $challan->customer->name ?? $challan->quotation->customer_name }}</div>
        @if($challan->customer)
            <div>{{ $challan->customer->phone }} | {{ $challan->customer->email }}</div>
            <div>{{ $challan->customer->address }}</div>
        @else
            <div>{{ $challan->quotation->customer->phone ?? '' }}</div>
        @endif
    </div>

    <table class="table">
        <thead>
            <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th>Description</th>
                <th class="text-center" style="width: 100px;">Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($challan->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-normal">{{ $item->product_name }}</td>
                <td class="text-center text-normal">{{ $item->quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($challan->note)
    <div class="notes">
        <strong>Note:</strong> {{ $challan->note }}
    </div>
    @endif

    <div class="footer">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Receiver's Signature</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Authorized Signature</div>
        </div>
    </div>

</body>
</html>
