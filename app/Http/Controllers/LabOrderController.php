<?php

namespace App\Http\Controllers;

use App\Models\LabOrder;
use App\Models\LabOrderGroup;
use App\Models\LabOrderTest;
use App\Models\Patient;
use App\Models\TestGroup;
use App\Models\TestReferenceRange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Notification;

 use App\Models\QrToken;

 use Illuminate\Support\Facades\Mail;
use App\Mail\ReportReadyMail;


class LabOrderController extends Controller
{
    /**
     * Calculate patient age in years (nullable if DOB missing/invalid).
     */
    private function patientAge(Patient $patient): ?int
    {
        if (empty($patient->dob)) {
            return null;
        }

        try {
            return Carbon::parse($patient->dob)->age;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Resolve the best reference range for a test based on patient gender + age.
     * Assumes gender values in DB are 'male'/'female'/'any' (case-insensitive).
     */
    private function resolveReferenceRange(int $testId, ?string $gender, ?int $age): ?TestReferenceRange
    {
        $gender = strtolower(trim((string) $gender));
        $gender = in_array($gender, ['male', 'female'], true) ? $gender : 'any';

        $q = TestReferenceRange::query()
            ->where('test_id', $testId)
            ->where(function ($x) use ($gender) {
                $x->where('gender', $gender)->orWhere('gender', 'any');
            });

        if (!is_null($age)) {
            $q->where(function ($x) use ($age) {
                $x->whereNull('age_min')->orWhere('age_min', '<=', $age);
            })->where(function ($x) use ($age) {
                $x->whereNull('age_max')->orWhere('age_max', '>=', $age);
            });
        } else {
            // age unknown -> prefer generic rows without age band
            $q->whereNull('age_min')->whereNull('age_max');
        }

        return $q->orderByRaw("CASE WHEN gender = ? THEN 0 ELSE 1 END", [$gender]) // exact gender first
            ->orderByRaw("(COALESCE(age_max, 999) - COALESCE(age_min, 0)) ASC")     // narrower band first
            ->orderByDesc('id')                                                     // latest wins
            ->first();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = LabOrder::query()
                ->with(['patient'])
                ->select('lab_orders.*');

            if ($request->filled('order_number')) {
                $query->where('order_number', 'like', '%' . $request->order_number . '%');
            }

            if ($request->filled('patient_id')) {
                $query->where('patient_id', $request->patient_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->addColumn('patient_name', fn ($o) => $o->patient?->full_name ?? $o->patient?->name ?? '-')
                ->addColumn('status_badge', fn ($o) => view('pages.lab_orders.partials.status', ['order' => $o])->render())
                ->addColumn('action', fn ($o) => view('pages.lab_orders.partials.actions', ['order' => $o])->render())
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        $patients = Patient::orderBy('full_name')->get(['id', 'full_name', 'patient_code']);

        return view('pages.lab_orders.index', compact('patients'));
    }

    public function create()
    {
        $patients = Patient::orderBy('full_name')->get(['id', 'full_name', 'patient_code']);

        $groups = TestGroup::withCount('tests')
            ->with(['tests:id,name'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'status']);

        return view('pages.lab_orders.create', compact('patients', 'groups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'group_ids' => ['required', 'array', 'min:1'],
            'group_ids.*' => ['integer', 'exists:test_groups,id'],
        ]);

        return DB::transaction(function () use ($data) {

            // ensure unique order number
            do {
                $orderNumber = 'LO-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            } while (LabOrder::where('order_number', $orderNumber)->exists());

            $order = LabOrder::create([
                'patient_id' => $data['patient_id'],
                'created_by' => auth()->id(),
                'order_number' => $orderNumber,
                'status' => 'pending',
                'total_amount' => 0,
                'notes' => $data['notes'] ?? null,
            ]);

            $patient = Patient::findOrFail($data['patient_id']);
            $age = $this->patientAge($patient);
            $gender = $patient->gender ?? 'any';

            // load selected groups + tests
            $groups = TestGroup::with(['tests:id,name,unit'])
                ->whereIn('id', $data['group_ids'])
                ->orderBy('name')
                ->get(['id', 'name', 'price']);

            $total = 0;
            $insertedTestIds = [];

            foreach ($groups as $group) {
                $total += (float)($group->price ?? 0);

                $orderGroup = LabOrderGroup::create([
                    'lab_order_id' => $order->id,
                    'test_group_id' => $group->id,
                    'group_price_snapshot' => $group->price ?? 0,
                ]);

                foreach ($group->tests as $test) {
                    if (isset($insertedTestIds[$test->id])) {
                        continue;
                    }

                    $range = $this->resolveReferenceRange($test->id, $gender, $age);

                    LabOrderTest::create([
                        'lab_order_id' => $order->id,
                        'lab_order_group_id' => $orderGroup->id,
                        'test_id' => $test->id,

                        // snapshots
                        'test_name' => $test->name,
                        'unit' => $test->unit,

                        // ✅ snapshot range NOW (so abnormal works)
                        'test_reference_range_id' => $range?->id,
                        'ref_min' => $range?->ref_min,
                        'ref_max' => $range?->ref_max,

                        'status' => 'pending',
                        'is_abnormal' => false,

                        'result_value' => null,
                        'entered_by' => null,
                        'entered_at' => null,
                        'verified_by' => null,
                        'verified_at' => null,
                    ]);

                    $insertedTestIds[$test->id] = true;
                }
            }

            $order->update(['total_amount' => $total]);

            return redirect()
                ->route('lab-orders.index')
                ->with('success', 'Lab Order created successfully.');
        });
    }

    public function show(LabOrder $labOrder)
    {
        $labOrder->load([
            'patient',
            'groups.testGroup',
            'groups.tests.test',
            'tests.test',
            'samples',
            'approver',
            'qrToken',
        ]);

        return view('pages.lab_orders.show', compact('labOrder'));
    }

    public function edit(LabOrder $labOrder)
    {
        if ($labOrder->status !== 'pending') {
            return redirect()
                ->route('lab-orders.index')
                ->with('error', 'Only pending orders can be edited.');
        }

        $hasAnyProgress = $labOrder->tests()
            ->whereNotNull('result_value')
            ->orWhereNotNull('entered_at')
            ->orWhereNotNull('verified_at')
            ->exists();

        $patients = Patient::orderBy('full_name')->get(['id', 'full_name', 'patient_code']);

        $groups = TestGroup::withCount('tests')
            ->with(['tests:id,name'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'status']);

        $selectedGroupIds = $labOrder->groups()->pluck('test_group_id')->toArray();

        return view('pages.lab_orders.edit', compact(
            'labOrder',
            'patients',
            'groups',
            'selectedGroupIds',
            'hasAnyProgress'
        ));
    }

    public function update(Request $request, LabOrder $labOrder)
    {
        if ($labOrder->status !== 'pending') {
            return redirect()
                ->route('lab-orders.index')
                ->with('error', 'Only pending orders can be updated.');
        }

        $hasAnyProgress = $labOrder->tests()
            ->whereNotNull('result_value')
            ->orWhereNotNull('entered_at')
            ->orWhereNotNull('verified_at')
            ->exists();

        $rules = [
            'patient_id' => ['required', 'exists:patients,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'approved'])],
        ];

        if (!$hasAnyProgress) {
            $rules['group_ids'] = ['required', 'array', 'min:1'];
            $rules['group_ids.*'] = ['integer', 'exists:test_groups,id'];
        }

        $data = $request->validate($rules);

        return DB::transaction(function () use ($labOrder, $data, $hasAnyProgress) {

            if (!$hasAnyProgress && isset($data['group_ids'])) {

                $labOrder->tests()->delete();
                $labOrder->groups()->delete();

                $patient = Patient::findOrFail($data['patient_id']);
                $age = $this->patientAge($patient);
                $gender = $patient->gender ?? 'any';

                $groups = TestGroup::with(['tests:id,name,unit'])
                    ->whereIn('id', $data['group_ids'])
                    ->orderBy('name')
                    ->get(['id', 'name', 'price']);

                $total = 0;
                $insertedTestIds = [];

                foreach ($groups as $group) {
                    $total += (float)($group->price ?? 0);

                    $orderGroup = LabOrderGroup::create([
                        'lab_order_id' => $labOrder->id,
                        'test_group_id' => $group->id,
                        'group_price_snapshot' => $group->price ?? 0,
                    ]);

                    foreach ($group->tests as $test) {
                        if (isset($insertedTestIds[$test->id])) continue;

                        $range = $this->resolveReferenceRange($test->id, $gender, $age);

                        LabOrderTest::create([
                            'lab_order_id' => $labOrder->id,
                            'lab_order_group_id' => $orderGroup->id,
                            'test_id' => $test->id,

                            'test_name' => $test->name,
                            'unit' => $test->unit,

                            // ✅ snapshot range NOW
                            'test_reference_range_id' => $range?->id,
                            'ref_min' => $range?->ref_min,
                            'ref_max' => $range?->ref_max,

                            'status' => 'pending',
                            'is_abnormal' => false,

                            'result_value' => null,
                            'entered_by' => null,
                            'entered_at' => null,
                            'verified_by' => null,
                            'verified_at' => null,
                        ]);

                        $insertedTestIds[$test->id] = true;
                    }
                }

                $labOrder->update(['total_amount' => $total]);
            }

            $labOrder->update([
                'patient_id' => $data['patient_id'],
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'],
            ]);

            return redirect()
                ->route('lab-orders.index')
                ->with('success', 'Lab Order updated successfully.');
        });
    }

    public function destroy(LabOrder $labOrder)
    {
        if ($labOrder->status !== 'pending') {
            return redirect()
                ->route('lab-orders.index')
                ->with('error', 'Only pending orders can be deleted.');
        }

        $labOrder->delete();

        return redirect()
            ->route('lab-orders.index')
            ->with('success', 'Lab Order deleted successfully.');
    }

    public function approve(LabOrder $labOrder)
    {
        $labOrder->load(['tests', 'patient', 'qrToken']);

        if ($labOrder->status === 'approved') {
            return redirect()
                ->route('lab-orders.show', $labOrder)
                ->with('error', 'This report is already approved.');
        }

        $hasTests = $labOrder->tests->isNotEmpty();
        $allVerified = $hasTests && $labOrder->tests->every(fn ($t) => $t->status === 'verified');

        if (!$allVerified || $labOrder->status !== 'completed') {
            return redirect()
                ->route('lab-orders.show', $labOrder)
                ->with('error', 'Only completed orders with all tests verified can be approved.');
        }

        $labOrder->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->ensureQrToken($labOrder);

        $labOrder->load(['qrToken', 'patient']);

        $this->queuePatientNotifications($labOrder);

        return redirect()
            ->route('lab-orders.show', $labOrder)
            ->with('success', 'Report approved successfully and notifications were queued.');
    }

    private function ensureQrToken(LabOrder $labOrder): void
    {
        if (!$labOrder->qrToken) {
            \App\Models\QrToken::create([
                'lab_order_id' => $labOrder->id,
                'token' => \Illuminate\Support\Str::random(64),
                'is_active' => true,
                'expires_at' => null,
            ]);
        }
    }

    private function queuePatientNotifications(LabOrder $labOrder): void
    {
        $patient = $labOrder->patient;

        if (!$patient) {
            return;
        }

        $publicUrl = $labOrder->qrToken
            ? route('public-reports.show', $labOrder->qrToken->token)
            : null;

        $message = "Your laboratory report (Order {$labOrder->order_number}) is ready. "
            . ($publicUrl ? "Access it here: {$publicUrl}" : "Please collect it from the laboratory.");

        if (!empty($patient->phone)) {
            Notification::create([
                'patient_id' => $patient->id,
                'lab_order_id' => $labOrder->id,
                'channel' => 'sms',
                'status' => 'pending',
                'message' => $message,
            ]);
        }

        if (!empty($patient->email)) {
            $emailNotification = Notification::create([
                'patient_id' => $patient->id,
                'lab_order_id' => $labOrder->id,
                'channel' => 'email',
                'status' => 'pending',
                'message' => $message,
            ]);

            try {
                Mail::to($patient->email)->send(
                    new ReportReadyMail(
                        $patient,
                        $labOrder->order_number,
                        $publicUrl
                    )
                );

                $emailNotification->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'provider_response' => 'Email sent successfully',
                ]);
            } catch (\Exception $e) {
                $emailNotification->update([
                    'status' => 'failed',
                    'provider_response' => $e->getMessage(),
                ]);
            }
        }
    }
    }