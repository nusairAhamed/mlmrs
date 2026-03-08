<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrToken extends Model
{
    protected $fillable = [
        'lab_order_id',
        'token',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function labOrder()
    {
        return $this->belongsTo(LabOrder::class);
    }
}