<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Invitation - DPS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            color: #333;
        }

        .container {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .interview-details {
            background-color: #e8f4fd;
            border: 1px solid #3498db;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .important-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 3px;
            margin: 15px 0;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">📅 Interview Invitation</div>
        
        <p>Dear {{ $applicant->firstname }} {{ $applicant->lastname }},</p>
        
        <p>Thank you for your interest in joining the <strong>DPS</strong> team!</p>
        
        <p>We are pleased to inform you that your application has been reviewed and we would like to invite you for an interview.</p>

        <div class="interview-details">
            <h3 style="color: #2c7be5; margin-top: 0;">📋 Interview Details</h3>
            
            <div style="background-color: white; padding: 15px; border-radius: 3px;">
                <p><strong>📅 Date:</strong> {{ Carbon\Carbon::parse($application->interview_date)->format('F j, Y') }}</p>
                <p><strong>⏰ Time:</strong> {{ Carbon\Carbon::parse($application->interview_time)->format('g:i A') }}</p>
                <p><strong>📍 Location:</strong> {{ $application->interview_location }}</p>
                <p><strong>👤 Interviewer:</strong> {{ $application->interviewer_name }}</p>
            </div>
        </div>

        <div class="important-notice">
            <p style="color: #856404; margin: 0;"><strong>⚠️ Important Reminders:</strong></p>
            <ul style="color: #856404;">
                <li>Please arrive 10-15 minutes before your scheduled interview time</li>
                <li>Bring a valid government-issued ID</li>
                <li>Dress professionally and appropriately</li>
                <li>Prepare to discuss your qualifications and experience</li>
                <li>Bring copies of your resume and relevant certificates</li>
            </ul>
        </div>

        <h3>🎯 What to Expect</h3>
        <p>The interview will cover:</p>
        <ul>
            <li>Review of your application and qualifications</li>
            <li>Discussion of the position requirements</li>
            <li>Questions about your experience and skills</li>
            <li>Opportunity for you to ask questions about the role</li>
        </ul>

        <h3>📞 Contact Information</h3>
        <p>If you need to reschedule or have any questions, please contact us immediately:</p>
        <ul>
            <li><strong>Email:</strong> hr@dps.gov.ph</li>
            <li><strong>Phone:</strong> (02) 123-4567</li>
            <li><strong>Office Hours:</strong> Monday to Friday, 8:00 AM - 5:00 PM</li>
        </ul>

        <p>We look forward to meeting you and learning more about how you can contribute to our team!</p>

        <div class="footer">
            <p>Best regards,<br>
            <strong>DPS Human Resources Team</strong></p>
            
            <p style="font-size: 12px; color: #777;">
                This is an automated message. For urgent matters, please contact us directly.
            </p>
        </div>
    </div>
</body>

</html>
