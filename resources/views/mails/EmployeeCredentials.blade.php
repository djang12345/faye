<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to DPS - Employee Credentials</title>
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

        .credentials-box {
            background-color: #e8f5e8;
            border: 1px solid #4caf50;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .credentials-list {
            background-color: white;
            padding: 15px;
            border-radius: 3px;
            margin: 10px 0;
        }

        .warning-box {
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
        <div class="header">🎉 Welcome to DPS!</div>
        
        <p>Dear {{ $user->name }},</p>
        
        <p>Welcome to the <strong>DPS</strong> team! We're excited to have you on board.</p>
        
        <p>Your employee account has been successfully created. Here are your login credentials:</p>

        <div class="credentials-box">
            <h3 style="color: #2e7d32; margin-top: 0;">🔐 Your Employee Account Credentials</h3>
            
            <div class="credentials-list">
                <p><strong>📧 Email:</strong> {{ $user->email }}</p>
                <p><strong>🔑 Password:</strong> {{ $password }}</p>
                <p><strong>🌐 Login URL:</strong> <a href="{{ url('/login') }}">{{ url('/login') }}</a></p>
            </div>
        </div>

        <div class="warning-box">
            <p style="color: #856404; margin: 0;"><strong>⚠️ Important Security Notice:</strong></p>
            <ul style="color: #856404;">
                <li>Please change your password immediately after your first login</li>
                <li>Keep your credentials secure and do not share them with anyone</li>
                <li>Contact IT support if you have any issues accessing your account</li>
                <li>This is a temporary password - change it for security</li>
            </ul>
        </div>

        <h3>🚀 Getting Started</h3>
        <p>Once you log in, you'll have access to:</p>
        <ul>
            <li><strong>Dashboard</strong> - View your work summary and statistics</li>
            <li><strong>Attendance</strong> - Clock in/out and view your attendance records</li>
            <li><strong>Payroll</strong> - View your salary and payment information</li>
            <li><strong>Leave Management</strong> - Submit and track leave requests</li>
        </ul>

        <p><strong>Next Steps:</strong></p>
        <ol>
            <li>Log in using the credentials above</li>
            <li>Change your password in the profile settings</li>
            <li>Complete your profile information</li>
            <li>Familiarize yourself with the system features</li>
        </ol>

        <p>If you have any questions or need assistance, please don't hesitate to contact the HR department.</p>

        <p>Welcome aboard and we look forward to working with you!</p>

        <div class="footer">
            <p>Best regards,<br>
            <strong>DPS Human Resources Team</strong></p>
            
            <p style="font-size: 12px; color: #777;">
                This is an automated message. Please do not reply to this email.
                For support, contact HR at hr@dps.gov.ph
            </p>
        </div>
    </div>
</body>

</html>
