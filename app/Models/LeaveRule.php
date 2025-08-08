<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveRule extends Model
{
    protected $fillable = [
        'leave_type',
        'display_name',
        'default_credits',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'leave_type', 'leave_type');
    }

    // Get available credits for a user
    public function getAvailableCredits($userId)
    {
        $usedCredits = LeaveRequest::where('user_id', $userId)
            ->where('leave_type', $this->leave_type)
            ->where('status', 'approved')
            ->sum('days_requested');

        return $this->default_credits - $usedCredits;
    }
}
