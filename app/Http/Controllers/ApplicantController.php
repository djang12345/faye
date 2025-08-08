<?php

namespace App\Http\Controllers;

use App\Mail\AcceptApplicant;
use App\Mail\RejectApplicant;
use App\Mail\InterviewScheduled;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use App\Models\Applicant;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ApplicantController extends Controller
{
    public function indexPending(Request $request)
    {
        $applicants = Application::where('status', Application::STATUS_PENDING)->when(
            $request->search,
            function ($query) use ($request) {
                $query->whereHas('applicant', function ($query) use ($request) {
                    $query->where('firstname', 'like', '%' . $request->search . '%')
                        ->orWhere('middlename', 'like', '%' . $request->search . '%')
                        ->orWhere('lastname', 'like', '%' . $request->search . '%');
                });
            }
        )->with('applicant')->paginate(10);
        return Inertia::render('Applicant/Index', [
            'applicants' => $applicants,
        ]);
    }

    public function indexInterviewScheduled(Request $request)
    {
        $applicants = Application::where('status', Application::STATUS_INTERVIEW_SCHEDULED)->when(
            $request->search,
            function ($query) use ($request) {
                $query->whereHas('applicant', function ($query) use ($request) {
                    $query->where('firstname', 'like', '%' . $request->search . '%')
                        ->orWhere('middlename', 'like', '%' . $request->search . '%')
                        ->orWhere('lastname', 'like', '%' . $request->search . '%');
                });
            }
        )->with('applicant')->paginate(10);
        return Inertia::render('Applicant/Index', [
            'applicants' => $applicants,
        ]);
    }

    public function indexInterviewed(Request $request)
    {
        $applicants = Application::where('status', Application::STATUS_INTERVIEWED)->when(
            $request->search,
            function ($query) use ($request) {
                $query->whereHas('applicant', function ($query) use ($request) {
                    $query->where('firstname', 'like', '%' . $request->search . '%')
                        ->orWhere('middlename', 'like', '%' . $request->search . '%')
                        ->orWhere('lastname', 'like', '%' . $request->search . '%');
                });
            }
        )->with('applicant')->paginate(10);
        return Inertia::render('Applicant/Index', [
            'applicants' => $applicants,
        ]);
    }

    public function indexAccepted(Request $request)
    {
        $applicants = Application::where('status', Application::STATUS_ACCEPTED)->when(
            $request->search,
            function ($query) use ($request) {
                $query->whereHas('applicant', function ($query) use ($request) {
                    $query->where('firstname', 'like', '%' . $request->search . '%')
                        ->orWhere('middlename', 'like', '%' . $request->search . '%')
                        ->orWhere('lastname', 'like', '%' . $request->search . '%');
                });
            }
        )->with('applicant')->paginate(10);
        return Inertia::render('Applicant/Index', [
            'applicants' => $applicants,
        ]);
    }

    public function indexRejected(Request $request)
    {
        $applicants = Application::where('status', Application::STATUS_REJECTED)->when(
            $request->search,
            function ($query) use ($request) {
                $query->whereHas('applicant', function ($query) use ($request) {
                    $query->where('firstname', 'like', '%' . $request->search . '%')
                        ->orWhere('middlename', 'like', '%' . $request->search . '%')
                        ->orWhere('lastname', 'like', '%' . $request->search . '%');
                });
            }
        )->with('applicant')->paginate(10);
        return Inertia::render('Applicant/Index', [
            'applicants' => $applicants,
        ]);
    }

    public function view($id) {
        $applicant = Applicant::with(['parents', 'application', 'user'])->find($id);
        return Inertia::render('Applicant/View', [
            'applicant' => $applicant,
        ]);
    }

    public function scheduleInterview($id, Request $request) {
        $request->validate([
            'interview_date' => 'required|date|after:today',
            'interview_time' => 'required|date_format:H:i',
            'interview_location' => 'required|string|max:255',
            'interviewer_name' => 'required|string|max:255',
        ]);

        $applicant = Applicant::find($id);
        $application = Application::where('applicant_id', $id)->first();
        
        // Update application with interview details
        $application->update([
            'status' => Application::STATUS_INTERVIEW_SCHEDULED,
            'interview_date' => $request->interview_date,
            'interview_time' => $request->interview_time,
            'interview_location' => $request->interview_location,
            'interviewer_name' => $request->interviewer_name,
        ]);

        // Send interview invitation email
        Mail::to($applicant->email)->send(new InterviewScheduled($applicant, $application));
        
        return Redirect::back()->with('success', 'Interview scheduled successfully. Email sent to applicant.');
    }

    public function conductInterview($id, Request $request) {
        $request->validate([
            'interview_result' => 'required|in:PASSED,FAILED',
            'interview_score' => 'required|integer|min:1|max:100',
            'interview_notes' => 'required|string|max:1000',
        ]);

        $applicant = Applicant::find($id);
        $application = Application::where('applicant_id', $id)->first();
        
        // Update application with interview results
        $application->update([
            'status' => Application::STATUS_INTERVIEWED,
            'interview_result' => $request->interview_result,
            'interview_score' => $request->interview_score,
            'interview_notes' => $request->interview_notes,
            'interview_conducted_at' => now(),
        ]);

        return Redirect::back()->with('success', 'Interview results recorded successfully.');
    }

    public function accept($id, Request $request) {
        \Log::info('Accept applicant request received', [
            'applicant_id' => $id,
            'request_data' => $request->all()
        ]);

        $request->validate([
            'interview_date' => 'required|date|after:today',
            'interview_time' => 'required|date_format:H:i',
        ]);

        $applicant = Applicant::find($id);
        $application = Application::where('applicant_id', $id)->first();
        
        if (!$applicant || !$application) {
            \Log::error('Applicant or application not found', ['applicant_id' => $id]);
            return Redirect::back()->withErrors(['error' => 'Applicant or application not found.']);
        }

        try {
            // Update application with interview details and status
            $application->update([
                'status' => Application::STATUS_ACCEPTED,
                'interview_date' => $request->interview_date,
                'interview_time' => $request->interview_time,
            ]);

            \Log::info('Application updated successfully', [
                'application_id' => $application->id,
                'status' => $application->status,
                'interview_date' => $request->interview_date,
                'interview_time' => $request->interview_time,
            ]);

            // Send acceptance email with interview schedule only
            Mail::to($applicant->email)->send(new AcceptApplicant($applicant, $application));
            
            \Log::info('Acceptance email sent successfully', ['email' => $applicant->email]);
            
            return Redirect::back()->with('success', 'Applicant accepted and interview scheduled successfully.');
        } catch (\Exception $e) {
            \Log::error('Error accepting applicant', [
                'applicant_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Redirect::back()->withErrors(['error' => 'An error occurred while processing the request.']);
        }
    }

    public function reject($id, Request $request) {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $applicant = Applicant::find($id);
        $application = Application::where('applicant_id', $id)->first();
        
        $application->update([
            'status' => Application::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
        ]);

        Mail::to($applicant->email)->send(new RejectApplicant($applicant, $application));
        return Redirect::back()->with('success', 'Applicant rejected successfully.');
    }

    public function rejectAfterInterview($id, Request $request) {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $applicant = Applicant::find($id);
        $application = Application::where('applicant_id', $id)->first();
        
        if (!$applicant || !$application) {
            return Redirect::back()->withErrors(['error' => 'Applicant or application not found.']);
        }

        // Update application status to rejected
        $application->update([
            'status' => Application::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Send polite rejection email
        Mail::to($applicant->email)->send(new RejectApplicant($applicant, $application));
        
        return Redirect::back()->with('success', 'Applicant rejected after interview. Polite rejection email sent.');
    }


}
