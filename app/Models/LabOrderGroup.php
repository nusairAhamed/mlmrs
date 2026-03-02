<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LabOrderGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'lab_order_id',
        'test_group_id',
        'group_price_snapshot',
    ];

    protected $casts = [
        'group_price_snapshot' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(LabOrder::class, 'lab_order_id');
    }

    public function testGroup()
    {
        return $this->belongsTo(TestGroup::class);
    }

    public function tests()
    {
        return $this->hasMany(LabOrderTest::class, 'lab_order_group_id');
    }
}