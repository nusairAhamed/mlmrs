<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LabOrder extends Model
{
    use HasFactory;

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
}