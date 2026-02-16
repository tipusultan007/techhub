<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>AMC Agreement - {{ $amc->contract_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 13px; color: #333; line-height: 1.5; }
        .header { border-bottom: 2px solid #2dae9a; padding-bottom: 20px; margin-bottom: 30px; }
        .company-info h1 { margin: 0; font-size: 28px; color: #024959; text-transform: uppercase; }
        .company-info p { margin: 5px 0 0; font-size: 10px; color: #2dae9a; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }
        .contract-meta { text-align: right; position: absolute; right: 0; top: 0; }
        .contract-meta p.label { font-size: 10px; font-weight: bold; color: #999; text-transform: uppercase; margin: 0; }
        .contract-meta p.value { font-size: 16px; font-weight: bold; margin: 5px 0 0; }
        .content { margin-bottom: 50px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th { background: #f8fafc; text-align: left; padding: 10px; font-size: 11px; text-transform: uppercase; color: #64748b; border: 1px solid #e2e8f0; }
        table td { padding: 10px; border: 1px solid #e2e8f0; vertical-align: top; }
        .footer { margin-top: 100px; border-top: 1px solid #eee; padding-top: 20px; }
        .signatures { display: table; width: 100%; margin-top: 50px; }
        .signature-box { display: table-cell; width: 50%; border-top: 1px solid #ccc; padding-top: 10px; text-align: center; }
        .signature-label { font-size: 9px; font-weight: bold; color: #aaa; text-transform: uppercase; }
        /* TinyMCE content styles */
        p { margin-bottom: 1em; }
        h1, h2, h3 { color: #024959; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <h1>{{ settings('site_name', 'TECH HUB') }}</h1>
            <p>Annual Maintenance Contract (AMC)</p>
        </div>
        <div class="contract-meta">
            <p class="label">Contract Number</p>
            <p class="value">#{{ $amc->contract_number }}</p>
        </div>
    </div>

    <div class="content">
        {!! $content !!}
    </div>

    <div class="signatures">
        <div class="signature-box" style="padding-right: 50px;">
            <p class="signature-label">Customer Signature & Seal</p>
        </div>
        <div class="signature-box" style="padding-left: 50px;">
            <p class="signature-label">For {{ settings('site_name', 'TECH HUB') }}</p>
        </div>
    </div>
</body>
</html>
