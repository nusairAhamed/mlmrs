<?php

namespace App\Http\Controllers;

use App\Models\LabOrder;
use Carbon\Carbon;

use Barryvdh\DomPDF\Facade\Pdf;


class LabReportController extends Controller
{
    public function show(LabOrder $labOrder)
    {
        $labOrder->load([
            'patient',
            'approver',
            'groups.testGroup',
            'groups.tests.test',
        ]);

        if ($labOrder->status !== 'approved') {
            return redirect()
                ->route('lab-orders.show', $labOrder)
                ->with('error', 'Only approved orders can be viewed as final reports.');
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

        return view('pages.lab_reports.show', compact('labOrder', 'patient', 'age'));
    }

    public function downloadPdf(LabOrder $labOrder)
{
    $labOrder->load([
    'patient',
    'approver',
    'qrToken',
    'groups.testGroup',
    'groups.tests.test',
]);
    if ($labOrder->status !== 'approved') {
        return redirect()
            ->route('lab-orders.show', $labOrder)
            ->with('error', 'Only approved orders can be downloaded as final reports.');
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

    $pdf = Pdf::loadView('pages.lab_reports.pdf', compact('labOrder', 'patient', 'age'))
        ->setPaper('a4', 'portrait');

    return $pdf->download("Lab-Report-{$labOrder->order_number}.pdf");
}
}