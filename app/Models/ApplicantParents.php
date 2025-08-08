<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicantParents extends Model
{
    protected $table = 'parents';

    protected $fillable = [
        'mother_firstname',
        'mother_middlename',
        'mother_lastname',
        'mother_occupation',
        'father_firstname',
        'father_middlename',
        'father_lastname',
        'father_occupation',
    ];
}
