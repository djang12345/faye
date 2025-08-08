<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status Update</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
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
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">Update on Your Application Status</div>

        <p>Dear {{ $applicant->firstname }} {{ $applicant->lastname }},</p>

        <p>Thank you for your interest in joining the <strong>DPS</strong> and for taking the time to participate in our interview process.</p>

        <p>After careful consideration of your application and interview performance, we regret to inform you that we will not be moving forward with your application at this time.</p>

        @if($application->rejection_reason)
        <p><strong>Feedback:</strong> {{ $application->rejection_reason }}</p>
        @endif

        <p>We appreciate the time and effort you put into your application and encourage you to stay connected with us for future opportunities that may align with your skills and aspirations.</p>

        <p>We wish you the very best in your future endeavors and thank you for considering <strong>DPS</strong> as a potential employer.</p>

        <div class="footer">
            Best regards,<br>
            <strong>DPS</strong><br>
        </div>
    </div>

</body>

</html>