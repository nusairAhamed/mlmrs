<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Patient;
use App\Models\User;

class LabOrder extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'patient_id',
        'created_by',
        'order_number',
        'status',
        'total_amount',
        'notes',
        'completed_at',
        'approved_by',
        'approved_at',
    ];

    protected $auditInclude = [
        'patient_id',
        'status',
        'total_amount',
        'notes',
        'approved_by',
        'approved_at',
    ];

    protected $auditEvents = ['created', 'updated', 'deleted'];

    public function transformAudit(array $data): array
    {
        // Suppress the internal 0→amount update that fires right after creation
        if (
            $data['event'] === 'updated' &&
            array_keys($data['new_values'] ?? []) === ['total_amount'] &&
            ($data['old_values']['total_amount'] ?? null) == 0
        ) {
            $data['new_values'] = [];
            $data['old_values'] = [];
        }

        // Resolve patient_id → patient_name as an immutable snapshot
        foreach (['new_values', 'old_values'] as $key) {
            if (!empty($data[$key]['patient_id'])) {
                $patient = Patient::find($data[$key]['patient_id']);
                $data[$key]['patient_name'] = $patient?->full_name ?? 'Unknown';
                unset($data[$key]['patient_id']);
            }
        }

        // Resolve approved_by → approver name as an immutable snapshot
        foreach (['new_values', 'old_values'] as $key) {
            if (!empty($data[$key]['approved_by'])) {
                $user = User::find($data[$key]['approved_by']);
                $data[$key]['approved_by_name'] = $user?->name ?? 'Unknown';
                unset($data[$key]['approved_by']);
            }
        }

        return $data;
    }

    protected $casts = [
        'total_amount' => 'decimal:2',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function groups()
    {
        return $this->hasMany(LabOrderGroup::class);
    }

    public function tests()
    {
        return $this->hasMany(LabOrderTest::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function samples()
    {
        return $this->hasMany(\App\Models\LabSample::class);
    }
    public function qrToken()
    {
        return $this->hasOne(QrToken::class);
    }
    public function notifications()
{
    return $this->hasMany(Notification::class);
}
}