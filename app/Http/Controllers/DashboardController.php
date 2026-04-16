<?php

namespace App\Http\Controllers;

use App\Models\LabOrder;
use App\Models\LabOrderTest;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $role = auth()->user()->role->name ?? null;

        $period = $request->get('period', 'today');
        [$from, $to, $periodLabel] = $this->resolvePeriod($period);

        $stats        = [];
        $orderStats   = $this->orderStatusBreakdown($from, $to);
        $recentOrders = $this->recentOrders($role, $from, $to);

        if ($role === 'Admin') {
            $stats = [
                [
                    'label' => 'Total Patients',
                    'value' => Patient::count(),
                    'color' => 'indigo',
                    'icon'  => 'patients',
                ],
                [
                    'label' => 'Orders (' . $periodLabel . ')',
                    'value' => LabOrder::whereBetween('created_at', [$from, $to])->count(),
                    'color' => 'blue',
                    'icon'  => 'orders',
                ],
                [
                    'label' => 'Pending Approval',
                    'value' => LabOrder::where('status', 'pending_review')->count(),
                    'color' => 'amber',
                    'icon'  => 'approval',
                ],
                [
                    'label' => 'Failed Notifications (' . $periodLabel . ')',
                    'value' => Notification::where('status', 'failed')->whereBetween('created_at', [$from, $to])->count(),
                    'color' => 'red',
                    'icon'  => 'notification',
                ],
                [
                    'label' => 'Total Users',
                    'value' => User::count(),
                    'color' => 'violet',
                    'icon'  => 'users',
                ],
                [
                    'label' => 'Revenue (' . $periodLabel . ')',
                    'value' => number_format(
                        LabOrder::where('status', 'approved')
                            ->whereBetween('approved_at', [$from, $to])
                            ->sum('total_amount'),
                        2
                    ),
                    'color' => 'emerald',
                    'icon'  => 'revenue',
                ],
            ];
        } elseif ($role === 'Receptionist') {
            $stats = [
                [
                    'label' => 'Total Patients',
                    'value' => Patient::count(),
                    'color' => 'indigo',
                    'icon'  => 'patients',
                ],
                [
                    'label' => 'Orders (' . $periodLabel . ')',
                    'value' => LabOrder::whereBetween('created_at', [$from, $to])->count(),
                    'color' => 'blue',
                    'icon'  => 'orders',
                ],
                [
                    'label' => 'Pending Orders',
                    'value' => LabOrder::where('status', 'pending')->count(),
                    'color' => 'amber',
                    'icon'  => 'approval',
                ],
                [
                    'label' => 'Failed Notifications (' . $periodLabel . ')',
                    'value' => Notification::where('status', 'failed')->whereBetween('created_at', [$from, $to])->count(),
                    'color' => 'red',
                    'icon'  => 'notification',
                ],
            ];
        } elseif ($role === 'Lab Technician') {
            $stats = [
                [
                    'label' => 'Work Queue',
                    'value' => LabOrder::whereIn('status', ['sample_collected', 'in_progress'])->count(),
                    'color' => 'indigo',
                    'icon'  => 'orders',
                ],
                [
                    'label' => 'Pending Results',
                    'value' => LabOrderTest::where('status', 'pending')->count(),
                    'color' => 'amber',
                    'icon'  => 'pending',
                ],
                [
                    'label' => 'Awaiting Verification',
                    'value' => LabOrderTest::where('status', 'entered')->count(),
                    'color' => 'blue',
                    'icon'  => 'verify',
                ],
                [
                    'label' => 'Completed (' . $periodLabel . ')',
                    'value' => LabOrder::where('status', 'approved')
                        ->whereBetween('approved_at', [$from, $to])
                        ->count(),
                    'color' => 'emerald',
                    'icon'  => 'done',
                ],
            ];
        }

        $readyForCollection = [];
        if (in_array($role, ['Admin', 'Receptionist'])) {
            $readyForCollection = LabOrder::with('patient')
                ->whereIn('status', ['approved', 'on_hold'])
                ->whereBetween('updated_at', [$from, $to])
                ->orderByDesc('updated_at')
                ->limit(10)
                ->get();
        }

        return view('dashboard', compact('stats', 'orderStats', 'recentOrders', 'role', 'period', 'periodLabel', 'readyForCollection'));
    }

    private function resolvePeriod(string $period): array
    {
        $now = now();

        if ($period === 'custom') {
            $from = request('date_from')
                ? \Carbon\Carbon::parse(request('date_from'))->startOfDay()
                : $now->copy()->startOfMonth();

            $to = request('date_to')
                ? \Carbon\Carbon::parse(request('date_to'))->endOfDay()
                : $now->copy()->endOfDay();

            // ensure from <= to
            if ($from->gt($to)) {
                [$from, $to] = [$to, $from];
            }

            $label = $from->format('d M Y') . ' – ' . $to->format('d M Y');

            return [$from, $to, $label];
        }

        return match ($period) {
            'yesterday'  => [
                $now->copy()->subDay()->startOfDay(),
                $now->copy()->subDay()->endOfDay(),
                'Yesterday',
            ],
            'last_week'  => [
                $now->copy()->subDays(6)->startOfDay(),
                $now->copy()->endOfDay(),
                'Last 7 Days',
            ],
            'this_month' => [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfDay(),
                'This Month',
            ],
            'last_month' => [
                $now->copy()->subMonth()->startOfMonth(),
                $now->copy()->subMonth()->endOfMonth(),
                'Last Month',
            ],
            'last_year'  => [
                $now->copy()->subYear()->startOfYear(),
                $now->copy()->subYear()->endOfYear(),
                'Last Year',
            ],
            default      => [  // today
                $now->copy()->startOfDay(),
                $now->copy()->endOfDay(),
                'Today',
            ],
        };
    }

    private function orderStatusBreakdown($from, $to): array
    {
        $counts = LabOrder::select('status', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $all = [
            'pending'          => ['label' => 'Pending',          'color' => 'gray'],
            'sample_collected' => ['label' => 'Sample Collected',  'color' => 'blue'],
            'sample_rejected'  => ['label' => 'Sample Rejected',   'color' => 'red'],
            'in_progress'      => ['label' => 'In Progress',       'color' => 'amber'],
            'pending_review'   => ['label' => 'Pending Review',    'color' => 'violet'],
            'on_hold'          => ['label' => 'On Hold',            'color' => 'violet'],
            'approved'         => ['label' => 'Approved',          'color' => 'emerald'],
            'cancelled'        => ['label' => 'Cancelled',         'color' => 'rose'],
        ];

        $result = [];
        $grand  = array_sum($counts) ?: 1;

        foreach ($all as $key => $meta) {
            $count    = $counts[$key] ?? 0;
            $result[] = [
                'key'     => $key,
                'label'   => $meta['label'],
                'color'   => $meta['color'],
                'count'   => $count,
                'percent' => round($count / $grand * 100),
            ];
        }

        return $result;
    }

    private function recentOrders(string $role, $from, $to): \Illuminate\Support\Collection
    {
        $query = LabOrder::with('patient')
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('created_at')
            ->limit(8);

        if ($role === 'Lab Technician') {
            $query->whereIn('status', ['sample_collected', 'in_progress', 'pending_review']);
        }

        return $query->get();
    }
}
