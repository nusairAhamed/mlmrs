<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sample Label — {{ $labSample->sample_code }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background: #e5e7eb;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
            padding: 24px;
        }

        .label {
            width: 320px;
            border: 1.5px solid #222;
            border-radius: 6px;
            padding: 12px 14px 10px;
            background: #fff;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 6px;
        }

        .sample-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #111;
        }

        .meta {
            font-size: 9px;
            color: #555;
            margin-top: 2px;
        }

        .mono { font-family: 'Courier New', monospace; }

        .issued {
            font-size: 9px;
            color: #888;
            text-align: right;
            white-space: nowrap;
        }

        .divider {
            border: none;
            border-top: 1px solid #ddd;
            margin: 5px 0;
        }

        .barcode-wrap {
            margin: 8px 0 4px;
            width: 100%;
        }

        .barcode-wrap svg {
            width: 100% !important;
            height: 50px !important;
            display: block;
        }

        .sample-code {
            text-align: center;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #111;
            margin-top: 3px;
        }

        .no-print {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            border: none;
        }

        .btn-primary  { background: #4f46e5; color: #fff; }
        .btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }

        @media print {
            @page {
                size: 90mm 50mm;
                margin: 0;
            }

            body {
                background: #fff;
                min-height: unset;
                padding: 2mm;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .label {
                width: 100%;
                border: 1.5px solid #000;
                border-radius: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="label">
        <div class="header">
            <div>
                <div class="sample-title">{{ strtoupper($labSample->sample_type) }} Sample</div>
                <div class="meta">Order: <span class="mono">{{ $labSample->order->order_number }}</span></div>
                <div class="meta">Patient: <span class="mono">{{ $labSample->order->patient?->patient_code ?? '-' }}</span></div>
            </div>
            <div class="issued">{{ now()->format('d/m/Y H:i') }}</div>
        </div>

        <hr class="divider">

        <div class="barcode-wrap">
            {!! DNS1D::getBarcodeSVG($labSample->sample_code, 'C128', 1.5, 50, 'black', false) !!}
        </div>
        <div class="sample-code">{{ $labSample->sample_code }}</div>
    </div>

    <div class="no-print">
        <button class="btn btn-primary" onclick="window.print()">Print Again</button>
        <button class="btn btn-secondary" onclick="window.close()">Close</button>
    </div>

    <script>window.print();</script>
</body>
</html>
