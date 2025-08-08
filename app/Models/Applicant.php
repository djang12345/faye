<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    protected $fillable = [
        'firstname',
        'middlename',
        'lastname',
        'age',
        'sex',
        'birthdate',
        'height',
        'weight',
        'status',
        'citizenship',
        'barangay',
        'municipality',
        'province',
        'country',
        'email',
        'parents_id',
        'user_id',
    ];

    public function parents()
    {
        return $this->belongsTo(ApplicantParents::class, 'parents_id');
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'id', 'applicant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
