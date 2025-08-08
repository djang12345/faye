<?php

namespace App\Http\Controllers;

use App\Mail\SubmittedApplication;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use App\Models\Applicant;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Models\ApplicantParents;
use Illuminate\Support\Facades\Redirect;

class ApplicationController extends Controller
{
    public function create()
    {
        return Inertia::render('Application/ApplicantForm');
    }

    public function validatePersonalInfo(Request $request)
    {
        $request->validate([
            'firstname' => 'required',
            'middlename' => 'nullable',
            'lastname' => 'required',
            'age' => 'required',
            'sex' => 'required',
            'birthdate' => 'required',
            'height' => 'required',
            'weight' => 'required',
            'status' => 'required',
            'citizenship' => 'required',
            'barangay' => 'required',
            'municipality' => 'required',
            'province' => 'required',
            'country' => 'required',
            'email' => 'required|email',
        ]);

        return Redirect::route('application.create');
    }

    public function validateParentsBackground(Request $request)
    {
        if (
            ($request->mother_firstname != null
                || $request->mother_middlename != null
                || $request->mother_lastname != null
                || $request->mother_occupation != null)
            && ($request->father_firstname != null
                || $request->father_middlename != null
                || $request->father_lastname != null
                || $request->father_occupation != null)
        ) {
            $request->validate([
                'mother_firstname' => 'required',
                'mother_middlename' => 'required',
                'mother_lastname' => 'required',
                'mother_occupation' => 'required',
                'father_firstname' => 'required',
                'father_middlename' => 'required',
                'father_lastname' => 'required',
                'father_occupation' => 'required',
            ]);
        } else if (
            $request->father_firstname != null
            || $request->father_middlename != null
            || $request->father_lastname != null
            || $request->father_occupation != null
        ) {
            $request->validate([
                'father_firstname' => 'required',
                'father_middlename' => 'required',
                'father_lastname' => 'required',
                'father_occupation' => 'required',
            ]);
        } else if (
            $request->mother_firstname != null
            || $request->mother_middlename != null
            || $request->mother_lastname != null
            || $request->mother_occupation != null
        ) {
            $request->validate([
                'mother_firstname' => 'required',
                'mother_middlename' => 'required',
                'mother_lastname' => 'required',
                'mother_occupation' => 'required',
            ]);
        }


        return Redirect::route('application.create');
    }

    public function submit(Request $request)
    {

        if (
            ($request->mother_firstname != null
                || $request->mother_middlename != null
                || $request->mother_lastname != null
                || $request->mother_occupation != null)
            && ($request->father_firstname != null
                || $request->father_middlename != null
                || $request->father_lastname != null
                || $request->father_occupation != null)
        ) {
            $parent = ApplicantParents::create([
                'mother_firstname' => $request->mother_firstname,
                'mother_middlename' => $request->mother_middlename,
                'mother_lastname' => $request->mother_lastname,
                'mother_occupation' => $request->mother_occupation,
                'father_firstname' => $request->father_firstname,
                'father_middlename' => $request->father_middlename,
                'father_lastname' => $request->father_lastname,
                'father_occupation' => $request->father_occupation,
            ]);


        } elseif (
            $request->father_firstname != null
            || $request->father_middlename != null
            || $request->father_lastname != null
            || $request->father_occupation != null
        ) {
            $parent = ApplicantParents::create([
                'father_firstname' => $request->father_firstname,
                'father_middlename' => $request->father_middlename,
                'father_lastname' => $request->father_lastname,
                'father_occupation' => $request->father_occupation,
            ]);

        } else if (
            $request->mother_firstname != null
            || $request->mother_middlename != null
            || $request->mother_lastname != null
            || $request->mother_occupation != null
        ) {
            $parent = ApplicantParents::create([
                'mother_firstname' => $request->mother_firstname,
                'mother_middlename' => $request->mother_middlename,
                'mother_lastname' => $request->mother_lastname,
                'mother_occupation' => $request->mother_occupation,

            ]);
        }
        $applicant = Applicant::create([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'age' => $request->age,
            'sex' => $request->sex,
            'birthdate' => $request->birthdate,
            'height' => $request->height,
            'weight' => $request->weight,
            'status' => $request->status,
            'citizenship' => $request->citizenship,
            'barangay' => $request->barangay,
            'municipality' => $request->municipality,
            'province' => $request->province,
            'country' => $request->country,
            'email' => $request->email,
            'parents_id' => $parent->id ?? null,
        ]);

        Application::create([
            'applicant_id' => $applicant->id,
            'status' => 'PENDING',
        ]);

        Mail::to($applicant->email)->send(new SubmittedApplication($applicant));

        return Redirect::route('application.create');
    }
}
