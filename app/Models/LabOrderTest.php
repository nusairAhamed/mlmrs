<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LabOrderTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'lab_order_id',
        'lab_order_group_id',
        'test_id',
        'test_name',
        'unit',
        'test_reference_range_id',
        'ref_min',
        'ref_max',
        'result_value',
        'is_abnormal',
        'status',
        'entered_by',
        'entered_at',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'is_abnormal' => 'boolean',
        'ref_min' => 'decimal:4',
        'ref_max' => 'decimal:4',
        'entered_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(LabOrder::class, 'lab_order_id');
    }

    public function orderGroup()
    {
        return $this->belongsTo(LabOrderGroup::class, 'lab_order_group_id');
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}