<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffNotification extends Model
{
    protected $fillable = [
        'target_role',
        'lab_order_id',
        'type',
        'title',
        'message',
    ];

    public function labOrder()
    {
        return $this->belongsTo(LabOrder::class);
    }

    public function reads()
    {
        return $this->hasMany(StaffNotificationRead::class);
    }

    public function isReadBy(int $userId): bool
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }

    /**
     * Scope to notifications visible to a given role.
     */
    public function scopeForRole($query, string $role)
    {
        return $query->whereIn('target_role', [$role, 'All']);
    }

    /**
     * Scope to notifications not yet read by a given user.
     */
    public function scopeUnreadBy($query, int $userId)
    {
        return $query->whereDoesntHave('reads', fn ($q) => $q->where('user_id', $userId));
    }
}
