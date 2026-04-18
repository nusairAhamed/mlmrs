<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    // Human-readable field labels
    public static array $fieldLabels = [
        'patient_name'    => 'Patient',
        'panels'          => 'Panels',
        'samples'         => 'Samples Collected',
        'status'          => 'Status',
        'total_amount'    => 'Total Amount',
        'approved_by_name'=> 'Approved By',
        'result_value'    => 'Result Value',
        'is_abnormal'  => 'Abnormal Flag',
        'entered_by'   => 'Entered By',
        'verified_by'  => 'Verified By',
        'approved_by'  => 'Approved By',
        'approved_at'  => 'Approved At',
        'notes'        => 'Notes',
        'full_name'    => 'Full Name',
        'dob'          => 'Date of Birth',
        'gender'       => 'Gender',
        'phone'        => 'Phone',
        'email'        => 'Email',
        'address'      => 'Address',
        'role_id'      => 'Role',
        'name'         => 'Name',
        'total_amount' => 'Total Amount',
    ];

    // Human-readable status values
    public static array $statusLabels = [
        'pending'         => 'Pending',
        'sample_collected'=> 'Sample Collected',
        'in_progress'     => 'In Progress',
        'pending_review'  => 'Pending Review',
        'on_hold'         => 'On Hold',
        'approved'        => 'Approved',
        'cancelled'       => 'Cancelled',
        'entered'         => 'Entered',
        'verified'        => 'Verified',
    ];

    public function index(Request $request)
    {
        $modelMap = [
            'LabOrder'     => \App\Models\LabOrder::class,
            'LabOrderTest' => \App\Models\LabOrderTest::class,
            'Patient'      => \App\Models\Patient::class,
            'User'         => \App\Models\User::class,
        ];

        $query = Audit::with(['user', 'auditable'])
            ->where(function ($q) {
                $q->whereRaw("JSON_LENGTH(new_values) > 0")
                  ->orWhereRaw("JSON_LENGTH(old_values) > 0");
            })
            ->orderByDesc('created_at');

        if ($request->filled('model')) {
            $query->where('auditable_type', $modelMap[$request->model] ?? $request->model);
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $audits = $query->paginate(50)->withQueryString();

        // Group consecutive LabOrderTest entries that share the same user + order + minute
        $rows = [];
        $lastGroupKey = null;

        foreach ($audits->items() as $audit) {
            if ($audit->auditable_type === \App\Models\LabOrderTest::class) {
                $orderId  = $audit->auditable?->lab_order_id ?? 0;
                $groupKey = ($audit->user_id ?? 0)
                    . '_' . $orderId
                    . '_' . $audit->event
                    . '_' . $audit->created_at->format('YmdHi');

                if ($groupKey === $lastGroupKey) {
                    $rows[count($rows) - 1]['items'][] = $audit;
                    continue;
                }

                $lastGroupKey = $groupKey;
                $rows[] = ['type' => 'test_group', 'items' => [$audit], 'order_id' => $orderId];
            } else {
                $lastGroupKey = null;
                $rows[] = ['type' => 'single', 'items' => [$audit]];
            }
        }

        $users = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('pages.audit_logs.index', compact('audits', 'rows', 'users', 'modelMap'));
    }
}
