<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sample Label</title>

    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .label {
            width: 320px;
            padding: 14px;
            border: 1px solid #111;
        }
        .row { display: flex; justify-content: space-between; gap: 10px; }
        .title { font-size: 12px; font-weight: 700; }
        .muted { font-size: 10px; color: #444; }
        .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
        .barcode { margin-top: 10px; }
        .code { margin-top: 6px; font-size: 11px; font-weight: 700; text-align: center; }
        @media print {
            body { margin: 0; }
            .label { border: none; }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="label">
        <div class="row">
            <div>
                <div class="title">{{ strtoupper($labSample->sample_type) }} SAMPLE</div>
                <div class="muted">Order: <span class="mono">{{ $labSample->order->order_number }}</span></div>
                <div class="muted">Patient: <span class="mono">{{ $labSample->order->patient?->patient_code ?? '-' }}</span></div>
            </div>
            <div class="muted" style="text-align:right;">
                {{ now()->format('Y-m-d H:i') }}
            </div>
        </div>

        <div class="barcode">
            {!! DNS1D::getBarcodeHTML($labSample->sample_code, 'C128', 2, 60) !!}
        </div>

        <div class="code mono">{{ $labSample->sample_code }}</div>
    </div>
</body>
</html>