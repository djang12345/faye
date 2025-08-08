<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submission Confirmation</title>
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
        }s

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">Application Submission Confirmation</div>

        <p>Dear, <?php echo e($applicant->firstname); ?> <?php echo e($applicant->lastname); ?></p>

        <p>Thank you for submitting your application. We are currently reviewing your information and will reach out to
            you shortly regarding the next steps in the process.</p>
        <p>We appreciate your interest in joining our team and look forward to connecting with you soon.</p>
        <div class="footer">
            Best regards,<br>
            DPS<br>
        </div>
    </div>

</body>

</html><?php /**PATH /home/lrjab/Downloads/dps_rework-main/resources/views/mails/SubmittedApplication.blade.php ENDPATH**/ ?>