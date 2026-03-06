<?php

namespace App\Http\Controllers;

use App\Models\LabOrder;
use App\Models\LabOrderTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabResultController extends Controller
{
    public function index(LabOrder $labOrder)
    {
        $labOrder->load([
            'patient',
            'groups.testGroup',
            'groups.tests.test',   // LabOrderGroup -> tests (LabOrderTest) -> test (Test)
        ]);

        return view('pages.lab_results.index', compact('labOrder'));
    }

    public function updateResult(Request $request, LabOrderTest $labOrderTest)
    {
        // Load relations needed for validation + abnormal check
        $labOrderTest->load(['test', 'order']);

        if (!in_array($labOrderTest->order->status, ['pending', 'in_progress'], true)) {
            return back()->with('error', 'Results can only be entered for Pending / In Progress orders.');
        }

        // Validate based on test data_type
        $rules = [
            'result_value' => ['nullable', 'string', 'max:100'],
        ];

        if ($labOrderTest->test?->data_type === 'numeric') {
            // allow decimals, but still store as string
            $rules['result_value'] = ['nullable', 'numeric'];
        }

        $data = $request->validate($rules);

        return DB::transaction(function () use ($labOrderTest, $data) {
            $value = $data['result_value'] ?? null;

            // Compute abnormal if numeric and ranges exist
            $isAbnormal = false;

            if ($labOrderTest->test?->data_type === 'numeric' && !is_null($value) && is_numeric($value)) {
                $num = (float) $value;

                if (!is_null($labOrderTest->ref_min) && $num < (float) $labOrderTest->ref_min) {
                    $isAbnormal = true;
                }
                if (!is_null($labOrderTest->ref_max) && $num > (float) $labOrderTest->ref_max) {
                    $isAbnormal = true;
                }
            }

            // Save
            $labOrderTest->update([
                'result_value' => $value,
                'is_abnormal' => $isAbnormal,
                'status' => $value === null || $value === '' ? 'pending' : 'entered',
                'entered_by' => $value === null || $value === '' ? null : auth()->id(),
                'entered_at' => $value === null || $value === '' ? null : now(),
                // keep verification as-is; do not auto-clear
            ]);

            // Ensure order moves to in_progress once results start coming in
            if ($labOrderTest->order->status === 'pending' && ($value !== null && $value !== '')) {
                $labOrderTest->order->update(['status' => 'in_progress']);
            }

            return back()->with('success', 'Result saved.');
        });
    }

    public function verify(LabOrderTest $labOrderTest)
    {
        $labOrderTest->load(['order']);

        if (!in_array($labOrderTest->order->status, ['pending', 'in_progress', 'completed'], true)) {
            return back()->with('error', 'This order cannot be verified in its current state.');
        }

        if (empty($labOrderTest->result_value)) {
            return back()->with('error', 'Enter a result before verifying.');
        }

        return DB::transaction(function () use ($labOrderTest) {

            $labOrderTest->update([
                'status' => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            // If ALL tests under the order are verified -> mark order completed
            $order = $labOrderTest->order;

            $allVerified = $order->tests()
                ->where('status', '!=', 'verified')
                ->doesntExist();

            if ($allVerified) {
                $order->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            } else {
                // If not all verified, keep at least in_progress
                if ($order->status === 'pending') {
                    $order->update(['status' => 'in_progress']);
                }
            }

            return back()->with('success', 'Test verified.');
        });
    }
}