<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{

    use HasFactory;

    protected $fillable = [
        'patient_code',
        'full_name',
        'dob',
        'gender',
        'phone',
        'email',
        'address',
        'created_by',
    ];

    protected static function booted()
    {
        static::creating(function ($patient) {

            if (!empty($patient->patient_code)) return;

            $year = now()->format('Y');

            $last = self::whereYear('created_at', now()->year)
                ->orderByDesc('id')
                ->value('patient_code');

            $nextNumber = 1;

            if ($last && preg_match('/PAT-\d{4}-(\d{5})/', $last, $m)) {
                $nextNumber = ((int) $m[1]) + 1;
            }

            $patient->patient_code = sprintf('PAT-%s-%05d', $year, $nextNumber);
        });
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}