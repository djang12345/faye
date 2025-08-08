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
        <div class="header">Congratulations on Your Application!</div>
        <p>Dear <?php echo e($applicant->firstname); ?> <?php echo e($applicant->lastname); ?>,</p>
        <p>We are pleased to inform you that your application at <strong>DPS</strong> has been accepted!</p>
        <p>Your qualifications and experiences stood out to us, and we are excited to welcome you to our team.</p>

        <p><strong>Interview Schedule:</strong></p>
        <p>
            Date: <strong><?php echo e(Carbon\Carbon::parse($application->interview_date)->format('F j, Y')); ?></strong><br>
            Time: <strong><?php echo e(Carbon\Carbon::parse($application->interview_time)->format('g:i A')); ?></strong>
        </p>

        <p>Further details regarding the next steps in the onboarding process will be sent to you shortly.</p>
        <p>Thank you for your interest in joining <strong>DPS</strong>. We look forward to working together!</p>

        <div class="footer">
            Best regards,<br>
            DPS<br>
        </div>
    </div>
</body>

</html><?php /**PATH /home/lrjab/Downloads/dps_rework-main/resources/views/mails/AcceptApplicant.blade.php ENDPATH**/ ?>