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
use App\Models\StaffNotification;

 use Illuminate\Support\Facades\Mail;
use App\Mail\ReportReadyMail;
use App\Mail\ReportHoldMail;


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
                ->addColumn('patient_name', fn ($o) => ($o->patient?->patient_code ? $o->patient->patient_code . ' — ' : '') . ($o->patient?->full_name ?? '-'))
                ->addColumn('status_badge', fn ($o) => view('pages.lab_orders.partials.status', ['order' => $o])->render())
                ->addColumn('action', fn ($o) => view('pages.lab_orders.partials.actions', ['order' => $o])->render())
                ->editColumn('created_at', fn ($o) => $o->created_at?->format('d M Y, h:i A'))
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        $patients = Patient::orderBy('full_name')->get(['id', 'full_name', 'patient_code']);

        $scannedPatient = null;
        if ($request->filled('patient_id')) {
            $scannedPatient = Patient::find($request->patient_id);
        }

        return view('pages.lab_orders.index', compact('patients', 'scannedPatient'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('full_name')->get(['id', 'full_name', 'patient_code']);

        $groups = TestGroup::withCount('tests')
            ->with(['tests:id,name'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'status']);

        $preselectedPatientId = $request->query('patient_id');

        return view('pages.lab_orders.create', compact('patients', 'groups', 'preselectedPatientId'));
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

            // Patch the creation audit with panels+tests + final amount as a frozen snapshot
            $creationAudit = $order->audits()->where('event', 'created')->latest()->first();
            if ($creationAudit) {
                $order->load('groups.testGroup', 'groups.tests.test');
                $panelsSnapshot = $order->groups->map(fn($g) => [
                    'name'  => $g->testGroup?->name ?? 'Unknown Panel',
                    'tests' => $g->tests->map(fn($t) => $t->test_name)->filter()->values()->toArray(),
                ])->values()->toArray();

                $newValues = $creationAudit->new_values ?? [];
                $newValues['panels']       = $panelsSnapshot;
                $newValues['total_amount'] = (string) $total;
                $creationAudit->new_values = $newValues;
                $creationAudit->save();
            }

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
            'groups.tests.verifiedBy',
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
            ->where(function ($q) {
                $q->whereNotNull('result_value')
                  ->orWhereNotNull('entered_at')
                  ->orWhereNotNull('verified_at');
            })
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
            ->where(function ($q) {
                $q->whereNotNull('result_value')
                  ->orWhereNotNull('entered_at')
                  ->orWhereNotNull('verified_at');
            })
            ->exists();

        $rules = [
            'patient_id' => ['required', 'exists:patients,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['pending', 'sample_collected', 'in_progress', 'pending_review', 'on_hold', 'approved', 'sample_rejected', 'cancelled'])],
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

        if (!$allVerified || !in_array($labOrder->status, ['pending_review', 'on_hold'])) {
            return redirect()
                ->route('lab-orders.show', $labOrder)
                ->with('error', 'Only orders in Pending Review or On Hold with all tests verified can be approved.');
        }

        $labOrder->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->ensureQrToken($labOrder);

        $labOrder->load(['qrToken', 'patient']);

        // Clear the "pending review" notifications now that someone approved it —
        // prevents all other techs/admins from seeing a stale action item.
        StaffNotification::where('lab_order_id', $labOrder->id)
            ->where('type', 'pending_review')
            ->delete();

        $this->queuePatientNotifications($labOrder);
        $this->createStaffNotification($labOrder, 'report_approved');

        return redirect()
            ->route('lab-orders.show', $labOrder)
            ->with('success', 'Report approved successfully and notifications were queued.');
    }

    public function hold(LabOrder $labOrder)
    {
        $labOrder->load(['tests']);

        if (!in_array($labOrder->status, ['pending_review'])) {
            return redirect()
                ->route('lab-orders.show', $labOrder)
                ->with('error', 'Only orders in Pending Review can be placed on hold.');
        }

        $hasTests = $labOrder->tests->isNotEmpty();
        $allVerified = $hasTests && $labOrder->tests->every(fn ($t) => $t->status === 'verified');

        if (!$allVerified) {
            return redirect()
                ->route('lab-orders.show', $labOrder)
                ->with('error', 'All tests must be verified before placing the report on hold.');
        }

        $labOrder->update(['status' => 'on_hold']);

        $labOrder->load(['patient']);
        $this->queueHoldNotifications($labOrder);
        $this->createStaffNotification($labOrder, 'report_on_hold');

        return redirect()
            ->route('lab-orders.show', $labOrder)
            ->with('success', 'Report placed on hold. Patient has been notified to collect the report in person.');
    }

    public function cancel(LabOrder $labOrder)
    {
        if ($labOrder->status !== 'pending') {
            return redirect()
                ->route('lab-orders.show', $labOrder)
                ->with('error', 'Only pending orders can be cancelled.');
        }

        $labOrder->update(['status' => 'cancelled']);

        return redirect()
            ->route('lab-orders.index')
            ->with('success', 'Lab Order cancelled successfully.');
    }

    private function ensureQrToken(LabOrder $labOrder): void
    {
        if (!$labOrder->qrToken) {
            \App\Models\QrToken::create([
                'lab_order_id' => $labOrder->id,
                'token' => \Illuminate\Support\Str::random(64),
                'is_active' => true,
                'expires_at' => now()->addDays(30),
            ]);
        }
    }

    private function createStaffNotification(LabOrder $labOrder, string $type): void
    {
        $patient = $labOrder->patient;
        $name    = $patient?->full_name ?? 'Unknown';

        [$title, $message] = match ($type) {
            'report_approved' => [
                'Report Approved — Ready for Collection',
                "Report {$labOrder->order_number} for {$name} has been approved. Patient has been notified via email.",
            ],
            'report_on_hold' => [
                'Report On Hold — Requires In-Person Delivery',
                "Report {$labOrder->order_number} for {$name} is on hold. Patient was asked to visit. Handle with care.",
            ],
            default => ['Notification', ''],
        };

        StaffNotification::create([
            'target_role'  => 'All',  // visible to Admin + Receptionist
            'lab_order_id' => $labOrder->id,
            'type'         => $type,
            'title'        => $title,
            'message'      => $message,
        ]);
    }

    private function queueHoldNotifications(LabOrder $labOrder): void
    {
        $patient = $labOrder->patient;

        if (!$patient) {
            return;
        }

        $message = "Your laboratory report (Order {$labOrder->order_number}) is ready. Please visit the laboratory to collect your report.";

        if (!empty($patient->phone)) {
            Notification::create([
                'patient_id'   => $patient->id,
                'lab_order_id' => $labOrder->id,
                'channel'      => 'sms',
                'status'       => 'pending',
                'message'      => $message,
            ]);
        }

        if (!empty($patient->email)) {
            $emailNotification = Notification::create([
                'patient_id'   => $patient->id,
                'lab_order_id' => $labOrder->id,
                'channel'      => 'email',
                'status'       => 'pending',
                'message'      => $message,
            ]);

            try {
                Mail::to($patient->email)->send(
                    new ReportHoldMail($patient, $labOrder->order_number)
                );

                $emailNotification->update([
                    'status'            => 'sent',
                    'sent_at'           => now(),
                    'provider_response' => 'Email sent successfully',
                ]);
            } catch (\Exception $e) {
                $emailNotification->update([
                    'provider_response' => $e->getMessage(),
                ]);
            }
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