<?php

namespace App\Http\Controllers;

use App\Models\LabOrder;
use App\Models\LabSample;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabSampleController extends Controller
{
    private function allowedSampleTypes(): array
    {
        return [
            'blood' => 'Blood',
            'urine' => 'Urine',
            'stool' => 'Stool',
            'sputum' => 'Sputum',
            'swab' => 'Swab',
            'serum' => 'Serum',
            'plasma' => 'Plasma',
        ];
    }

    private function hasLockedSamples(LabOrder $labOrder): bool
    {
        // once samples move forward in workflow, don't allow adding more (audit-safe)
        return $labOrder->samples()
            ->whereIn('status', ['received', 'in_process', 'completed'])
            ->exists();
    }

    public function index(LabOrder $labOrder)
    {
        $labOrder->load(['patient', 'samples']);

        // convenience flag for UI
        $hasLockedSamples = $labOrder->samples->contains(fn ($s) =>
            in_array($s->status, ['received', 'in_process', 'completed'], true)
        );

        return view('pages.lab_samples.index', compact('labOrder', 'hasLockedSamples'));
    }

    public function create(LabOrder $labOrder)
    {
        $labOrder->load(['patient', 'samples']);

        if (!in_array($labOrder->status, ['pending', 'in_progress'], true)) {
            return redirect()
                ->route('lab-orders.show', $labOrder)
                ->with('error', 'Samples can only be generated for Pending / In Progress orders.');
        }

        if ($this->hasLockedSamples($labOrder)) {
            return redirect()
                ->route('lab-orders.samples.index', $labOrder)
                ->with('error', 'Cannot add samples because one or more samples are already received/in process.');
        }

        // ✅ Allow create page both for first-time generate AND add-more
        $sampleTypes = $this->allowedSampleTypes();
        $mode = $labOrder->samples()->exists() ? 'add' : 'generate'; // for UI label if you want

        return view('pages.lab_samples.create', compact('labOrder', 'sampleTypes', 'mode'));
    }

    public function store(Request $request, LabOrder $labOrder)
    {
        if (!in_array($labOrder->status, ['pending', 'in_progress'], true)) {
            return redirect()
                ->route('lab-orders.show', $labOrder)
                ->with('error', 'Samples can only be generated for Pending / In Progress orders.');
        }

        if ($this->hasLockedSamples($labOrder)) {
            return redirect()
                ->route('lab-orders.samples.index', $labOrder)
                ->with('error', 'Cannot add samples because one or more samples are already received/in process.');
        }

        $allowedKeys = array_keys($this->allowedSampleTypes());

        $data = $request->validate([
            'samples' => ['required', 'array', 'min:1'],
            'samples.*.sample_type' => ['required', 'string', 'in:' . implode(',', $allowedKeys)],
            'samples.*.qty' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        // ✅ prevent duplicate types inside the same request
        $types = array_map(fn ($r) => $r['sample_type'], $data['samples']);
        if (count($types) !== count(array_unique($types))) {
            return back()
                ->withInput()
                ->withErrors(['samples' => 'Duplicate sample types are not allowed. Increase Qty instead.']);
        }

        return DB::transaction(function () use ($labOrder, $data) {

            $ts = now();

            // optional: move order to in_progress after first sample generation
            if ($labOrder->status === 'pending') {
                $labOrder->update(['status' => 'in_progress']);
            }

            // stable ordering
            $rows = $data['samples'];
            usort($rows, fn ($a, $b) => strcmp($a['sample_type'], $b['sample_type']));

            // ✅ IMPORTANT: continue sequence (append-only)
            // If order already has N samples, next will be N+1
            $seq = $labOrder->samples()->count() + 1;

            foreach ($rows as $row) {
                $type = $row['sample_type'];
                $qty  = (int) $row['qty'];

                for ($i = 1; $i <= $qty; $i++) {
                    $sampleCode = 'SMP-' . $ts->format('Ymd')
                        . '-' . str_pad((string) $labOrder->id, 6, '0', STR_PAD_LEFT)
                        . '-' . str_pad((string) $seq, 2, '0', STR_PAD_LEFT);

                    LabSample::create([
                        'lab_order_id' => $labOrder->id,
                        'sample_code' => $sampleCode,
                        'sample_type' => $type,
                        'status' => 'collected',
                        'collected_at' => $ts,
                    ]);

                    $seq++;
                }
            }

            return redirect()
                ->route('lab-orders.samples.index', $labOrder)
                ->with('success', 'Samples saved successfully.');
        });
    }

    public function label(LabSample $labSample)
    {
        $labSample->load(['order.patient']);

        return view('pages.lab_samples.label', compact('labSample'));
    }
}