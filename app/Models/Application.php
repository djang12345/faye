<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'applicant_id',
        'status',
        'interview_date',
        'interview_time',
        'interview_location',
        'interview_notes',
        'interview_result',
        'interview_score',
        'interviewer_name',
        'interview_conducted_at',
        'rejection_reason',
    ];

    protected $casts = [
        'interview_date' => 'date',
        'interview_time' => 'datetime',
        'interview_conducted_at' => 'datetime',
        'interview_score' => 'integer',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }

    // Status constants
    const STATUS_PENDING = 'PENDING';
    const STATUS_INTERVIEW_SCHEDULED = 'INTERVIEW_SCHEDULED';
    const STATUS_INTERVIEWED = 'INTERVIEWED';
    const STATUS_ACCEPTED = 'ACCEPTED';
    const STATUS_REJECTED = 'REJECTED';

    // Interview result constants
    const RESULT_PASSED = 'PASSED';
    const RESULT_FAILED = 'FAILED';
    const RESULT_PENDING = 'PENDING';

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'badge bg-secondary',
            self::STATUS_INTERVIEW_SCHEDULED => 'badge bg-info',
            self::STATUS_INTERVIEWED => 'badge bg-warning',
            self::STATUS_ACCEPTED => 'badge bg-success',
            self::STATUS_REJECTED => 'badge bg-danger',
            default => 'badge bg-secondary',
        };
    }

    public function getStatusText()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending Review',
            self::STATUS_INTERVIEW_SCHEDULED => 'Interview Scheduled',
            self::STATUS_INTERVIEWED => 'Interviewed',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Unknown',
        };
    }

    public function getInterviewResultBadgeClass()
    {
        return match($this->interview_result) {
            self::RESULT_PASSED => 'badge bg-success',
            self::RESULT_FAILED => 'badge bg-danger',
            self::RESULT_PENDING => 'badge bg-warning',
            default => 'badge bg-secondary',
        };
    }
}
