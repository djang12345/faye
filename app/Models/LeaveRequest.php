<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $fillable = [
        'user_id',
        'leave_type',
        'start_date',
        'end_date',
        'days_requested',
        'details',
        'status',
        'admin_notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function leaveRule(): BelongsTo
    {
        return $this->belongsTo(LeaveRule::class, 'leave_type', 'leave_type');
    }

    // Get status badge class
    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            'pending' => 'badge bg-warning',
            'approved' => 'badge bg-success',
            'rejected' => 'badge bg-danger',
        ];

        return $classes[$this->status] ?? $classes['pending'];
    }

    // Get status display text
    public function getStatusDisplayAttribute()
    {
        return ucfirst($this->status);
    }

    // Get leave type display name
    public function getLeaveTypeDisplayAttribute()
    {
        if ($this->leaveRule) {
            return $this->leaveRule->display_name;
        }
        
        $displayNames = [
            'emergency' => 'Emergency Leave',
            'sick' => 'Sick Leave',
            'vacation' => 'Vacation Leave',
        ];

        return $displayNames[$this->leave_type] ?? $this->leave_type;
    }
}
