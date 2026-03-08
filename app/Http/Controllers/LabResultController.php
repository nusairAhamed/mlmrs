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
            'tests.test',
            'groups.testGroup',
            'groups.tests.test',
        ]);

        return view('pages.lab_results.index', compact('labOrder'));
    }

    public function bulkUpdateResults(Request $request, LabOrder $labOrder)
    {
        $labOrder->load(['tests.test']);

        if (!in_array($labOrder->status, ['pending', 'in_progress', 'completed'], true)) {
            return back()->with('error', 'Results can only be edited for Pending, In Progress, or Completed orders.');
        }

        if ($labOrder->status === 'approved') {
            return back()->with('error', 'Approved orders cannot be edited.');
        }

        $payload = $request->input('results', []);

        return DB::transaction(function () use ($labOrder, $payload) {
            $updatedCount = 0;
            $reverifyCount = 0;

            foreach ($labOrder->tests as $labOrderTest) {
                if ($labOrderTest->status === 'verified') {
                    continue; // locked after verification
                }

                if (!array_key_exists($labOrderTest->id, $payload)) {
                    continue;
                }

                $rawValue = $payload[$labOrderTest->id];
                $value = is_string($rawValue) ? trim($rawValue) : $rawValue;

                if ($value === '') {
                    $value = null;
                }

                $dataType = $labOrderTest->test?->data_type ?? 'text';

                if ($dataType === 'numeric' && !is_null($value) && !is_numeric($value)) {
                    return back()->with('error', "Invalid numeric value for {$labOrderTest->test_name}.");
                }

                if ($dataType === 'text' && !is_null($value) && mb_strlen((string) $value) > 100) {
                    return back()->with('error', "Result for {$labOrderTest->test_name} is too long.");
                }

                $oldValue = $labOrderTest->result_value;
                $wasVerified = !is_null($labOrderTest->verified_at) || !is_null($labOrderTest->verified_by);
                $valueChanged = (string) ($oldValue ?? '') !== (string) ($value ?? '');

                $isAbnormal = false;

                if ($dataType === 'numeric' && !is_null($value) && is_numeric($value)) {
                    $num = (float) $value;

                    if (!is_null($labOrderTest->ref_min) && $num < (float) $labOrderTest->ref_min) {
                        $isAbnormal = true;
                    }

                    if (!is_null($labOrderTest->ref_max) && $num > (float) $labOrderTest->ref_max) {
                        $isAbnormal = true;
                    }
                }

                $updateData = [
                    'result_value' => $value,
                    'is_abnormal' => $isAbnormal,
                    'status' => is_null($value) ? 'pending' : 'entered',
                    'entered_by' => is_null($value) ? null : auth()->id(),
                    'entered_at' => is_null($value) ? null : now(),
                ];

                if ($wasVerified && $valueChanged) {
                    $updateData['verified_by'] = null;
                    $updateData['verified_at'] = null;
                    $reverifyCount++;
                }

                if (is_null($value)) {
                    $updateData['verified_by'] = null;
                    $updateData['verified_at'] = null;
                }

                $labOrderTest->update($updateData);
                $updatedCount++;
            }

            $this->refreshOrderStatus($labOrder);

            $message = "{$updatedCount} result(s) saved.";
            if ($reverifyCount > 0) {
                $message .= " {$reverifyCount} previously verified result(s) were reset and need re-verification.";
            }

            return back()->with('success', $message);
        });
    }

    public function bulkVerify(Request $request, LabOrder $labOrder)
    {
        $labOrder->load('tests');

        if (!in_array($labOrder->status, ['pending', 'in_progress', 'completed'], true)) {
            return back()->with('error', 'This order cannot be verified in its current state.');
        }

        if ($labOrder->status === 'approved') {
            return back()->with('error', 'Approved orders cannot be modified.');
        }

        $verifyIds = $request->input('verify_ids', []);

        if (!is_array($verifyIds) || empty($verifyIds)) {
            return back()->with('error', 'Select at least one test to verify.');
        }

        return DB::transaction(function () use ($labOrder, $verifyIds) {
            $selectedTests = $labOrder->tests()->whereIn('id', $verifyIds)->get();

            $verifiedCount = 0;
            $skippedCount = 0;

            foreach ($selectedTests as $test) {
                $hasResult = !is_null($test->result_value) && $test->result_value !== '';

                if (!$hasResult || $test->status === 'verified') {
                    $skippedCount++;
                    continue;
                }

                $test->update([
                    'status' => 'verified',
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);

                $verifiedCount++;
            }

            $this->refreshOrderStatus($labOrder);

            if ($verifiedCount === 0) {
                return back()->with('error', 'No selected tests were eligible for verification.');
            }

            $message = "{$verifiedCount} test(s) verified.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} test(s) were skipped.";
            }

            return back()->with('success', $message);
        });
    }

    private function refreshOrderStatus(LabOrder $order): void
    {
        $order->load('tests');

        $tests = $order->tests;

        if ($tests->isEmpty()) {
            $order->update([
                'status' => 'pending',
                'completed_at' => null,
            ]);
            return;
        }

        $allPending = $tests->every(function ($t) {
            return $t->status === 'pending' && (is_null($t->result_value) || $t->result_value === '');
        });

        $allVerified = $tests->every(function ($t) {
            return $t->status === 'verified';
        });

        if ($allVerified) {
            $order->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            return;
        }

        if ($allPending) {
            $order->update([
                'status' => 'pending',
                'completed_at' => null,
            ]);
            return;
        }

        $order->update([
            'status' => 'in_progress',
            'completed_at' => null,
        ]);
    }
}