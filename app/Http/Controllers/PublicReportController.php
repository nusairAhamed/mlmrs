<?php

namespace App\Http\Controllers;

use App\Models\QrToken;
use Carbon\Carbon;

class PublicReportController extends Controller
{
    public function show(string $token)
    {
        $qrToken = QrToken::with([
            'labOrder.patient',
            'labOrder.approver',
            'labOrder.groups.testGroup',
            'labOrder.groups.tests.test',
        ])
        ->where('token', $token)
        ->where('is_active', true)
        ->first();

        if (!$qrToken) {
            abort(404, 'Invalid report link.');
        }

        if (!is_null($qrToken->expires_at) && now()->gt($qrToken->expires_at)) {
            abort(403, 'This report link has expired.');
        }

        $labOrder = $qrToken->labOrder;

        if (!$labOrder || $labOrder->status !== 'approved') {
            abort(403, 'This report is not available.');
        }

        $patient = $labOrder->patient;

        $age = null;
        if (!empty($patient?->dob)) {
            try {
                $age = Carbon::parse($patient->dob)->age;
            } catch (\Throwable $e) {
                $age = null;
            }
        }

        return view('pages.lab_reports.public', compact('labOrder', 'patient', 'age', 'qrToken'));
    }
}