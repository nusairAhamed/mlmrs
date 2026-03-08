<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Notification::query()
                ->with(['patient', 'labOrder'])
                ->select('notifications.*');

            if ($request->filled('channel')) {
                $query->where('channel', $request->channel);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->addColumn('patient_name', function ($notification) {
                    $code = $notification->patient?->patient_code ?? '-';
                    $name = $notification->patient?->full_name ?? '-';
                    return $code . ' — ' . $name;
                })
                ->addColumn('order_number', function ($notification) {
                    return $notification->labOrder?->order_number ?? '-';
                })
                ->addColumn('channel_badge', function ($notification) {
                    $class = match ($notification->channel) {
                        'sms' => 'bg-blue-50 text-blue-700 ring-blue-200',
                        'email' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
                        default => 'bg-gray-50 text-gray-700 ring-gray-200',
                    };

                    return '<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 ' . $class . '">'
                        . strtoupper($notification->channel) .
                        '</span>';
                })
                ->addColumn('status_badge', function ($notification) {
                    $class = match ($notification->status) {
                        'sent' => 'bg-green-50 text-green-700 ring-green-200',
                        'failed' => 'bg-red-50 text-red-700 ring-red-200',
                        default => 'bg-yellow-50 text-yellow-700 ring-yellow-200',
                    };

                    return '<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 ' . $class . '">'
                        . ucfirst($notification->status) .
                        '</span>';
                })
                ->addColumn('action', function ($notification) {

                    if ($notification->status === 'failed') {
                        return '
                        <form method="POST" action="'.route('notifications.retry', $notification).'">
                            '.csrf_field().'
                            <button class="text-xs bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                                Retry
                            </button>
                        </form>';
                    }

                    return '-';
                })
                ->editColumn('message', function ($notification) {
                    return \Illuminate\Support\Str::limit($notification->message, 80);
                })
                ->editColumn('sent_at', function ($notification) {
                    return $notification->sent_at
                        ? $notification->sent_at->format('Y-m-d h:i A')
                        : '-';
                })
                ->editColumn('created_at', function ($notification) {
                    return $notification->created_at
                        ? $notification->created_at->format('Y-m-d h:i A')
                        : '-';
                })
                ->rawColumns(['channel_badge', 'status_badge'])
                ->make(true);
        }

        return view('pages.notifications.index');
    }


    public function retry(Notification $notification)
{
    if ($notification->status !== 'failed') {
        return back()->with('error', 'Only failed notifications can be retried.');
    }

    $patient = $notification->patient;
    $labOrder = $notification->labOrder;

    try {

        if ($notification->channel === 'email') {

            $publicUrl = $labOrder->qrToken
                ? route('public-reports.show', $labOrder->qrToken->token)
                : null;

            Mail::to($patient->email)->send(
                new ReportReadyMail(
                    $patient,
                    $labOrder->order_number,
                    $publicUrl
                )
            );
        }

        // future SMS implementation could go here

        $notification->update([
            'status' => 'sent',
            'sent_at' => now(),
            'provider_response' => 'Retry successful',
        ]);

    } catch (\Exception $e) {

        $notification->update([
            'provider_response' => $e->getMessage(),
        ]);

        return back()->with('error', 'Retry failed.');
    }

    return back()->with('success', 'Notification resent successfully.');
}
}