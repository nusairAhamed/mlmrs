<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'patient_id',
        'lab_order_id',
        'channel',
        'status',
        'message',
        'provider_response',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function labOrder()
    {
        return $this->belongsTo(LabOrder::class);
    }
}