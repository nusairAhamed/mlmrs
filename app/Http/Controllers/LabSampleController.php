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

        if (!in_array($labOrder->status, ['pending', 'sample_collected', 'sample_rejected'], true)) {
            return redirect()
                ->route('lab-orders.show', $labOrder)
                ->with('error', 'Samples can only be added for Pending, Sample Collected, or Sample Rejected orders.');
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
        if (!in_array($labOrder->status, ['pending', 'sample_collected', 'sample_rejected'], true)) {
            return redirect()
                ->route('lab-orders.show', $labOrder)
                ->with('error', 'Samples can only be added for Pending, Sample Collected, or Sample Rejected orders.');
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

            // move order to sample_collected after first sample generation
            if (in_array($labOrder->status, ['pending', 'sample_rejected'], true)) {
                $labOrder->update(['status' => 'sample_collected']);
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

            // Patch the status-change audit with a frozen sample snapshot
            $statusAudit = $labOrder->audits()
                ->where('event', 'updated')
                ->whereRaw("JSON_EXTRACT(new_values, '$.status') = 'sample_collected'")
                ->latest()
                ->first();

            if ($statusAudit) {
                $labOrder->load('samples');
                $samplesSnapshot = $labOrder->samples->map(fn($s) => [
                    'code'         => $s->sample_code,
                    'type'         => $s->sample_type,
                    'collected_at' => $s->collected_at?->format('d M Y, h:i A'),
                ])->values()->toArray();

                $newValues = $statusAudit->new_values ?? [];
                $newValues['samples'] = $samplesSnapshot;
                $statusAudit->new_values = $newValues;
                $statusAudit->save();
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

    public function printAll(LabOrder $labOrder)
    {
        $labOrder->load(['patient', 'samples']);

        if ($labOrder->samples->isEmpty()) {
            return redirect()
                ->route('lab-orders.samples.index', $labOrder)
                ->with('error', 'No samples to print.');
        }

        return view('pages.lab_samples.print_all', compact('labOrder'));
    }

    public function scanForm()
    {
        return view('pages.lab_samples.scan');
    }

    public function scan(Request $request)
    {
        $data = $request->validate([
            'sample_code' => ['required', 'string', 'max:100'],
        ]);

        $sample = LabSample::where('sample_code', trim($data['sample_code']))->first();

        if (!$sample) {
            return back()
                ->withInput()
                ->withErrors(['sample_code' => 'No sample found with code: ' . $data['sample_code']]);
        }

        if ($sample->status === 'rejected') {
            return back()
                ->withInput()
                ->withErrors(['sample_code' => 'Sample ' . $sample->sample_code . ' has been rejected and cannot be processed.']);
        }

        // Mark as received the first time it is scanned at the lab
        if ($sample->status === 'collected') {
            $sample->update([
                'status'      => 'received',
                'received_at' => now(),
            ]);
        }

        return redirect()
            ->route('lab-orders.results.index', $sample->lab_order_id)
            ->with('success', 'Sample ' . $sample->sample_code . ' received. Enter results below.');
    }
}