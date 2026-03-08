<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab Report - {{ $labOrder->order_number }}</title>
    <style>
        @page {
            margin: 20px 22px 24px 22px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 0;
            line-height: 1.35;
            background: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        .report-wrapper {
            width: 100%;
        }

        .full-width {
            width: 100%;
            border-collapse: collapse;
        }

        .header-brand {
            border: 1px solid #d1d5db;
            border-bottom: none;
            padding: 0;
        }

        .brand-top {
            background: #0f4c81;
            height: 8px;
            width: 100%;
        }

        .brand-body {
            padding: 14px 16px 12px 16px;
        }

        .brand-name {
            font-size: 22px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 2px 0;
        }

        .brand-tagline {
            font-size: 10px;
            color: #374151;
            margin: 0 0 6px 0;
            font-weight: bold;
            letter-spacing: 0.2px;
        }

        .brand-meta {
            font-size: 10px;
            color: #4b5563;
            line-height: 1.4;
        }

        .report-box {
            text-align: right;
        }

        .report-title {
            font-size: 17px;
            font-weight: bold;
            color: #111827;
            margin: 0 0 4px 0;
        }

        .report-meta {
            font-size: 10px;
            color: #4b5563;
            line-height: 1.5;
        }

        .section-box {
            border: 1px solid #d1d5db;
            border-top: none;
            padding: 12px 16px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #111827;
            margin: 0 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .patient-table {
            width: 100%;
            border-collapse: collapse;
        }

        .patient-table td {
            vertical-align: top;
        }

        .patient-meta-cell {
            width: 76%;
            padding-right: 12px;
        }

        .patient-qr-cell {
            width: 24%;
            text-align: right;
        }

        .patient-info-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .patient-info-grid td {
            width: 25%;
            padding: 6px 10px 6px 0;
            vertical-align: top;
        }

        .meta-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.25px;
            margin-bottom: 2px;
        }

        .meta-value {
            font-size: 11px;
            color: #111827;
            font-weight: bold;
        }

        .qr-box {
            display: inline-block;
            text-align: center;
            border: 1px solid #d1d5db;
            padding: 8px;
        }

        .qr-label {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .qr-note {
            font-size: 8px;
            color: #6b7280;
            margin-top: 4px;
            line-height: 1.3;
        }

        .results-shell {
            border: 1px solid #d1d5db;
            border-top: none;
            padding: 12px 16px 14px 16px;
        }

        .panel-block {
            margin-top: 10px;
        }

        .panel-block:first-child {
            margin-top: 0;
        }

        .panel-title {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e3a8a;
            font-size: 11px;
            font-weight: bold;
            padding: 7px 10px;
            margin: 0 0 0 0;
            text-transform: uppercase;
            letter-spacing: 0.25px;
        }

        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }

        .result-table th {
            background: #f8fafc;
            color: #334155;
            font-size: 10px;
            font-weight: bold;
            text-align: left;
            padding: 7px 8px;
            border: 1px solid #dbe2ea;
        }

        .result-table td {
            padding: 7px 8px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            font-size: 10.5px;
        }

        .test-name {
            font-weight: bold;
            color: #111827;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .flag-normal {
            color: #15803d;
            font-weight: bold;
        }

        .flag-low {
            color: #1d4ed8;
            font-weight: bold;
        }

        .flag-high,
        .flag-abnormal {
            color: #b91c1c;
            font-weight: bold;
        }

        .abnormal-row {
            background: #fff7f7;
        }

        .muted {
            color: #6b7280;
            font-size: 10px;
        }

        .footer-box {
            border: 1px solid #d1d5db;
            border-top: none;
            padding: 14px 16px;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            vertical-align: bottom;
        }

        .note-block {
            width: 58%;
        }

        .approval-block {
            width: 42%;
            text-align: right;
        }

        .signature-wrap {
            display: inline-block;
            min-width: 220px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #9ca3af;
            margin-top: 20px;
            padding-top: 6px;
        }

        .approved-name {
            font-size: 11px;
            font-weight: bold;
            color: #111827;
        }

        .approved-role {
            font-size: 9px;
            color: #6b7280;
            margin-top: 2px;
        }

        .approved-date {
            font-size: 9px;
            color: #6b7280;
            margin-top: 2px;
        }

        .small-note {
            font-size: 9px;
            color: #6b7280;
            line-height: 1.5;
        }

        .page-footer {
            margin-top: 8px;
            font-size: 9px;
            color: #9ca3af;
            text-align: right;
        }

        .nowrap {
            white-space: nowrap;
        }
    </style>
</head>
<body>
@php
    function formatRefRange($min, $max) {
        if (is_null($min) && is_null($max)) {
            return '-';
        }

        $format = function ($value) {
            if (is_null($value)) {
                return '-';
            }

            return rtrim(rtrim(number_format((float) $value, 4, '.', ''), '0'), '.');
        };

        return $format($min) . ' – ' . $format($max);
    }

    function abnormalPdfLabel($test) {
        if (!$test->is_abnormal) {
            return ['label' => 'Normal', 'class' => 'flag-normal'];
        }

        if (
            ($test->test?->data_type ?? 'text') === 'numeric' &&
            !is_null($test->result_value) &&
            is_numeric($test->result_value)
        ) {
            $value = (float) $test->result_value;

            if (!is_null($test->ref_min) && $value < (float) $test->ref_min) {
                return ['label' => 'Low', 'class' => 'flag-low'];
            }

            if (!is_null($test->ref_max) && $value > (float) $test->ref_max) {
                return ['label' => 'High', 'class' => 'flag-high'];
            }
        }

        return ['label' => 'Abnormal', 'class' => 'flag-abnormal'];
    }

    $publicReportUrl = $labOrder->qrToken
        ? route('public-reports.show', $labOrder->qrToken->token)
        : null;

    $qrSvg = null;
    if ($publicReportUrl) {
        $qrSvg = base64_encode(
            \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(90)->margin(1)->generate($publicReportUrl)
        );
    }
@endphp

<div class="report-wrapper">

    {{-- Header --}}
    <table class="full-width header-brand">
        <tr>
            <td>
                <div class="brand-top"></div>
                <div class="brand-body">
                    <table class="full-width">
                        <tr>
                            <td style="width: 62%; vertical-align: top;">
                                <div class="brand-name">Your Laboratory Name</div>
                                <div class="brand-tagline">Accurate | Caring | Trusted</div>
                                <div class="brand-meta">
                                    Laboratory Address Line 1<br>
                                    Laboratory Address Line 2<br>
                                    Phone: 0XX-XXXXXXX | Email: lab@example.com
                                </div>
                            </td>
                            <td style="width: 38%; vertical-align: top;" class="report-box">
                                <div class="report-title">Laboratory Report</div>
                                <div class="report-meta">
                                    <strong>Report No:</strong> {{ $labOrder->order_number }}<br>
                                    <strong>Issued At:</strong>
                                    {{ $labOrder->approved_at ? $labOrder->approved_at->format('Y-m-d h:i A') : '-' }}<br>
                                    <strong>Status:</strong> Approved
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    {{-- Patient Information --}}
    <div class="section-box">
        <div class="section-title">Patient Information</div>

        <table class="patient-table">
            <tr>
                <td class="patient-meta-cell">
                    <table class="patient-info-grid">
                        <tr>
                            <td>
                                <div class="meta-label">Patient Code</div>
                                <div class="meta-value">{{ $patient?->patient_code ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="meta-label">Patient Name</div>
                                <div class="meta-value">{{ $patient?->full_name ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="meta-label">Age</div>
                                <div class="meta-value">{{ $age ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="meta-label">Gender</div>
                                <div class="meta-value">{{ !empty($patient?->gender) ? ucfirst($patient->gender) : '-' }}</div>
                            </td>
                        </tr>
                    </table>
                </td>

                <td class="patient-qr-cell">
                    @if($qrSvg)
                        <div class="qr-box">
                            <div class="qr-label">Scan to view report</div>
                            <img
                                src="data:image/svg+xml;base64,{{ $qrSvg }}"
                                width="90"
                                height="90"
                                alt="QR Code"
                            >
                            <div class="qr-note">Secure patient report access</div>
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Results --}}
    <div class="results-shell">
        <div class="section-title" style="margin-bottom: 10px;">Test Results</div>

        @forelse($labOrder->groups as $group)
            <div class="panel-block">
                <div class="panel-title">
                    {{ $group->testGroup?->name ?? 'Panel' }}
                </div>

                <table class="result-table">
                    <thead>
                        <tr>
                            <th style="width: 34%;">Investigation</th>
                            <th style="width: 14%;">Result</th>
                            <th style="width: 14%;">Reference Value</th>
                            <th style="width: 14%;">Unit</th>
                            <th style="width: 10%;">Flag</th>
                            <th style="width: 14%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($group->tests as $t)
                            @php
                                $abnormal = abnormalPdfLabel($t);
                            @endphp
                            <tr class="{{ $t->is_abnormal ? 'abnormal-row' : '' }}">
                                <td class="test-name">{{ $t->test_name }}</td>
                                <td>{{ $t->result_value ?? '-' }}</td>
                                <td>{{ formatRefRange($t->ref_min, $t->ref_max) }}</td>
                                <td>{{ $t->unit ?? '-' }}</td>
                                <td class="{{ $abnormal['class'] }}">{{ $abnormal['label'] }}</td>
                                <td>{{ ucfirst($t->status ?? '-') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="muted">No tests in this panel.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @empty
            <div class="muted">No grouped results found.</div>
        @endforelse
    </div>

    {{-- Footer --}}
    <div class="footer-box">
        <table class="footer-table">
            <tr>
                <td class="note-block">
                    <div class="small-note">
                        This is a computer-generated laboratory report.<br>
                        Please contact the laboratory for clarification if required.<br>
                        Unauthorized alteration of this report is prohibited.
                    </div>
                </td>
                <td class="approval-block">
                    <div class="signature-wrap">
                        <div class="signature-line">
                            <div class="approved-name">{{ $labOrder->approver?->name ?? '-' }}</div>
                            <div class="approved-role">Approved By</div>
                            <div class="approved-date">
                                {{ $labOrder->approved_at ? $labOrder->approved_at->format('Y-m-d h:i A') : '-' }}
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="page-footer">
            Generated on {{ now()->format('Y-m-d h:i A') }}
        </div>
    </div>

</div>
</body>
</html>